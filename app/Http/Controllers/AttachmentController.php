<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function destroy(Attachment $attachment)
    {
        // Get the complaint ID before deleting the attachment
        $complaintId = $attachment->complaint_id;

        // Check if the file exists in storage
        if (Storage::exists('public/attachments/' . basename($attachment->file_path))) {
            // Delete the file from storage
            Storage::delete('public/attachments/' . basename($attachment->file_path));
        }

        // Delete the attachment record
        $attachment = Attachment::find($attachment->id);
        $attachment->delete();

        return redirect()->route('admin.complaints.edit', $complaintId)
                         ->with('success', 'Attachment deleted successfully.');
    }
}
