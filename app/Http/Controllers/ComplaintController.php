<?php
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Complaint;
use App\Models\ComplaintRespondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name'           => 'required|string|max:255',
                'email'          => 'required|email',
                'phone_number'   => 'nullable|string',
                'location'       => 'required|string',
                'complaint_type' => 'required|string',
                'description'    => 'required|string',
                'attachments.*'  => 'nullable|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mov,avi,wmv',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            $complaint                 = new Complaint();
            $complaint->name           = $validated['name'];
            $complaint->email          = $validated['email'];
            $complaint->phone_number   = $validated['phone_number'];
            $complaint->location       = $validated['location'];
            $complaint->complaint_type = $validated['complaint_type'];
            $complaint->description    = $validated['description'];
            $complaint->anonymity      = $request->anonymity;
            $complaint->case_number    = 'COMP-' . strtoupper(Str::random(8));

            if ($request->has('submitted_by_admin') && $request->submitted_by_admin) {
                $complaint->submitted_by_admin    = true;
                $complaint->submitted_by_admin_id = $validated['submitted_by_admin_id'];
            } else {
                $complaint->user_id = Auth::id();
            }

            $complaint->stage_id = 1; // Set to first stage
            $complaint->save();

            if ($complaint->user_id) {
                ComplaintRespondent::create([
                    'complaint_id' => $complaint->id,
                    'user_id'      => $complaint->user_id,
                ]);
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path     = $file->storeAs('images/complaint-images', $filename, 'public');

                    Attachment::create([
                        'complaint_id' => $complaint->id,
                        'file_path'    => $path,
                        'file_type'    => $file->getClientOriginalExtension(),
                        'description'  => null,
                    ]);
                }
            }

            return redirect()->route('cast_member.complaints')
                ->with('success', 'Complaint created successfully with case number: ' . $complaint->case_number);
        } catch (\Exception $e) {
            // Log the error if needed
            dd($e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong while submitting the complaint.');
        }
    }

    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        return view('complaints.edit', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'email'          => 'required|email',
                'phone_number'   => 'nullable|string',
                'location'       => 'required|string',
                'complaint_type' => 'required|string',
                'description'    => 'required|string',
                'attachments.*'  => 'nullable|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mov,avi,wmv',
            ]);

            $complaint->name           = $validated['name'];
            $complaint->email          = $validated['email'];
            $complaint->phone_number   = $validated['phone_number'];
            $complaint->location       = $validated['location'];
            $complaint->complaint_type = $validated['complaint_type'];
            $complaint->description    = $validated['description'];
            $complaint->anonymity      = $request->anonymity;

            if ($request->hasFile('attachments')) {
                // Delete old attachments
                foreach ($complaint->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }

                // Upload new attachments
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path     = $file->storeAs('images/complaint-images', $filename, 'public');

                    Attachment::create([
                        'complaint_id' => $complaint->id,
                        'file_path'    => $path,
                        'file_type'    => $file->getClientOriginalExtension(),
                        'description'  => null,
                    ]);
                }
            }

            $complaint->save();
            ComplaintRespondent::create([
                'complaint_id' => $complaint->id,
                'user_id'      => Auth::user()->id,
            ]);

            return redirect()->route('cast_member.complaints')
                ->with('success', 'Complaint updated successfully');

        } catch (\Exception $e) {
            // Log the error if needed
            dd($e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong while updating the complaint.');
        }
    }

    public function destroy(Complaint $complaint)
    {
        $this->authorize('delete', $complaint);

        // Delete all attachments
        foreach ($complaint->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $complaint->delete();

        return redirect()->route('admin.complaints')
            ->with('success', 'Complaint deleted successfully');
    }
}
