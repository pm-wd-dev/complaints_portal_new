<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Attachment;
use App\Models\CaseSignature;
use App\Models\Stage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ComplaintSubmittedNotification;
use App\Mail\AdminComplaintNotification;
use App\Models\StageChangeLog;
use App\Models\GuestOtp;
use App\Mail\GuestOtpNotification;

class GuestComplaintController extends Controller
{
    public function create(Request $request)
    {
        $location = null;
        if ($request->has('location')) {
            $location = \App\Models\Location::find($request->location);
        }
        return view('public.complaint', compact('location'));
    }

    public function store(Request $request)
    {
        // Define base validation rules
        $rules = [
            'submitted_as' => 'required|in:cast_member,guest',
            'is_anonymous' => 'sometimes|boolean',
            'issue_type' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'additional_attachments.*' => 'nullable|file|max:51200', // 50MB max for each file
            'date_of_experience' => 'required|date|before_or_equal:today',
            'complaint_about' => 'required|string|max:255',
            'complainee_name' => 'nullable|string|max:255',
            'complainee_email' => 'nullable|email|max:255',
            'complainee_address' => 'nullable|string|max:1000',
            'witnesses' => 'nullable|string|max:1000',
            'evidence_type' => 'required|in:photo_screenshot,videos,messages_emails,other_documents,no_evidence',
            'evidence_description' => 'nullable|string|max:1000',
        ];

        // Handle conditional validation based on anonymous status
        if ($request->has('is_anonymous') && $request->is_anonymous) {
            // For anonymous complaints, contact method is required
            $rules['name'] = 'nullable|string|max:255';
            $rules['contact_method'] = 'required|in:email,phone';
            
            // Validate contact method selection first
            $request->validate(array_intersect_key($rules, ['contact_method' => true]));
            
            if ($request->contact_method === 'email') {
                $rules['email'] = 'required|email|max:255';
                $rules['phone_number'] = 'nullable|string|max:20|regex:/^\+?[0-9\s\-()]{7,20}$/';
            } else if ($request->contact_method === 'phone') {
                $rules['email'] = 'nullable|email|max:255';
                $rules['phone_number'] = [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9\s\-()]{7,20}$/'
                ];
            }
        } else {
            // For non-anonymous complaints
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
            $rules['phone_number'] = [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-()]{7,20}$/'
            ];
        }

        // Additional validation for evidence
        if ($request->evidence_type !== 'no_evidence') {
            $rules['evidence_description'] = 'required|string|max:1000';
        }

        $validated = $request->validate($rules);

        try {
            \DB::beginTransaction();

            // Determine case number prefix based on submitted_as
            $prefix = $validated['submitted_as'] === 'cast_member' ? 'COMPC-' : 'COMPG-';
            $initialStage = Stage::where('step_number', 1)->first();
            // Create and save the complaint
            $complaint = Complaint::create([
                'case_number' => $prefix . strtoupper(Str::random(8)),
                'submitted_as' => $validated['submitted_as'],
                'is_anonymous' => $request->has('is_anonymous') && $request->is_anonymous,
                'name' => $request->has('is_anonymous') && $request->is_anonymous ? null : $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'description' => $validated['description'],
                'submitted_by_admin' => false,
                'location' => $validated['location'],
                'complaint_type' => $validated['issue_type'],
                'complaint_about' => $validated['complaint_about'],
                'complainee_name' => $validated['complainee_name'] ?? null,
                'complainee_email' => $validated['complainee_email'] ?? null,
                'complainee_address' => $validated['complainee_address'] ?? null,
                'witnesses' => $validated['witnesses'] ?? null,
                'evidence_type' => $validated['evidence_type'],
                'evidence_description' => $validated['evidence_description'] ?? null,
                'status' => $initialStage->name ?? 'submitted',
                'stage_id' => $initialStage->id ?? null,
                'submitted_at' => $validated['date_of_experience'] . ' ' . now()->format('H:i:s'),
            ]);

            // Handle file attachments
            if ($request->hasFile('additional_attachments')) {
                foreach ($request->file('additional_attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();

                        // Create complaint-images directory if it doesn't exist
                        $path = public_path('images/complaint-images');
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }

                        // Move file to public/images/complaint-images
                        $file->move($path, $filename);
                        $filePath = 'images/complaint-images/' . $filename;

                        // Create attachment record
                        Attachment::create([
                            'complaint_id' => $complaint->id,
                            'uploaded_by' => null, // admin's ID for admin uploads
                            'file_path' => $filePath,
                            'file_type' => $file->getClientOriginalExtension(),
                            'description' => 'Evidence file'
                        ]);
                }
            }

            if (!$complaint) {
                throw new \Exception('Failed to save complaint');
            }

            \DB::commit();
            
            // Log initial complaint submission
            StageChangeLog::logChange(
                $complaint->id,
                null,
                $initialStage->id,
                'complaint_submitted',
                'Initial complaint submitted by ' . ($complaint->name ?: 'Anonymous'),
                [
                    'submission_type' => $complaint->submitted_as,
                    'is_anonymous' => $complaint->is_anonymous,
                    'complaint_type' => $complaint->complaint_type,
                    'location' => $complaint->location
                ]
            );

            // Load attachments for email notifications
            $complaint->load('attachments');
            
            // Send email notifications
            try {
                // Send email to complainant if email is provided
                if (!empty($complaint->email)) {
                    Mail::to($complaint->email)->send(new ComplaintSubmittedNotification($complaint));
                }

                // Send email to admin users
                $adminUsers = User::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    Mail::to($admin->email)->send(new AdminComplaintNotification($complaint));
                }
            } catch (\Exception $e) {
                \Log::error('Error sending complaint notification emails: ' . $e->getMessage());
                // Don't fail the request if email sending fails
            }

            return redirect()->route('public.complaints.track-form')
                ->with([
                    'success' => true,
                    'case_number' => $complaint->case_number
                ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating complaint: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error submitting complaint. Please try again.');
        }
    }

    public function showTrackForm()
    {
        return view('public.track');
    }

    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'complaint_id' => 'required|exists:complaints,id',
            'email' => 'required|email'
        ]);

        try {
            $complaint = Complaint::findOrFail($request->complaint_id);
            $signature = CaseSignature::where('complaint_id', $complaint->id)
                                    ->whereNull('user_id')
                                    ->first();

            if (!$signature) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending signature request found.'
                ], 404);
            }

            // Create signatures directory if it doesn't exist
            $path = public_path('images/signatures');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Store signature image
            $file = $request->file('signature');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($path, $filename);

            // Set the relative path for database storage
            $filePath = 'images/signatures/' . $filename;

            // Update signature record
            $signature->signature_path = $filePath;
            $signature->signer_email = $request->email;
            $signature->signed_at = now();
            $signature->ip_address = $request->ip();
            $signature->save();

            // Check if all signatures are complete and generate PDF
            $this->checkAndGenerateResolutionPdf($complaint->id);

            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully',
                'path' => asset($filePath)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading signature: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkAndGenerateResolutionPdf($complaintId)
    {
        try {
            \Log::info('Starting resolution PDF generation check', ['complaint_id' => $complaintId]);

            $complaint = Complaint::with([
                'latestResolution.signatures.user',
                'respondents.user'
            ])->find($complaintId);
            \Log::info('Complaint loaded', [
                'case_number' => $complaint->case_number,
                'status' => $complaint->status,
                'resolution_text' => $complaint->latestResolution->resolution_text,
                'signatures_count' => $complaint->signatures->count(),
                'respondents_count' => $complaint->respondents->count()
            ]);

            // Get all required signatures for this complaint
            $requiredSignatures = collect($complaint->signatures->pluck('signer_role')->toArray() ?? ['complainant', 'respondent', 'leadership']);
            \Log::info('Required signatures', ['signers' => $requiredSignatures->toArray()]);

            // Update complaint status based on signatures
            $complaint->awaiting_signature = true;
            $complaint->save();

            $allSignaturesComplete = true;

            foreach ($requiredSignatures as $signer) {
                $signature = null;

                if ($signer === 'complainant') {
                    $signature = $complaint->signatures()->whereNull('user_id')->first();
                    \Log::info('Checking complainant signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                } elseif ($signer === 'respondent') {
                    $respondent = $complaint->respondents->first();
                    \Log::info('Checking respondent', [
                        'respondent_id' => $respondent ? $respondent->user_id : 'none'
                    ]);
                    $signature = $respondent ? $complaint->signatures()->where('user_id', $respondent->user_id)->first() : null;
                    \Log::info('Checking respondent signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                } elseif ($signer === 'leadership') {
                    $signature = $complaint->signatures()->whereExists(function ($query) {
                        $query->from('users')
                              ->whereColumn('users.id', 'case_signatures.user_id')
                              ->where('users.role', 'admin');
                    })->first();
                    \Log::info('Checking leadership signature', [
                        'found' => $signature ? 'yes' : 'no',
                        'has_path' => $signature && $signature->signature_path ? 'yes' : 'no'
                    ]);
                }

                if (!$signature || !$signature->signature_path) {
                    $allSignaturesComplete = false;
                    \Log::info('Missing required signature', ['signer' => $signer]);
                    break;
                }
            }

            // If all signatures are complete, generate the PDF
            \Log::info('Signature check complete', [
                'all_complete' => $allSignaturesComplete ? 'yes' : 'no',
                'complaint_id' => $complaintId
            ]);

            if ($allSignaturesComplete) {
                \Log::info('Starting PDF generation', [
                    'complaint_id' => $complaintId,
                    'resolution_text' => $complaint->latestResolution->resolution_text,
                    'signers' => $requiredSignatures->toArray()
                ]);

                $pdf = PDF::loadView('admin.pdfs.resolution', [
                    'complaint' => $complaint,
                    'resolution_text' => $complaint->latestResolution->resolution_text,
                    'signers' => $requiredSignatures->toArray(),
                    'generated_at' => now()->format('F d, Y'),
                    'template_type'=>$complaint->latestResolution->template_type,
                    'admin_name'=>User::where('role','admin')->first()->name
                ]);

                // Create resolutions directory if it doesn't exist
                $path = public_path('documents/resolutions');
                \Log::info('Checking resolutions directory', [
                    'path' => $path,
                    'exists' => file_exists($path) ? 'yes' : 'no'
                ]);

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                    \Log::info('Created resolutions directory', ['path' => $path]);
                }

                // Generate filename and save PDF
                $filename = 'resolution_' . $complaint->case_number . '_' . now()->format('Ymd_His') . '.pdf';
                $fullPath = public_path('documents/resolutions/' . $filename);

                try {
                    $pdf->save($fullPath);
                    \Log::info('PDF saved successfully', [
                        'filename' => $filename,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath) ? 'yes' : 'no',
                        'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to save PDF', [
                        'filename' => $filename,
                        'error' => $e->getMessage(),
                        'path' => $fullPath
                    ]);
                    throw $e;
                }

                // Update resolution record with PDF path
                $resolution = $complaint->resolutions()->latest()->first();
                \Log::info('Found resolution record', [
                    'found' => $resolution ? 'yes' : 'no',
                    'resolution_id' => $resolution ? $resolution->id : null
                ]);

                if ($resolution) {
                    $resolution->generated_pdf_path = 'documents/resolutions/' . $filename;
                    $resolution->save();
                    \Log::info('Updated resolution record with PDF path', [
                        'resolution_id' => $resolution->id,
                        'pdf_path' => $resolution->generated_pdf_path
                    ]);
                }

                // Update complaint status to show signatures are complete
                $complaint->awaiting_signature = false;
                $complaint->save();

                \Log::info('Resolution PDF generated successfully', [
                    'complaint_id' => $complaintId,
                    'filename' => $filename
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error generating resolution PDF: ' . $e->getMessage(), [
                'complaint_id' => $complaintId,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function track(Request $request)
    {

        $caseNumber = $request->input('case_number');

        if (!$caseNumber) {
            return redirect()->route('public.complaints.track-form')
                ->with('error', 'Please enter a case number.');
        }

        // $complaint = Complaint::where('case_number', $caseNumber)
        //     ->with([
        //         'attachments' => function($query) {
        //             $query->whereNull('complaint_response_id');
        //         },
        //         'respondents.user',
        //         'respondents' => function($query) {
        //             $query->with(['responses' => function($query) {
        //                 $query->orderBy('created_at', 'desc');
        //             }, 'responses.attachments']);
        //         }
        //     ])
        //     ->first();

        $complaint = Complaint::where('case_number', $caseNumber)
    ->with([
        'attachments' => function ($query) {
            $query->whereNull('complaint_response_id');
        },
        'respondents.user',
        'respondents.responses' => function ($query) {
            $query->orderBy('created_at', 'desc');
        },
        'respondents.responses.attachments',
        'replies.user',
        'replies.recipient'
    ])
    ->first();

        if (!$complaint) {
            return redirect()->route('public.complaints.track-form')
                ->with('error', 'No complaint found with this case number.');
        }

        return view('public.track', [
            'complaint' => $complaint,
            'statusLabels' => [
                'submitted' => 'Submitted',
                'under_review' => 'Under Review',
                'escalated' => 'Progress',
                'resolved' => 'Resolved',
                'closed' => 'Closed'
            ]
        ]);
    }

    public function viewComplaint($caseNumber)
    {
        $complaint = Complaint::where('case_number', $caseNumber)
            ->with([
                'attachments' => function ($query) {
                    $query->whereNull('complaint_response_id')
                          ->whereNull('respondent_response_id');
                },
                'respondents.user',
                'respondents.responses' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'respondents.responses.attachments',
                'stage',
                'replies.user',
                'replies.recipient'
            ])
            ->first();

        if (!$complaint) {
            return redirect()->route('public.complaints.track-form')
                ->with('error', 'No complaint found with this case number: ' . $caseNumber);
        }

        return view('public.track', [
            'complaint' => $complaint,
            'statusLabels' => [
                'submitted' => 'Submitted',
                'under_review' => 'Under Review',
                'escalated' => 'Progress',
                'resolved' => 'Resolved',
                'closed' => 'Closed'
            ],
            'directAccess' => true
        ]);
    }

    public function respondentAccess($caseNumber)
    {
        $complaint = Complaint::where('case_number', $caseNumber)
            ->with([
                'attachments' => function ($query) {
                    $query->whereNull('complaint_response_id')
                          ->whereNull('respondent_response_id');
                },
                'respondents.user',
                'respondents.responses' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'respondents.responses.attachments',
                'stage',
                'replies.user',
                'replies.recipient'
            ])
            ->first();

        if (!$complaint) {
            return redirect()->route('public.complaints.track-form')
                ->with('error', 'No complaint found with this case number: ' . $caseNumber);
        }

        // Create a public respondent view that shows the complaint details
        // and allows them to access the cast member login if they want to respond
        return view('public.respondent-access', [
            'complaint' => $complaint,
            'loginUrl' => config('app.url') . '/login/cast-member',
            'statusLabels' => [
                'submitted' => 'Submitted',
                'under_review' => 'Under Review',
                'escalated' => 'Progress',
                'resolved' => 'Resolved',
                'closed' => 'Closed'
            ]
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Check if there are any complaints with this email
            $complaintsExist = Complaint::where('email', $request->email)
                ->where('submitted_as', 'guest')
                ->exists();

            if (!$complaintsExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'No complaints found for this email address.'
                ], 404);
            }

            // Generate and send OTP
            $otpRecord = GuestOtp::generateOtp(
                $request->email,
                null,
                $request->ip()
            );

            // Send OTP email
            Mail::to($request->email)->send(new GuestOtpNotification($otpRecord));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email address.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending guest OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        try {
            // Find the OTP record
            $otpRecord = GuestOtp::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('is_verified', false)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code.'
                ], 400);
            }

            if ($otpRecord->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 400);
            }

            // Mark OTP as verified
            $otpRecord->markAsVerified();

            // Get all complaints for this email
            $complaints = Complaint::where('email', $request->email)
                ->where('submitted_as', 'guest')
                ->select([
                    'id', 'case_number', 'status', 'complaint_type', 
                    'location', 'description', 'created_at', 'awaiting_signature'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($complaint) {
                    $complaint->display_status = $complaint->awaiting_signature ? 'awaiting_signature' : $complaint->status;
                    return $complaint;
                });

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'complaints' => $complaints
            ]);

        } catch (\Exception $e) {
            \Log::error('Error verifying guest OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.'
            ], 500);
        }
    }

    public function getComplaintDetails($caseNumber)
    {
        try {
            $complaint = Complaint::where('case_number', $caseNumber)
                ->with([
                    'attachments' => function ($query) {
                        $query->whereNull('complaint_response_id')
                              ->whereNull('respondent_response_id');
                    },
                    'respondents.user',
                    'respondents.responses' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    },
                    'respondents.responses.attachments',
                    'stage'
                ])
                ->first();

            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complaint not found.'
                ], 404);
            }

            // Add display_status for frontend
            $complaint->display_status = $complaint->awaiting_signature ? 'awaiting_signature' : $complaint->status;

            return response()->json([
                'success' => true,
                'complaint' => $complaint
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching complaint details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading complaint details.'
            ], 500);
        }
    }
}
