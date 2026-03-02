<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicComplaintController extends Controller
{
    public function create()
    {
        return view('public.complaint');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'issue_type' => 'required|string|in:service,product,staff,facility,other',
            'date_of_experience' => 'required|date',
            'description' => 'required|string|min:10',
            'location' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $complaint = new Complaint();
        $complaint->case_number = 'CASE-' . strtoupper(Str::random(8));
        $complaint->name = $request->name;
        $complaint->email = $request->email;
        $complaint->phone_number = $request->phone_number;
        $complaint->complaint_type = $request->issue_type;
        $complaint->date_of_experience = $request->date_of_experience;
        $complaint->description = $request->description;
        $complaint->location = $request->location;
        $complaint->status = 'submitted';
        $complaint->submitted_at = now();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'public');
            $complaint->attachment_path = $path;
        }

        $complaint->save();

        return redirect()->back()->with([
            'success' => '  ',
            'case_number' => $complaint->case_number
        ]);
    }

    public function track(Request $request)
    {
        $statusLabels = [
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'resolved' => 'Resolved',
            'closed' => 'Closed'
        ];

        if ($request->has('case_number')) {
            $complaint = Complaint::with([
                'latestResolution.signatures.user',
                'respondents.user',
                'investigationLogs.creator',
                'attachments'
            ])->where('case_number', $request->case_number)->first();

            if (!$complaint) {
                return back()->with('error', 'No complaint found with this case number.');
            }

            return view('public.track', compact('complaint', 'statusLabels'));
        }

        return view('public.track', compact('statusLabels'));
    }
}
