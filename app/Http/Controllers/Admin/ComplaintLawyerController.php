<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintLawyerController extends Controller
{
    /**
     * Assign lawyers to a complaint
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $request->validate([
            'lawyer_ids' => 'required|array',
            'lawyer_ids.*' => 'exists:users,id'
        ]);

        // Verify all users are lawyers
        $lawyers = User::whereIn('id', $request->lawyer_ids)
                      ->where('role', 'lawyer')
                      ->get();

        if ($lawyers->count() !== count($request->lawyer_ids)) {
            return back()->with('error', 'Some selected users are not lawyers');
        }

        // Sync the lawyers
        $complaint->lawyers()->sync($request->lawyer_ids);

        return back()->with('success', 'Lawyers assigned successfully');
    }

    /**
     * Remove a lawyer from a complaint
     */
    public function remove(Complaint $complaint, User $lawyer)
    {
        if ($lawyer->role !== 'lawyer') {
            return back()->with('error', 'Selected user is not a lawyer');
        }

        $complaint->lawyers()->detach($lawyer->id);
        return back()->with('success', 'Lawyer removed successfully');
    }
}
