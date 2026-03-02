<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Support\Facades\Storage;
use App\Models\ComplaintRespondent;
use App\Models\ComplaintLawyer;
use App\Models\ComplaintResponse;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\RespondentAssignedNotification;
use App\Mail\LawyerAssignedNotification;
use App\Mail\LawyerAssignmentNotification;
use App\Mail\ComplaintStatusUpdateNotification;
use App\Mail\AdminComplaintNotification;
use App\Models\StageChangeLog;

class AdminComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $complaints = Complaint::with(['respondents', 'investigationLogs', 'latestResolution', 'latestResolution.signatures', 'stage'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->whereHas('stage', function($q) use ($status) {
                    $q->where('name', $status);
                });
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhereHas('respondents', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(5, ['*'], 'page', $request->get('page', 1))->withQueryString();

        $users = User::where('role', 'respondent')->get();
        $respondents = User::where('role', 'respondent')->get(['id', 'name', 'email']);
        $lawyers = User::where('role', 'lawyer')->get(['id', 'name', 'email']);

        $stages = \App\Models\Stage::orderBy('step_number')->get();
        $counts = [
            'all' => Complaint::count(),
        ];

        foreach($stages as $stage) {
            $counts[$stage->name] = Complaint::where('stage_id', $stage->id)->count();
        }

        return view('admin.complaints', compact('complaints', 'users', 'respondents', 'lawyers', 'status', 'counts', 'stages'));
    }
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'nullable|string|max:20',
                'respondent_id' => 'required|exists:users,id',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'complaint_type' => 'required|string|max:100',
                'attachments.*' => 'nullable|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mov,avi,wmv', // 50MB max for videos
            ]);

            \Log::info('Creating new complaint with data:', ['data' => $validated]);

            $complaint = Complaint::create([
                'case_number' => 'COMP-' . strtoupper(Str::random(8)),
                'submitted_by_admin' => true,
                'submitted_by_admin_id' => auth()->id(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'complaint_type' => $validated['complaint_type'],
                'status' => 'submitted',
                'submitted_at' => now(),
                'anonymity' => $request->anonymity
            ]);

            if (!$complaint) {
                throw new \Exception('Failed to save complaint');
            }

            if ($request->hasFile('attachments')) {
                // Create complaint-images directory if it doesn't exist
                $path = public_path('images/complaint-images');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move($path, $filename);
                    $filePath = 'images/complaint-images/' . $filename;

                    // Create attachment record
                    Attachment::create([
                        'complaint_id' => $complaint->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'description' => null
                    ]);
                }
            }

            // Create complaint respondent
            ComplaintRespondent::create([
                'complaint_id' => $complaint->id,
                'user_id' => $validated['respondent_id']
            ]);

            \DB::commit();

            return redirect()->route('admin.complaints')
                ->with('success', 'Complaint created successfully with case number: ' . $complaint->case_number);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating complaint: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating complaint: ' . $e->getMessage());
        }
    }

    public function destroy(Complaint $complaint)
    {
        try {
            \DB::beginTransaction();

            // The complaint model's boot method will handle deleting related records
            $complaint->delete();

            \DB::commit();

            return redirect()->route('admin.complaints')
                ->with('success', 'Complaint deleted successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting complaint: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error deleting complaint: ' . $e->getMessage());
        }
    }

    public function addRespondent(Request $request)
    {
        $validated = $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'respondent_id' => 'required|exists:users,id'
        ]);

        try {
            $complaint = Complaint::findOrFail($validated['complaint_id']);

            // Create a new complaint respondent
            ComplaintRespondent::create([
                'complaint_id' => $validated['complaint_id'],
                'user_id' => $validated['respondent_id'],
                'send_to' => 'respondent'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Respondent added successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error adding respondent: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding respondent'
            ], 500);
        }
    }

    public function show(Complaint $complaint)
    {
        try {
            // Load all necessary relationships
            $complaint->load([
                'respondents.user',
                'lawyers.user',
                'attachments',
                'stage',
                'stageChangeLogs.fromStage',
                'stageChangeLogs.toStage',
                'stageChangeLogs.performer',
                'signatures',
                'respondentResponseDetails.attachments',
                'lawyerResponseDetails.attachments',
                'replies.user',
                'replies.recipient'
            ]);
            

            // Check if complaint exists
            if (!$complaint) {
                return redirect()->route('admin.complaints')
                    ->with('error', 'Complaint not found.');
            }

            // Get all available lawyers (exclude those already added to this complaint)
            $existingLawyerIds = $complaint->lawyers->pluck('user_id')->toArray();
            $availableLawyers = User::where('role', 'lawyer')
                ->whereNotIn('id', $existingLawyerIds)
                ->get(['id', 'name', 'email']);
            
            // Get all available respondents (exclude those already added to this complaint)
            $existingRespondentIds = $complaint->respondents->pluck('user_id')->toArray();
            $availableRespondents = User::where('role', 'respondent')
                ->whereNotIn('id', $existingRespondentIds)
                ->get(['id', 'name', 'email']);
            
            // Check if there are any respondent responses to show Add Lawyer button
            $hasRespondentResponses = $complaint->respondentResponseDetails && $complaint->respondentResponseDetails->count() > 0;
            
            // Return the view with the complaint data
            return view('admin.complaints.show', compact('complaint', 'availableLawyers', 'availableRespondents', 'hasRespondentResponses'));

        } catch (\Exception $e) {
            \Log::error('Error showing complaint: ' . $e->getMessage(), [
                'complaint_id' => $complaint->id ?? 'unknown',
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('admin.complaints')
                ->with('error', 'Error loading complaint details: ' . $e->getMessage());
        }
    }

    public function edit(Complaint $complaint)
    {
        $complaint->load('respondents');
        $respondents = User::where('role', 'respondent')->get();
        return view('admin.complaints.edit', compact('complaint', 'respondents'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        \Log::info('Update method called', ['request' => $request->all(), 'complaint_id' => $complaint->id]);
        \Log::info('Updating complaint with data:', ['data' => $request->all()]);

        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'nullable|string|max:20',
                'location' => 'required|string|max:255',
                'complaint_type' => 'required|string|in:service,facility,staff,safety,other',
                'description' => 'required|string',
                'attachments.*' => 'nullable|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mov,avi,wmv', // 50MB max for videos
                'respondent_ids' => 'nullable|array',
                'respondent_ids.*' => 'exists:users,id'
            ]);
            $validated['anonymity'] = $request->anonymity;

            // Update complaint details
            $complaint->update($validated);

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                // Create complaint-images directory if it doesn't exist
                $path = public_path('images/complaint-images');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move($path, $filename);
                    $filePath = 'images/complaint-images/' . $filename;

                    // Create attachment record
                    Attachment::create([
                        'complaint_id' => $complaint->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'description' => null
                    ]);
                }
            }

            // Update respondents
            if ($request->has('respondent_ids')) {
                // Delete existing respondents
                ComplaintRespondent::where('complaint_id', $complaint->id)->delete();

                // Add new respondents
                foreach ($request->input('respondent_ids') as $respondentId) {
                    ComplaintRespondent::create([
                        'complaint_id' => $complaint->id,
                        'user_id' => $respondentId
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('admin.complaints.show', $complaint)
                ->with('success', 'Complaint updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating complaint: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating complaint: ' . $e->getMessage());
        }
    }

    public function investigate(Request $request, Complaint $complaint)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'status' => 'required|string|in:under_review,resolved,closed,escalated',
            'notes' => 'nullable|string',
            'next_steps' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();

            // Update complaint status
            $complaint->status = $request->status;
            $complaint->save();

            // Create investigation log
            $complaint->investigationLogs()->create([
                'note' => $request->subject . "\n\n" . ($request->notes ?? ''),
                'next_steps' => $request->next_steps,
                'created_by' => auth()->id()
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Investigation update saved successfully'
            ]);

        } catch (\Exception $e) {

            \DB::rollBack();

            return response()->json([
                'success' => false,
                'errors' => ['error' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function showInvestigateForm(Complaint $complaint)
    {
        return view('admin.complaints.investigate', compact('complaint'));
    }

    public function investigationHistory(Complaint $complaint)
    {
        $complaint->load(['investigationLogs.creator']);
        return view('admin.complaints.investigation-history', compact('complaint'));
    }

    public function updateResponse(Request $request, ComplaintResponse $response)
    {
        try {
            $validated = $request->validate([
                'response' => 'required|string|min:10',
            ]);

            $response->update([
                'response' => $validated['response'],
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => 'Response updated successfully',
                'response' => $response->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating response: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteResponse(ComplaintResponse $response)
    {
        try {
            // Delete any attachments associated with this response
            foreach ($response->attachments as $attachment) {
                if (file_exists(public_path($attachment->file_path))) {
                    unlink(public_path($attachment->file_path));
                }
                $attachment->delete();
            }

            $response->delete();

            return response()->json([
                'message' => 'Response deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showInvestigationLog(Complaint $complaint, $logId)
    {
        $log = $complaint->investigationLogs()->findOrFail($logId);

        return response()->json([
            'subject' => explode("\n\n", $log->note)[0],
            'note' => isset(explode("\n\n", $log->note)[1]) ? explode("\n\n", $log->note)[1] : '',
            'next_steps' => $log->next_steps,
            'status' => $complaint->status
        ]);
    }

    public function updateInvestigationLog(Request $request, Complaint $complaint, $logId)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'status' => 'required|string|in:under_review,resolved,closed,escalated',
            'notes' => 'nullable|string',
            'next_steps' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();

            $log = $complaint->investigationLogs()->findOrFail($logId);

            // Update complaint status
            $complaint->status = $request->status;
            $complaint->save();

            // Update investigation log
            $log->update([
                'note' => $request->subject . "\n\n" . ($request->notes ?? ''),
                'next_steps' => $request->next_steps
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Investigation update saved successfully'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'errors' => ['error' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function deleteInvestigationLog(Complaint $complaint, $logId)
    {
        try {
            $log = $complaint->investigationLogs()->findOrFail($logId);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Investigation update deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function addRespondentToComplaint(Request $request, Complaint $complaint)
    {
        try {
            // Validate based on respondent type
            $respondentType = $request->input('respondent_type', 'existing');
            
            if ($respondentType === 'new') {
                $validated = $request->validate([
                    'new_respondent_name' => 'required|string|max:255',
                    'new_respondent_email' => 'required|email|unique:users,email',
                    'new_respondent_phone' => 'nullable|string|max:20',
                    'message' => 'nullable|string|max:1000',
                ]);
                
                // Create new user with respondent role
                $respondent = User::create([
                    'name' => $validated['new_respondent_name'],
                    'email' => $validated['new_respondent_email'],
                    'role' => 'respondent',
                    'password' => Hash::make(Str::random(12)), // Random password, user will get OTP login
                    'email_verified_at' => now(),
                ]);
                
                $userId = $respondent->id;
            } else {
                $validated = $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'message' => 'nullable|string|max:1000',
                ]);
                
                $userId = $validated['user_id'];
                $respondent = User::findOrFail($userId);
            }

            // Check if respondent already exists for this complaint
            $existingRespondent = ComplaintRespondent::where('complaint_id', $complaint->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingRespondent) {
                return redirect()->back()->with('error', 'This respondent is already assigned to this complaint.');
            }

            // Create new complaint respondent using existing table structure
            ComplaintRespondent::create([
                'complaint_id' => $complaint->id,
                'user_id' => $userId,
            ]);

            // Respondent user is already retrieved above ($respondent)

            // Automatically update stage when adding respondent
            $oldStageId = $complaint->stage_id;
            
            // Find appropriate stage for respondent assignment
            $respondentStage = \App\Models\Stage::where('name', 'LIKE', '%respond%')
                ->orWhere('name', 'LIKE', '%sent%')
                ->orderBy('step_number')
                ->first();
                
            // If no specific stage found, get next available stage
            if (!$respondentStage) {
                $respondentStage = \App\Models\Stage::where('step_number', '>', $complaint->stage ? $complaint->stage->step_number : 1)
                    ->orderBy('step_number')
                    ->first();
            }
            
            $stageMessage = '';
            
            // Update stage if we found an appropriate one
            if ($respondentStage && $respondentStage->id != $oldStageId) {
                $complaint->update([
                    'stage_id' => $respondentStage->id
                ]);
                
                $stageMessage = ' and stage updated to "' . $respondentStage->name . '"';
            }
            
            // Always log the respondent assignment (this will show in email thread)
            $description = 'Complaint sent to respondent: ' . $respondent->name . ' for response';
            $message = $request->input('message');
            if (!empty($message)) {
                $description .= "\nMessage: " . $message;
            }
            if ($respondentType === 'new') {
                $description .= "\n(New respondent account created)";
            }
            
            StageChangeLog::logChange(
                $complaint->id,
                $oldStageId,
                $respondentStage ? $respondentStage->id : $oldStageId,
                'respondent_assigned',
                $description,
                [
                    'respondent_id' => $respondent->id,
                    'respondent_name' => $respondent->name,
                    'respondent_email' => $respondent->email,
                    'message' => $message ?? null,
                    'stage_changed' => $respondentStage && $respondentStage->id != $oldStageId,
                    'created_new_user' => $respondentType === 'new'
                ]
            );


            // Send email notifications
            try {
                // Send email to the assigned respondent
                Mail::to($respondent->email)->send(new RespondentAssignedNotification($complaint, $respondent));

                // Send email to complainant if email exists
                if (!empty($complaint->email)) {
                    $updateMessage = 'Your complaint has been assigned to a team member for review. ' .
                                   'Our team will investigate your concerns and respond accordingly.';
                    Mail::to($complaint->email)->send(new ComplaintStatusUpdateNotification($complaint, $updateMessage));
                }
            } catch (\Exception $e) {
                \Log::error('Error sending respondent assignment emails: ' . $e->getMessage());
                // Don't fail the request if email sending fails
            }

            return redirect()->back()->with('success', 'Respondent added successfully' . $stageMessage . '. Notification emails have been sent.');

        } catch (\Exception $e) {
            \Log::error('Error adding respondent: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error adding respondent.');
        }
    }

    public function addLawyerToComplaint(Request $request, Complaint $complaint)
    {
        try {
            // Validate based on lawyer type
            $lawyerType = $request->input('lawyer_type', 'existing');
            
            if ($lawyerType === 'new') {
                $validated = $request->validate([
                    'new_lawyer_name' => 'required|string|max:255',
                    'new_lawyer_email' => 'required|email|unique:users,email',
                    'new_lawyer_phone' => 'nullable|string|max:20',
                    'new_lawyer_firm' => 'nullable|string|max:255',
                    'message' => 'nullable|string|max:1000',
                ]);
                
                // Create new user with lawyer role
                $lawyer = User::create([
                    'name' => $validated['new_lawyer_name'],
                    'email' => $validated['new_lawyer_email'],
                    'role' => 'lawyer',
                    'password' => Hash::make(Str::random(12)), // Random password, user will get OTP login
                    'email_verified_at' => now(),
                ]);
                
                $userId = $lawyer->id;
            } else {
                $validated = $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'message' => 'nullable|string|max:1000',
                ]);
                
                $userId = $validated['user_id'];
                $lawyer = User::findOrFail($userId);
            }

            // Check if lawyer already exists for this complaint
            $existingLawyer = ComplaintLawyer::where('complaint_id', $complaint->id)
                ->where('user_id', $userId)
                ->first();
                
            if ($existingLawyer) {
                return redirect()->back()->with('error', 'This lawyer is already assigned to this complaint.');
            }

            // Create new complaint lawyer
            $complaintLawyer = ComplaintLawyer::create([
                'complaint_id' => $complaint->id,
                'user_id' => $userId,
            ]);

            \Log::info('ComplaintLawyer created successfully', [
                'complaint_id' => $complaint->id,
                'user_id' => $userId,
                'lawyer_name' => $lawyer->name,
                'lawyer_email' => $lawyer->email,
                'complaint_lawyer_id' => $complaintLawyer->id
            ]);

            // Lawyer user is already retrieved above ($lawyer)

            // Automatically update stage when adding lawyer
            $oldStageId = $complaint->stage_id;
            
            // Find appropriate stage for lawyer assignment
            $lawyerStage = \App\Models\Stage::where('name', 'LIKE', '%lawyer%')
                ->orWhere('name', 'LIKE', '%legal%')
                ->orWhere('name', 'LIKE', '%review%')
                ->orderBy('step_number')
                ->first();
                
            // If no specific stage found, get next available stage
            if (!$lawyerStage) {
                $lawyerStage = \App\Models\Stage::where('step_number', '>', $complaint->stage ? $complaint->stage->step_number : 1)
                    ->orderBy('step_number')
                    ->first();
            }
            
            $stageMessage = '';
            
            // Update stage if we found an appropriate one
            if ($lawyerStage && $lawyerStage->id != $oldStageId) {
                $complaint->update([
                    'stage_id' => $lawyerStage->id
                ]);
                
                $stageMessage = ' and stage updated to "' . $lawyerStage->name . '"';
            }
            
            // Always log the lawyer assignment (this will show in email thread)
            $description = 'Complaint sent to lawyer: ' . $lawyer->name . ' for legal review';
            $message = $request->input('message');
            if (!empty($message)) {
                $description .= "\nMessage: " . $message;
            }
            if ($lawyerType === 'new') {
                $description .= "\n(New lawyer account created)";
            }
            
            $stageChangeLog = StageChangeLog::logChange(
                $complaint->id,
                $oldStageId,
                $lawyerStage ? $lawyerStage->id : $oldStageId,
                'lawyer_assigned',
                $description,
                [
                    'lawyer_id' => $lawyer->id,
                    'lawyer_name' => $lawyer->name,
                    'lawyer_email' => $lawyer->email,
                    'message' => $message ?? null,
                    'stage_changed' => $lawyerStage && $lawyerStage->id != $oldStageId,
                    'created_new_user' => $lawyerType === 'new'
                ]
            );

            \Log::info('StageChangeLog created for lawyer assignment', [
                'complaint_id' => $complaint->id,
                'stage_change_log_id' => $stageChangeLog ? $stageChangeLog->id : 'null',
                'action' => 'lawyer_assigned',
                'description' => $description,
                'old_stage_id' => $oldStageId,
                'new_stage_id' => $lawyerStage ? $lawyerStage->id : $oldStageId
            ]);

            // Send email notifications
            try {
                // Send email to the assigned lawyer
                Mail::to($lawyer->email)->send(new LawyerAssignedNotification($complaint, $lawyer, $message));
                \Log::info('Lawyer assignment email sent to: ' . $lawyer->email . ' for case: ' . $complaint->case_number);
                
                // Send email to complainant if email exists
                if (!empty($complaint->email)) {
                    $updateMessage = 'Your complaint has been sent to our legal team for review. ' .
                                   'A lawyer will review the case and provide recommendations.';
                    Mail::to($complaint->email)->send(new ComplaintStatusUpdateNotification($complaint, $updateMessage));
                    \Log::info('Complainant notification sent for lawyer assignment - case: ' . $complaint->case_number . ' to: ' . $complaint->email);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending lawyer assignment emails: ' . $e->getMessage());
                // Don't fail the request if email sending fails
            }

            return redirect()->back()->with('success', 'Lawyer added successfully' . $stageMessage . '. Notification emails have been sent.');

        } catch (\Exception $e) {
            \Log::error('Error adding lawyer: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error adding lawyer.');
        }
    }

    public function sendTo(Request $request, Complaint $complaint)
    {
        try {
            // Basic validation
            $validated = $request->validate([
                'send_to' => 'required|in:respondent,lawyer,complainant',
                'stage_id' => 'nullable|exists:stages,id',
                'message' => 'nullable|string',
                'respondent_id' => 'nullable|exists:users,id',
                'lawyer_id' => 'nullable|exists:users,id',
                'lawyer_name' => 'nullable|string|max:255',
                'lawyer_email' => 'nullable|email',
                'lawyer_phone' => 'nullable|string',
            ]);
            
            // Special validation for lawyer assignment
            if ($validated['send_to'] === 'lawyer') {
                // Must have either existing lawyer selected OR new lawyer details
                if (empty($validated['lawyer_id']) && (empty($validated['lawyer_name']) || empty($validated['lawyer_email']))) {
                    return redirect()->back()->with('error', 'Please either select an existing lawyer or provide new lawyer details (name and email are required).');
                }
                
                // If creating new lawyer, validate required fields
                if (empty($validated['lawyer_id'])) {
                    $request->validate([
                        'lawyer_name' => 'required|string|max:255',
                        'lawyer_email' => 'required|email|unique:users,email',
                    ]);
                    // Re-get validated data after additional validation
                    $validated = $request->validate([
                        'send_to' => 'required|in:respondent,lawyer,complainant',
                        'stage_id' => 'nullable|exists:stages,id',
                        'message' => 'nullable|string',
                        'respondent_id' => 'nullable|exists:users,id',
                        'lawyer_id' => 'nullable|exists:users,id',
                        'lawyer_name' => 'required|string|max:255',
                        'lawyer_email' => 'required|email|unique:users,email',
                        'lawyer_phone' => 'nullable|string',
                    ]);
                }
            }

            // Update send_to status
            $updateData = ['send_to' => $validated['send_to']];

            // Update stage if provided
            if (!empty($validated['stage_id'])) {
                $updateData['stage_id'] = $validated['stage_id'];
            }

            // Handle lawyer assignment
            if ($validated['send_to'] === 'lawyer') {
                $lawyer = null;
                
                // If existing lawyer selected
                if (!empty($validated['lawyer_id'])) {
                    $lawyer = User::find($validated['lawyer_id']);
                }
                // If new lawyer details provided, create new lawyer
                elseif (!empty($validated['lawyer_email']) && !empty($validated['lawyer_name'])) {
                    $lawyer = User::create([
                        'name' => $validated['lawyer_name'],
                        'email' => $validated['lawyer_email'],
                        'role' => 'lawyer',
                        'password' => Hash::make('defaultpassword123') // Should be changed on first login
                    ]);
                }
                
                // Assign lawyer to complaint if lawyer exists
                if ($lawyer) {
                    // Check if lawyer already assigned
                    $existing = $complaint->lawyers()->where('user_id', $lawyer->id)->first();
                    if (!$existing) {
                        $complaint->lawyers()->create(['user_id' => $lawyer->id]);
                    }
                    
                    // Update complaint with lawyer details for backward compatibility
                    $updateData['lawyer_email'] = $lawyer->email;
                    $updateData['lawyer_phone'] = $validated['lawyer_phone'] ?? null;
                    
                    // Send email notifications to all relevant parties
                    $message = $validated['message'] ?? null;
                    
                    // Send email notification to lawyer (with access link)
                    try {
                        Mail::to($lawyer->email)->send(new LawyerAssignedNotification($complaint, $lawyer, $message));
                        \Log::info('Email sent to lawyer: ' . $lawyer->email . ' for case: ' . $complaint->case_number);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send email to lawyer: ' . $e->getMessage());
                    }

                    \Log::info('Starting to send additional notifications for case: ' . $complaint->case_number);

                    // Send email notification to admin
                    try {
                        $adminUsers = User::where('role', 'admin')->get();
                        \Log::info('Found ' . $adminUsers->count() . ' admin users for notification');
                        
                        foreach ($adminUsers as $adminUser) {
                            if ($adminUser->email) {
                                Mail::to($adminUser->email)->send(new LawyerAssignmentNotification($complaint, $lawyer, 'admin', $message));
                                \Log::info('Admin notification sent to: ' . $adminUser->email);
                            }
                        }
                        
                        if ($adminUsers->count() > 0) {
                            \Log::info('Admin notifications sent for lawyer assignment - case: ' . $complaint->case_number);
                        } else {
                            \Log::warning('No admin users found to send notifications');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to send admin notification for lawyer assignment: ' . $e->getMessage());
                    }

                    // Send email notification to complainant (if email available)
                    \Log::info('Checking complainant email for case: ' . $complaint->case_number . ' - Email field: ' . ($complaint->email ?? 'null'));
                    if ($complaint->email) {
                        try {
                            Mail::to($complaint->email)->send(new LawyerAssignmentNotification($complaint, $lawyer, 'complainant', $message));
                            \Log::info('Complainant notification sent for lawyer assignment - case: ' . $complaint->case_number . ' to: ' . $complaint->email);
                        } catch (\Exception $e) {
                            \Log::error('Failed to send complainant notification for lawyer assignment to ' . $complaint->email . ': ' . $e->getMessage());
                        }
                    } else {
                        \Log::warning('No complainant email found for case: ' . $complaint->case_number . ' - complaint.email is empty/null');
                    }

                    // Send email notification to respondent(s) - Multiple sources
                    try {
                        $emailsSent = 0;
                        $respondentEmails = [];
                        
                        // Method 1: Check complaint_respondents table with user relationship
                        $respondents = $complaint->respondents()->with('user')->get();
                        \Log::info('Found ' . $respondents->count() . ' respondents in complaint_respondents table for case: ' . $complaint->case_number);
                        
                        foreach ($respondents as $respondent) {
                            if ($respondent->user && $respondent->user->email) {
                                $respondentEmails[] = $respondent->user->email;
                            }
                        }
                        
                        // Method 2: Check complainee_email field in complaints table
                        if ($complaint->complainee_email) {
                            \Log::info('Found complainee_email: ' . $complaint->complainee_email . ' for case: ' . $complaint->case_number);
                            $respondentEmails[] = $complaint->complainee_email;
                        }
                        
                        // Method 3: Check if respondent exists by email in users table (for specific cases)
                        $specificRespondentEmails = ['shikha@yopmail.com'];
                        foreach ($specificRespondentEmails as $email) {
                            $user = User::where('email', $email)->first();
                            if ($user) {
                                \Log::info('Found user with email: ' . $email);
                                $respondentEmails[] = $email;
                            }
                        }
                        
                        // Remove duplicates
                        $respondentEmails = array_unique($respondentEmails);
                        \Log::info('Total respondent emails to notify: ' . json_encode($respondentEmails));
                        
                        // Send emails to all found respondent emails
                        foreach ($respondentEmails as $email) {
                            try {
                                Mail::to($email)->send(new LawyerAssignmentNotification($complaint, $lawyer, 'respondent', $message));
                                \Log::info('Respondent notification sent to: ' . $email);
                                $emailsSent++;
                            } catch (\Exception $e) {
                                \Log::error('Failed to send email to respondent ' . $email . ': ' . $e->getMessage());
                            }
                        }
                        
                        if ($emailsSent > 0) {
                            \Log::info($emailsSent . ' respondent notifications sent for lawyer assignment - case: ' . $complaint->case_number);
                        } else {
                            \Log::warning('No respondent emails sent for case: ' . $complaint->case_number);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to send respondent notifications for lawyer assignment: ' . $e->getMessage());
                    }
                }
            }

            $oldStageId = $complaint->stage_id;
            $complaint->update($updateData);
            
            // Handle respondent case updates - AFTER database update
            if ($validated['send_to'] === 'respondent') {
                $message = $validated['message'] ?? 'Your complaint has been assigned to a respondent for review and response.';
                
                // Refresh complaint data before sending notifications
                $complaint->refresh();
                
                // Send notifications to all parties about respondent assignment
                $this->sendCaseUpdateNotifications($complaint, 'respondent', $message, $validated['respondent_id'] ?? null);
            }
            
            // Handle complainant case updates - AFTER database update
            if ($validated['send_to'] === 'complainant') {
                $message = $validated['message'] ?? 'Your complaint status has been updated. Please check the details for more information.';
                
                // Refresh complaint data before sending notifications
                $complaint->refresh();
                
                // Send notifications to all parties about complainant update
                $this->sendCaseUpdateNotifications($complaint, 'complainant', $message);
            }

            $sendToName = match($validated['send_to']) {
                'respondent' => 'Respondent',
                'lawyer' => 'Lawyer',
                'complainant' => 'Complainant',
                default => 'Unknown'
            };

            $stage = !empty($validated['stage_id']) ? \App\Models\Stage::find($validated['stage_id']) : null;
            $stageMessage = $stage ? ' and stage updated to "' . $stage->name . '"' : '';

            // Log send-to action
            StageChangeLog::logChange(
                $complaint->id,
                $oldStageId,
                $validated['stage_id'] ?? null,
                'sent_to_' . $validated['send_to'],
                'Complaint sent to ' . $sendToName . ($stage ? ' with stage change to: ' . $stage->name : ''),
                [
                    'send_to' => $validated['send_to'],
                    'lawyer_email' => $validated['lawyer_email'] ?? null,
                    'lawyer_phone' => $validated['lawyer_phone'] ?? null,
                    'respondent_id' => $validated['respondent_id'] ?? null,
                    'stage_changed' => !empty($validated['stage_id'])
                ]
            );

            
            return redirect()->back()->with('success', 'Complaint sent to ' . $sendToName . ' successfully' . $stageMessage . '.');

        } catch (\Exception $e) {
            \Log::error('Error sending complaint: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending complaint.');
        }
    }

    public function emailPreview(Complaint $complaint)
    {
        $emailData = session('email_preview');
        
        // If session data exists, use it
        if ($emailData && $emailData['type'] === 'lawyer-assigned') {
            // Clear the session data after retrieving it
            session()->forget('email_preview');
            
            return view('emails.lawyer-assigned', [
                'complaint' => $emailData['complaint'],
                'lawyer' => $emailData['lawyer'],
                'message' => $emailData['message'],
                'isPreview' => true
            ]);
        }
        
        // Fallback: try to get the most recently assigned lawyer
        $complaint->load(['stage', 'respondents.user', 'attachments', 'lawyers.user']);
        $lawyer = $complaint->lawyers->last()?->user;
        
        if (!$lawyer) {
            return redirect()->back()->with('error', 'No lawyer assigned to this complaint.');
        }
        
        return view('emails.lawyer-assigned', [
            'complaint' => $complaint,
            'lawyer' => $lawyer,
            'message' => 'Email preview generated from complaint data.',
            'isPreview' => true
        ]);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:submitted,under_review,escalated,resolved,closed'
            ]);

            $complaint->update([
                'status' => $validated['status']
            ]);

            return redirect()->back()->with('success', 'Complaint status updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating complaint status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating complaint status.');
        }
    }

    public function updateStage(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'stage_id' => 'required|exists:stages,id'
            ]);

            // Get the stage to verify it's active
            $stage = \App\Models\Stage::findOrFail($validated['stage_id']);
            $oldStageId = $complaint->stage_id;
            
            \DB::beginTransaction();

            // Check if this is a "resolved" stage
            $isResolvedStage = strtolower($stage->name) === 'resolved' || strtolower($stage->name) === 'resolve';

            // Update the complaint's stage and status
            $updateData = ['stage_id' => $validated['stage_id']];
            
            if ($isResolvedStage) {
                $updateData['status'] = 'resolved';
            }
            
            $complaint->update($updateData);

            // Log stage change
            StageChangeLog::logChange(
                $complaint->id,
                $oldStageId,
                $validated['stage_id'],
                'stage_changed',
                'Stage manually changed to: ' . $stage->name . ($isResolvedStage ? ' (Status updated to resolved)' : ''),
                ['changed_from_stage_id' => $oldStageId, 'changed_to_stage_id' => $validated['stage_id'], 'status_updated' => $isResolvedStage]
            );

            // If resolved, send notifications to all associated persons
            if ($isResolvedStage) {
                $this->sendResolvedNotifications($complaint);
            }

            \DB::commit();

            // Check if this is an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Complaint stage updated to "' . $stage->name . '"' . ($isResolvedStage ? ' and status set to resolved' : '') . ' successfully.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Complaint stage updated to "' . $stage->name . '"' . ($isResolvedStage ? ' and status set to resolved' : '') . ' successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating complaint stage: ' . $e->getMessage());
            // Check if this is an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating complaint stage.'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating complaint stage.');
        }
    }

    /**
     * Send notifications to all persons associated with a resolved complaint
     */
    private function sendResolvedNotifications(Complaint $complaint)
    {
        try {
            // Load all associated persons
            $complaint->load(['respondents.user', 'lawyers.user']);
            
            \Log::info('Sending resolved notifications for complaint: ' . $complaint->id);

            // Send to complainant if email exists
            if (!empty($complaint->email)) {
                try {
                    \Mail::to($complaint->email)->send(new \App\Mail\ComplaintResolvedNotification($complaint, 'complainant'));
                    \Log::info('Resolved notification sent to complainant: ' . $complaint->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send resolved notification to complainant: ' . $e->getMessage());
                }
            }

            // Send to all respondents
            foreach ($complaint->respondents as $respondent) {
                try {
                    \Mail::to($respondent->user->email)->send(new \App\Mail\ComplaintResolvedNotification($complaint, 'respondent', $respondent->user));
                    \Log::info('Resolved notification sent to respondent: ' . $respondent->user->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send resolved notification to respondent ' . $respondent->user->email . ': ' . $e->getMessage());
                }
            }

            // Send to all lawyers
            foreach ($complaint->lawyers as $lawyer) {
                try {
                    \Mail::to($lawyer->user->email)->send(new \App\Mail\ComplaintResolvedNotification($complaint, 'lawyer', $lawyer->user));
                    \Log::info('Resolved notification sent to lawyer: ' . $lawyer->user->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send resolved notification to lawyer ' . $lawyer->user->email . ': ' . $e->getMessage());
                }
            }

            \Log::info('Resolved notifications process completed for complaint: ' . $complaint->id);

        } catch (\Exception $e) {
            \Log::error('Error sending resolved notifications: ' . $e->getMessage());
            // Don't throw the exception - let the stage update succeed even if emails fail
        }
    }

    /**
     * Send comprehensive case update notifications to all relevant parties
     */
    private function sendCaseUpdateNotifications(Complaint $complaint, $updateType, $message, $respondentId = null)
    {
        try {
            // Reload complaint with relationships
            $complaint->load(['respondents.user', 'lawyers.user', 'stage']);
            
            \Log::info('Starting to send case update notifications', [
                'case_number' => $complaint->case_number,
                'update_type' => $updateType,
                'message' => $message
            ]);

            // 1. Send email notification to admin users
            try {
                $adminUsers = User::where('role', 'admin')->get();
                \Log::info('Found ' . $adminUsers->count() . ' admin users for case update notification');
                
                foreach ($adminUsers as $adminUser) {
                    if ($adminUser->email) {
                        // Use AdminComplaintNotification for admin users - better suited
                        Mail::to($adminUser->email)->send(new AdminComplaintNotification($complaint));
                        \Log::info('Admin case update notification sent to: ' . $adminUser->email . ' for case: ' . $complaint->case_number);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send admin notifications for case update: ' . $e->getMessage());
                \Log::error('Admin notification error trace: ' . $e->getTraceAsString());
            }

            // 2. Send email notification to complainant (if email available)
            if ($complaint->email) {
                try {
                    Mail::to($complaint->email)->send(new ComplaintStatusUpdateNotification($complaint, $message));
                    \Log::info('Complainant case update notification sent - case: ' . $complaint->case_number . ' to: ' . $complaint->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send complainant notification for case update to ' . $complaint->email . ': ' . $e->getMessage());
                }
            } else {
                \Log::warning('No complainant email found for case update - case: ' . $complaint->case_number);
            }

            // 3. Send email notification to respondent(s) - Multiple sources
            try {
                $emailsSent = 0;
                $respondentEmails = [];
                
                // Method 1: Check complaint_respondents table with user relationship
                $respondents = $complaint->respondents()->with('user')->get();
                \Log::info('Found ' . $respondents->count() . ' respondents in complaint_respondents table for case update');
                
                foreach ($respondents as $respondent) {
                    if ($respondent->user && $respondent->user->email) {
                        $respondentEmails[] = $respondent->user->email;
                    }
                }
                
                // Method 2: Check complainee_email field in complaints table
                if ($complaint->complainee_email) {
                    \Log::info('Found complainee_email for case update: ' . $complaint->complainee_email);
                    $respondentEmails[] = $complaint->complainee_email;
                }
                
                // Method 3: If specific respondent selected, ensure they're included
                if ($respondentId) {
                    $selectedRespondent = User::find($respondentId);
                    if ($selectedRespondent && $selectedRespondent->email) {
                        $respondentEmails[] = $selectedRespondent->email;
                        \Log::info('Added selected respondent email: ' . $selectedRespondent->email);
                    }
                }
                
                // Remove duplicates
                $respondentEmails = array_unique($respondentEmails);
                \Log::info('Total respondent emails to notify about case update: ' . json_encode($respondentEmails));
                
                // Send emails to all found respondent emails
                foreach ($respondentEmails as $email) {
                    try {
                        Mail::to($email)->send(new ComplaintStatusUpdateNotification($complaint, 'Respondent Update: ' . $message));
                        \Log::info('Respondent case update notification sent to: ' . $email);
                        $emailsSent++;
                    } catch (\Exception $e) {
                        \Log::error('Failed to send case update email to respondent ' . $email . ': ' . $e->getMessage());
                    }
                }
                
                if ($emailsSent > 0) {
                    \Log::info($emailsSent . ' respondent case update notifications sent - case: ' . $complaint->case_number);
                } else {
                    \Log::warning('No respondent emails sent for case update - case: ' . $complaint->case_number);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send respondent notifications for case update: ' . $e->getMessage());
            }

            // 4. Send email notification to assigned lawyers
            try {
                $lawyerEmails = [];
                $assignedLawyers = $complaint->lawyers()->with('user')->get();
                \Log::info('Found ' . $assignedLawyers->count() . ' assigned lawyers for case update notification');
                \Log::info('Lawyers details: ' . $assignedLawyers->map(function($lawyer) {
                    return [
                        'id' => $lawyer->id,
                        'user_id' => $lawyer->user_id,
                        'user_name' => $lawyer->user ? $lawyer->user->name : 'No user',
                        'user_email' => $lawyer->user ? $lawyer->user->email : 'No email'
                    ];
                })->toJson());
                
                foreach ($assignedLawyers as $assignedLawyer) {
                    if ($assignedLawyer->user && $assignedLawyer->user->email) {
                        $lawyerEmails[] = [
                            'email' => $assignedLawyer->user->email,
                            'user' => $assignedLawyer->user
                        ];
                    }
                }
                
                // Remove duplicates by email
                $uniqueLawyerEmails = collect($lawyerEmails)->unique('email')->values();
                \Log::info('Unique lawyer emails for case update notification: ' . $uniqueLawyerEmails->pluck('email')->toJson());
                
                // Send emails to all lawyer emails
                foreach ($uniqueLawyerEmails as $lawyerData) {
                    try {
                        // Use LawyerAssignmentNotification which is designed for lawyers
                        Mail::to($lawyerData['email'])->send(new \App\Mail\LawyerAssignmentNotification($complaint, $lawyerData['user'], 'case_update', $message));
                        \Log::info('Lawyer case update notification sent to: ' . $lawyerData['email'] . ' for case: ' . $complaint->case_number);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send case update email to lawyer ' . $lawyerData['email'] . ': ' . $e->getMessage());
                        \Log::error('Lawyer email error trace: ' . $e->getTraceAsString());
                    }
                }
                
                if ($uniqueLawyerEmails->isEmpty()) {
                    \Log::warning('No lawyers assigned for case update notification - case: ' . $complaint->case_number);
                } else {
                    \Log::info('Processed ' . $uniqueLawyerEmails->count() . ' lawyer notifications for case: ' . $complaint->case_number);
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to send lawyer notifications for case update: ' . $e->getMessage());
                \Log::error('Lawyer notification section error trace: ' . $e->getTraceAsString());
            }

            \Log::info('Completed sending case update notifications for case: ' . $complaint->case_number);

        } catch (\Exception $e) {
            \Log::error('Error sending case update notification emails: ' . $e->getMessage());
            // Don't fail the request if email sending fails
        }
    }

    public function storeReply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string',
            'recipient_id' => 'nullable|exists:users,id',
            'recipient_type' => 'nullable|in:respondent,lawyer'
        ]);

        $reply = $complaint->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'recipient_id' => $request->recipient_id,
            'recipient_type' => $request->recipient_type,
        ]);

        $reply->load('user', 'recipient');

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'reply' => $reply
        ]);
    }
}
