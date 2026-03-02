<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintLawyer;
use App\Models\ComplaintResponse;
use App\Models\LawyerAccess;
use App\Models\LawyerResponseDetail;
use App\Models\Attachment;
use App\Models\StageChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\LawyerResponseNotification;

class LawyerController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $email = $request->get('email');
        return view('lawyer.login', compact('email'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'complaint_number' => 'required|string',
            'lawyer_email' => 'required|email',
        ]);

        // If no email provided, redirect back to login with error
        if (empty($validated['lawyer_email'])) {
            return back()->withErrors(['lawyer_email' => 'Email is required. Please use the link from your assignment email.']);
        }

        // Check if complaint exists
        $complaint = Complaint::where('case_number', $validated['complaint_number'])->first();

        if (!$complaint) {
            return back()->withErrors(['complaint_number' => 'Invalid complaint number.']);
        }

        // Find the specific lawyer by email who is assigned to this complaint
        $lawyer = ComplaintLawyer::where('complaint_id', $complaint->id)
            ->whereHas('user', function($query) use ($validated) {
                $query->where('email', $validated['lawyer_email']);
            })
            ->with('user')
            ->first();

        if (!$lawyer) {
            return back()->withErrors([
                'lawyer_email' => 'The email address is not assigned to this case. Please check your email and case number.'
            ]);
        }

        // Store complaint and lawyer info in session
        session([
            'lawyer_complaint_id' => $complaint->id,
            'lawyer_user_id' => $lawyer->user_id,
            'complaint_number' => $complaint->case_number,
            'lawyer_email' => $lawyer->user->email
        ]);

        return redirect()->route('lawyer.otp');
    }

    public function showOtpForm()
    {
        if (!session('lawyer_complaint_id')) {
            return redirect()->route('lawyer.login');
        }

        return view('lawyer.otp');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string|size:4',
        ]);

        // For now, accept 0000 as valid OTP
        if ($validated['otp'] !== '0000') {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        if (!session('lawyer_complaint_id')) {
            return redirect()->route('lawyer.login');
        }

        // Create or get access token
        $accessToken = $this->createAccessToken(session('lawyer_user_id'), session('lawyer_complaint_id'));

        // Mark as authenticated
        session([
            'lawyer_authenticated' => true,
            'lawyer_access_token' => $accessToken
        ]);

        return redirect()->route('lawyer.dashboard');
    }

    public function dashboard()
    {
        $userId = session('lawyer_user_id');
        $user = User::findOrFail($userId);

        // Get all complaints assigned to this lawyer with lawyer relationship
        $complaints = Complaint::whereHas('lawyers', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['stage', 'attachments', 'lawyers' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->orderBy('created_at', 'desc')->get();

        return view('lawyer.dashboard', compact('user', 'complaints'));
    }

    public function viewComplaint(Complaint $complaint)
    {
        $userId = session('lawyer_user_id');

        // Check if user has access to this complaint
        $hasAccess = ComplaintLawyer::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $complaint->load([
            'attachments' => function($query) {
                $query->whereNull('lawyer_response_id');
            },
            'stage',
            'lawyers.user',
            'respondents.user',
            'replies.user',
            'replies.recipient'
        ]);

        // Get respondent responses for this complaint
        $respondentResponses = \App\Models\RespondentResponseDetail::where('complaint_id', $complaint->id)
            ->with(['user', 'attachments'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Get existing detailed response for this user
        $existingResponse = LawyerResponseDetail::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->with('attachments')
            ->first();

        return view('lawyer.complaint-view', compact('complaint', 'existingResponse', 'respondentResponses'));
    }

    public function submitResponse(Request $request, Complaint $complaint)
    {
        \Log::info('=== LAWYER RESPONSE SUBMISSION STARTED ===', [
            'complaint_id' => $complaint->id,
            'user_id' => session('lawyer_user_id'),
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'lawyer_email' => 'required|email|max:255',
            'law_firm_name' => 'required|string|max:255',
            'lawyer_city_state' => 'required|string|max:255',
            'lawyer_name' => 'required|string|max:255',
            'review_date' => 'required|date|before_or_equal:today',
            'legal_assessment' => 'required|string|min:10',
            'legal_recommendations' => 'required|string|min:10',
            'compliance_notes' => 'required|string|min:3',
            'supporting_evidence_type' => 'required|in:legal_docs,case_law,regulations,correspondence,none',
            'evidence_description' => 'required_unless:supporting_evidence_type,none|string|min:10',
            'attachments.*' => 'nullable|file|max:1048576|mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp4,mov,avi,wmv', // 1GB = 1048576 KB
        ]);

        \Log::info('=== VALIDATION PASSED ===', ['validated_data' => $validated]);

        $userId = session('lawyer_user_id');

        // Check if user has access to this complaint
        $hasAccess = ComplaintLawyer::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        // Check if response already exists
        $existingResponse = LawyerResponseDetail::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingResponse) {
            return back()->with('error', 'You have already submitted a legal review for this complaint.');
        }

        try {
            \DB::beginTransaction();
            
            \Log::info('Lawyer submitting response for complaint: ' . $complaint->id . ' by user: ' . $userId);

            // Create the detailed response
            $response = LawyerResponseDetail::create([
                'complaint_id' => $complaint->id,
                'user_id' => $userId,
                'lawyer_email' => $validated['lawyer_email'],
                'case_number' => $complaint->case_number,
                'law_firm_name' => $validated['law_firm_name'],
                'lawyer_city_state' => $validated['lawyer_city_state'],
                'lawyer_name' => $validated['lawyer_name'],
                'review_date' => $validated['review_date'],
                'legal_assessment' => $validated['legal_assessment'],
                'legal_recommendations' => $validated['legal_recommendations'],
                'compliance_notes' => $validated['compliance_notes'],
                'supporting_evidence_type' => $validated['supporting_evidence_type'],
                'evidence_description' => $validated['evidence_description'] ?? null,
                'has_supporting_evidence' => $validated['supporting_evidence_type'] !== 'none',
                'submitted_at' => now()
            ]);
            
            \Log::info('Lawyer response created with ID: ' . $response->id);

            // Handle file attachments
            if ($request->hasFile('attachments') && $validated['supporting_evidence_type'] !== 'none') {
                $path = public_path('images/lawyer-responses');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . Str::random(8) . '_' . $file->getClientOriginalName();
                    $file->move($path, $filename);
                    $filePath = 'images/lawyer-responses/' . $filename;

                    Attachment::create([
                        'lawyer_response_id' => $response->id,
                        'uploaded_by' => $userId,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'description' => 'Lawyer legal review evidence'
                    ]);
                }
            }

            // Update complaint stage to next stage after lawyer review (optional)
            try {
                $reviewStage = \App\Models\Stage::where('id','>',$complaint->stage_id)->first();
                
                if ($reviewStage) {
                    $oldStageId = $complaint->stage_id;
                    $complaint->update(['stage_id' => $reviewStage->id]);
                    
                    // Log stage change
                    StageChangeLog::logChange(
                        $complaint->id,
                        $oldStageId,
                        $reviewStage->id,
                        'lawyer_review_submitted',
                        'Lawyer submitted legal review for complaint',
                        [
                            'lawyer_name' => $validated['lawyer_name'],
                            'response_id' => $response->id,
                            'law_firm' => $validated['law_firm_name'],
                            'has_evidence' => $validated['supporting_evidence_type'] !== 'none'
                        ]
                    );
                    \Log::info('Stage updated to: ' . $reviewStage->name . ' for complaint: ' . $complaint->id);
                } else {
                    \Log::info('No next stage found for complaint: ' . $complaint->id . ', keeping current stage');
                }
            } catch (\Exception $stageError) {
                \Log::error('Error updating stage: ' . $stageError->getMessage());
                // Continue without stage update - response is still saved
            }

            // Mark lawyer as responded in the ComplaintLawyer table
            $complaintLawyer = ComplaintLawyer::where('complaint_id', $complaint->id)
                ->where('user_id', $userId)
                ->first();

            if ($complaintLawyer) {
                $complaintLawyer->update(['responded_at' => now()]);
            }

            \DB::commit();
            
            \Log::info('Lawyer response transaction committed successfully for complaint: ' . $complaint->id);

            // Send email notifications
            try {
                $this->sendResponseNotifications($complaint, $response);
                \Log::info('Email notifications sent for lawyer response: ' . $response->id);
            } catch (\Exception $emailError) {
                \Log::error('Failed to send email notifications: ' . $emailError->getMessage());
            }

            return redirect()->route('lawyer.complaint.view', $complaint)
                ->with('success', '✅ Your legal review has been submitted successfully! Case ID: ' . $complaint->case_number . '. Administrators have been notified.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error submitting lawyer response: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->withInput()
                ->with('error', 'Error submitting legal review. Please try again.');
        }
    }

    private function sendResponseNotifications(Complaint $complaint, LawyerResponseDetail $response)
    {
        try {
            // Load complaint with necessary relationships
            $complaint->load(['attachments' => function($query) {
                $query->whereNull('lawyer_response_id');
            }, 'respondents.user']);

            // Load response with attachments
            $response->load('attachments');

            \Log::info('Starting to send lawyer response notifications for case: ' . $complaint->case_number);

            // 1. Send email notification to admin users
            try {
                $adminUsers = User::where('role', 'admin')->get();
                \Log::info('Found ' . $adminUsers->count() . ' admin users for lawyer response notification');
                
                foreach ($adminUsers as $adminUser) {
                    if ($adminUser->email) {
                        \Mail::to($adminUser->email)->send(new LawyerResponseNotification($complaint, $response, 'admin'));
                        \Log::info('Admin notification sent to: ' . $adminUser->email);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send admin notification for lawyer response: ' . $e->getMessage());
            }

            // 2. Send email notification to complainant (if email available)
            \Log::info('Checking complainant email for lawyer response - Email field: ' . ($complaint->email ?? 'null'));
            if ($complaint->email) {
                try {
                    \Mail::to($complaint->email)->send(new LawyerResponseNotification($complaint, $response, 'complainant'));
                    \Log::info('Complainant notification sent for lawyer response - case: ' . $complaint->case_number . ' to: ' . $complaint->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send complainant notification for lawyer response to ' . $complaint->email . ': ' . $e->getMessage());
                }
            } else {
                \Log::warning('No complainant email found for lawyer response - case: ' . $complaint->case_number);
            }

            // 3. Send email notification to respondent(s) - Multiple sources
            try {
                $emailsSent = 0;
                $respondentEmails = [];
                
                // Method 1: Check complaint_respondents table with user relationship
                $respondents = $complaint->respondents()->with('user')->get();
                \Log::info('Found ' . $respondents->count() . ' respondents in complaint_respondents table for lawyer response');
                
                foreach ($respondents as $respondent) {
                    if ($respondent->user && $respondent->user->email) {
                        $respondentEmails[] = $respondent->user->email;
                    }
                }
                
                // Method 2: Check complainee_email field in complaints table
                if ($complaint->complainee_email) {
                    \Log::info('Found complainee_email for lawyer response: ' . $complaint->complainee_email);
                    $respondentEmails[] = $complaint->complainee_email;
                }
                
                // Remove duplicates
                $respondentEmails = array_unique($respondentEmails);
                \Log::info('Total respondent emails to notify about lawyer response: ' . json_encode($respondentEmails));
                
                // Send emails to all found respondent emails
                foreach ($respondentEmails as $email) {
                    try {
                        \Mail::to($email)->send(new LawyerResponseNotification($complaint, $response, 'respondent'));
                        \Log::info('Respondent notification sent to: ' . $email . ' for lawyer response');
                        $emailsSent++;
                    } catch (\Exception $e) {
                        \Log::error('Failed to send lawyer response email to respondent ' . $email . ': ' . $e->getMessage());
                    }
                }
                
                if ($emailsSent > 0) {
                    \Log::info($emailsSent . ' respondent notifications sent for lawyer response - case: ' . $complaint->case_number);
                } else {
                    \Log::warning('No respondent emails sent for lawyer response - case: ' . $complaint->case_number);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send respondent notifications for lawyer response: ' . $e->getMessage());
            }

            // 4. Send confirmation email to the lawyer(s)
            try {
                $lawyerEmails = [];
                
                // Add the lawyer who submitted the response
                if ($response->lawyer_email) {
                    $lawyerEmails[] = $response->lawyer_email;
                    \Log::info('Added responding lawyer email: ' . $response->lawyer_email);
                }
                
                // Also get the originally assigned lawyer(s) for this complaint
                $assignedLawyers = $complaint->lawyers()->with('user')->get();
                \Log::info('Found ' . $assignedLawyers->count() . ' assigned lawyers for this complaint');
                
                foreach ($assignedLawyers as $assignedLawyer) {
                    if ($assignedLawyer->user && $assignedLawyer->user->email) {
                        $lawyerEmails[] = $assignedLawyer->user->email;
                        \Log::info('Added assigned lawyer email: ' . $assignedLawyer->user->email);
                    }
                }
                
                // Get current logged-in lawyer's email from session as backup
                $currentLawyerUserId = session('lawyer_user_id');
                if ($currentLawyerUserId) {
                    $currentLawyer = User::find($currentLawyerUserId);
                    if ($currentLawyer && $currentLawyer->email) {
                        $lawyerEmails[] = $currentLawyer->email;
                        \Log::info('Added current session lawyer email: ' . $currentLawyer->email);
                    }
                }
                
                // Remove duplicates
                $lawyerEmails = array_unique($lawyerEmails);
                \Log::info('Final lawyer emails for notification: ' . json_encode($lawyerEmails));
                
                // Send emails to all lawyer emails
                foreach ($lawyerEmails as $lawyerEmail) {
                    try {
                        \Mail::to($lawyerEmail)->send(new LawyerResponseNotification($complaint, $response, 'lawyer'));
                        \Log::info('Confirmation email sent to lawyer: ' . $lawyerEmail . ' for case: ' . $complaint->case_number);
                    } catch (\Exception $emailError) {
                        \Log::error('Failed to send confirmation email to lawyer ' . $lawyerEmail . ': ' . $emailError->getMessage());
                    }
                }
                
                if (empty($lawyerEmails)) {
                    \Log::warning('No lawyer emails found to send confirmation for case: ' . $complaint->case_number);
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to send confirmation emails to lawyers: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
            }

            \Log::info('Completed sending lawyer response notifications for case: ' . $complaint->case_number);

        } catch (\Exception $e) {
            \Log::error('Error sending lawyer response notification emails: ' . $e->getMessage());
            // Don't fail the request if email sending fails
        }
    }

    public function profile()
    {
        $user = User::findOrFail(session('lawyer_user_id'));
        return view('lawyer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . session('lawyer_user_id'),
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail(session('lawyer_user_id'));
        $user->update($validated);

        return redirect()->route('lawyer.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function logout()
    {
        // Clear all lawyer session data
        session()->forget([
            'lawyer_complaint_id',
            'lawyer_user_id',
            'complaint_number',
            'lawyer_authenticated',
            'lawyer_access_token'
        ]);

        return redirect()->route('lawyer.login')
            ->with('success', 'You have been logged out successfully.');
    }

    private function createAccessToken($userId, $complaintId)
    {
        $token = Str::random(32);

        // Store or update access token in database (you may want to create a table for this)
        LawyerAccess::updateOrCreate([
            'user_id' => $userId,
            'complaint_id' => $complaintId,
        ], [
            'access_token' => $token,
            'expires_at' => now()->addDays(30), // Token expires in 30 days
        ]);

        return $token;
    }
}