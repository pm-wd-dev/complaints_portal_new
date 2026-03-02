<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\InvestigationLog;
use App\Models\Attachment;
use App\Models\ComplaintResponse;
use App\Models\ComplaintRespondent;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CastMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('cast.member');
    }

    public function dashboard()
    {
        $user = auth()->user();

        // Get base query for complaints where user is a respondent
        $complaintsQuery = Complaint::whereHas('respondents', function($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        // Calculate statistics
        // Get counts using separate queries to avoid query issues
        $totalAssigned = (clone $complaintsQuery)->count();
        $inProgress = (clone $complaintsQuery)->where('status', 'escalated')->count();
        $resolved = (clone $complaintsQuery)->where('status', 'resolved')->count();

        // Get 5 most recent complaints
        $recentComplaints = $complaintsQuery
            ->with('latestResolution.signatures.user')
            ->select(['id', 'case_number', 'complaint_type', 'description', 'status', 'created_at','location'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($complaint) {
                return [
                    'id' => $complaint->id,
                    'case_number' => $complaint->case_number,
                    'issue_type' => $complaint->complaint_type,
                    'description' => $complaint->description,
                    'status' => $complaint->status,
                    'display_status' => $complaint->display_status,
                    'created_at' => $complaint->created_at,
                    'location'=>$complaint->location
                ];
            });

        return view('cast_member.dashboard', compact('totalAssigned', 'inProgress', 'resolved', 'recentComplaints'));
    }

    public function complaints(Request $request)
    {
        $user = auth()->user();
        $status = $request->input('status');
        $sort = $request->input('sort', 'newest');

        // Base query for complaints where user is a respondent
        $query = Complaint::whereHas('respondents', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['user', 'adminUser', 'respondents', 'latestResolution.signatures.user']);

        // Apply status filter if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Apply sorting
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        // Get paginated results
        $complaints = $query->paginate(5)->withQueryString();

        // Get total count before any filters
        $totalCount = Complaint::whereHas('respondents', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        // Get counts for different statuses
        $counts = [
            'all' => $totalCount,
            'escalated' => Complaint::whereHas('respondents', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'escalated')->count(),
            'under_review' => Complaint::whereHas('respondents', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'under_review')->count(),
            'resolved' => Complaint::whereHas('respondents', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'resolved')->count()
        ];

        return view('cast_member.complaints', compact('complaints', 'counts', 'status', 'sort'));
    }

    public function documents()
    {
        return view('cast_member.documents');
    }

    public function settings()
    {
        return view('cast_member.settings');
    }

    public function downloadAttachment(Attachment $attachment)
    {
        // Check if the user has access to this attachment through a complaint
        $hasAccess = $attachment->complaint->respondents()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        return Storage::download($attachment->path, $attachment->original_name);
    }

    public function showComplaint(Complaint $complaint)
    {
        // Get the complaint respondent record for this user
        $complaintRespondent = ComplaintRespondent::where('complaint_id', $complaint->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$complaintRespondent) {
            abort(403);
        }

        // Load all responses for this respondent
        $responses = $complaintRespondent->responses()
            ->orderBy('created_at', 'desc')
            ->get();

        $complaint->loadMissing([
            'adminUser:id',
            'respondents:id',
            'investigationLogs' => function($query) {
                $query->latest()->select('id', 'complaint_id', 'note', 'next_steps', 'created_at', 'created_by');
            },
            'investigationLogs.creator:id',
            'attachments' => function($query) {
                $query->whereNull('respondent_response_id');
            },
            'latestResolution.signatures.user'
        ]);

        return view('cast_member.complaints.show', compact('complaint', 'responses', 'complaintRespondent'));
    }

    public function respond(Request $request, Complaint $complaint)
    {
        // Check if the complaint is assigned to this cast member
        $complaintRespondent = ComplaintRespondent::where('complaint_id', $complaint->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$complaintRespondent) {
            abort(403);
        }

        // Validate request
        $validated = $request->validate([
            'response' => 'required|string|min:10',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
            'status' => 'required|in:submitted,under_review,escalated,resolved'
        ]);

        // Create new response
        $response = new ComplaintResponse([
            'complaint_respondent_id' => $complaintRespondent->id,
            'response' => $validated['response'],
            'responded_at' => Carbon::now()
        ]);

        // Save the response
        $complaintRespondent->responses()->save($response);

        // Update complaint status
        $complaint->update([
            'status' => $validated['status']
        ]);

        // Handle attachment if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            if ($file->isValid()) {
                try {
                    // Get file details before moving
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientOriginalExtension();
                    $size = $file->getSize();

                    // Create complaint-images directory if it doesn't exist
                    $path = public_path('images/complaint-images');
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    // Generate filename and move file
                    $fileName = time() . '_' . $originalName;
                    $file->move($path, $fileName);
                    $filePath = 'images/complaint-images/' . $fileName;

                    // Create new attachment record
                    $attachment = new Attachment([
                        'file_path' => $filePath,
                        'original_name' => $originalName,
                        'file_type' => $mimeType,
                        'file_size' => $size,
                        'uploaded_by' => auth()->id()
                    ]);

                    // Associate attachment with complaint
                    $complaint->attachments()->save($attachment);
                } catch (\Exception $e) {
                    \Log::error('Error processing file upload in cast member response: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error uploading file. Please try again.');
                }
            } else {
                \Log::error('Invalid file upload detected in cast member response');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The uploaded file is invalid. Please try again.');
            }
        }

        return redirect()->route('cast_member.complaints')
            ->with('success', 'Response added successfully.');
    }
}
