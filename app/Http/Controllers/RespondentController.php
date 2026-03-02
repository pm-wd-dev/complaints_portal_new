<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintRespondent;
use App\Models\ComplaintResponse;
use App\Models\RespondentAccess;
use App\Models\RespondentResponseDetail;
use App\Models\Attachment;
use App\Models\StageChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RespondentController extends Controller
{
    public function showLoginForm()
    {
        return view('respondent.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'complaint_number' => 'required|string',
        ]);

        // Check if complaint exists and user has access
        $complaint = Complaint::where('case_number', $validated['complaint_number'])->first();

        if (!$complaint) {
            return back()->withErrors(['complaint_number' => 'Invalid complaint number.']);
        }

        // Check if user is a respondent for this complaint
        $respondent = ComplaintRespondent::where('complaint_id', $complaint->id)
            ->with('user')
            ->first();

        if (!$respondent) {
            return back()->withErrors(['complaint_number' => 'You are not authorized to access this complaint.']);
        }

        // Store complaint and respondent info in session
        session([
            'respondent_complaint_id' => $complaint->id,
            'respondent_user_id' => $respondent->user_id,
            'complaint_number' => $complaint->case_number
        ]);

        return redirect()->route('respondent.otp');
    }

    public function showOtpForm()
    {
        if (!session('respondent_complaint_id')) {
            return redirect()->route('respondent.login');
        }

        return view('respondent.otp');
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

        if (!session('respondent_complaint_id')) {
            return redirect()->route('respondent.login');
        }

        // Create or get access token
        $accessToken = $this->createAccessToken(session('respondent_user_id'), session('respondent_complaint_id'));

        // Mark as authenticated
        session([
            'respondent_authenticated' => true,
            'respondent_access_token' => $accessToken
        ]);

        return redirect()->route('respondent.dashboard');
    }

    public function dashboard()
    {
        $userId = session('respondent_user_id');
        $user = User::findOrFail($userId);

        // Get all complaints assigned to this respondent
        $complaints = Complaint::whereHas('respondents', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['stage', 'attachments'])->orderBy('created_at', 'desc')->get();

        return view('respondent.dashboard', compact('user', 'complaints'));
    }

    public function viewComplaint(Complaint $complaint)
    {
        $userId = session('respondent_user_id');

        // Check if user has access to this complaint
        $hasAccess = ComplaintRespondent::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $complaint->load([
            'attachments' => function($query) {
                $query->whereNull('respondent_response_id');
            },
            'stage',
            'respondents.user',
            'respondents.responses.attachments',
            'replies.user',
            'replies.recipient'
        ]);

        // Get existing detailed response for this user
        $existingResponse = RespondentResponseDetail::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->with('attachments')
            ->first();

        return view('respondent.complaint-view', compact('complaint', 'existingResponse'));
    }

    public function submitResponse(Request $request, Complaint $complaint)
    {
        \Log::info('Respondent form submission started', [
            'complaint_id' => $complaint->id,
            'request_data' => $request->all(),
            'user_id' => session('respondent_user_id')
        ]);

        // Create validator manually to handle errors properly
        $validator = \Validator::make($request->all(), [
            'respondent_email' => 'required|email|max:255',
            'venue_legal_name' => 'required|string|max:255',
            'venue_city_state' => 'required|string|max:255',
            'respondent_name' => 'required|string|max:255',
            'complaint_date' => 'required|date|before_or_equal:today',
            'respondent_side_story' => 'required|string|min:10',
            'issue_detail_description' => 'required|string|min:10',
            'witnesses_information' => 'required|string|min:3',
            'supporting_evidence_type' => 'required|in:photos,videos,messages,documents,none',
            'evidence_description' => 'required_unless:supporting_evidence_type,none|string|min:10',
            'attachments.*' => 'nullable|file|max:1048576|mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp4,mov,avi,wmv', // 1GB = 1048576 KB
        ]);

        if ($validator->fails()) {
            \Log::info('Validation failed for respondent form', [
                'complaint_id' => $complaint->id,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->except(['attachments'])
            ]);
            
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Please check the form for errors and try again.');
        }

        $validated = $validator->validated();
        
        \Log::info('Validation passed successfully', [
            'complaint_id' => $complaint->id,
            'validated_data' => array_keys($validated)
        ]);

        $userId = session('respondent_user_id');

        // Check if user has access to this complaint
        $hasAccess = ComplaintRespondent::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        // Check if response already exists
        $existingResponse = RespondentResponseDetail::where('complaint_id', $complaint->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingResponse) {
            return back()->with('error', 'You have already submitted a response to this complaint.');
        }

        try {
            \DB::beginTransaction();

            // Create the detailed response
            $response = RespondentResponseDetail::create([
                'complaint_id' => $complaint->id,
                'user_id' => $userId,
                'respondent_email' => $validated['respondent_email'],
                'case_number' => $complaint->case_number,
                'venue_legal_name' => $validated['venue_legal_name'],
                'venue_city_state' => $validated['venue_city_state'],
                'respondent_name' => $validated['respondent_name'],
                'complaint_date' => $validated['complaint_date'],
                'respondent_side_story' => $validated['respondent_side_story'],
                'issue_detail_description' => $validated['issue_detail_description'],
                'witnesses_information' => $validated['witnesses_information'],
                'supporting_evidence_type' => $validated['supporting_evidence_type'],
                'evidence_description' => $validated['evidence_description'] ?? null,
                'has_supporting_evidence' => $validated['supporting_evidence_type'] !== 'none',
                'submitted_at' => now()
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments') && $validated['supporting_evidence_type'] !== 'none') {
                $path = public_path('images/respondent-responses');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . Str::random(8) . '_' . $file->getClientOriginalName();
                    $file->move($path, $filename);
                    $filePath = 'images/respondent-responses/' . $filename;

                    Attachment::create([
                        'respondent_response_id' => $response->id,
                        'uploaded_by' => $userId,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'description' => 'Respondent supporting evidence'
                    ]);
                }
            }

            // Update complaint stage to "Respond by respondent" - only if next stage exists
            $responseStage = \App\Models\Stage::where('id', '>', $complaint->stage_id)->first();
            
            if ($responseStage) {
                $oldStageId = $complaint->stage_id;
                $complaint->update(['stage_id' => $responseStage->id]);
                
                // Log stage change
                StageChangeLog::logChange(
                    $complaint->id,
                    $oldStageId,
                    $responseStage->id,
                    'respondent_response_submitted',
                    'Respondent submitted detailed response to complaint',
                    [
                        'respondent_name' => $validated['respondent_name'],
                        'response_id' => $response->id,
                        'venue_name' => $validated['venue_legal_name'],
                        'has_evidence' => $validated['supporting_evidence_type'] !== 'none'
                    ]
                );
            } else {
                \Log::warning('No next stage found for complaint', [
                    'complaint_id' => $complaint->id,
                    'current_stage_id' => $complaint->stage_id
                ]);
            }


            // Mark respondent as responded in the ComplaintRespondent table
            $complaintRespondent = ComplaintRespondent::where('complaint_id', $complaint->id)
                ->where('user_id', $userId)
                ->first();

            if ($complaintRespondent) {
                $complaintRespondent->update(['responded_at' => now()]);
            }

            \DB::commit();
            
            \Log::info('Respondent response saved successfully', [
                'complaint_id' => $complaint->id,
                'response_id' => $response->id,
                'user_id' => $userId
            ]);

            // Send email notifications (don't fail the request if email fails)
            try {
                $this->sendResponseNotifications($complaint, $response);
            } catch (\Exception $e) {
                \Log::error('Failed to send email notifications: ' . $e->getMessage());
            }

            return redirect()->route('respondent.complaint.view', $complaint)
                ->with('success', 'Your detailed response has been submitted successfully. Administrators and the complainant have been notified.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error submitting respondent response: ' . $e->getMessage(), [
                'complaint_id' => $complaint->id,
                'user_id' => session('respondent_user_id'),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->withInput()
                ->with('error', 'Error submitting response: ' . $e->getMessage());
        }
    }

    private function sendResponseNotifications(Complaint $complaint, RespondentResponseDetail $response)
    {
        try {
            // Load complaint with attachments for email (exclude respondent response attachments)
            $complaint->load(['attachments' => function($query) {
                $query->whereNull('respondent_response_id');
            }]);

            // Send email to complainant if email exists
            if (!empty($complaint->email)) {
                \Mail::to($complaint->email)->send(new \App\Mail\RespondentResponseSubmitted($complaint, $response, false));
            }

            // Send email to admin users
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                \Mail::to($admin->email)->send(new \App\Mail\RespondentResponseSubmitted($complaint, $response, true));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending response notification emails: ' . $e->getMessage());
            // Don't fail the request if email sending fails
        }
    }

    public function profile()
    {
        $user = User::findOrFail(session('respondent_user_id'));
        return view('respondent.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . session('respondent_user_id'),
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail(session('respondent_user_id'));
        $user->update($validated);

        return redirect()->route('respondent.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function logout()
    {
        // Clear all respondent session data
        session()->forget([
            'respondent_complaint_id',
            'respondent_user_id',
            'complaint_number',
            'respondent_authenticated',
            'respondent_access_token'
        ]);

        return redirect()->route('respondent.login')
            ->with('success', 'You have been logged out successfully.');
    }

    private function createAccessToken($userId, $complaintId)
    {
        $token = Str::random(32);

        // Store or update access token in database (you may want to create a table for this)
        RespondentAccess::updateOrCreate([
            'user_id' => $userId,
            'complaint_id' => $complaintId,
        ], [
            'access_token' => $token,
            'expires_at' => now()->addDays(30), // Token expires in 30 days
        ]);

        return $token;
    }
}