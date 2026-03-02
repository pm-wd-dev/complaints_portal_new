<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintRespondentController extends Controller
{
    /**
     * Assign respondents to a complaint
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $request->validate([
            'respondent_ids' => 'required|array',
            'respondent_ids.*' => 'exists:users,id'
        ]);

        // Verify all users are respondents
        $respondents = User::whereIn('id', $request->respondent_ids)
                          ->where('role', 'respondent')
                          ->get();

        if ($respondents->count() !== count($request->respondent_ids)) {
            return back()->with('error', 'Some selected users are not respondents');
        }

        // Sync the respondents
        $complaint->respondents()->sync($request->respondent_ids);

        return back()->with('success', 'Respondents assigned successfully');
    }

    /**
     * Remove a respondent from a complaint
     */
    public function remove(Complaint $complaint, User $respondent)
    {
        if ($respondent->role !== 'respondent') {
            return back()->with('error', 'Selected user is not a respondent');
        }

        $complaint->respondents()->detach($respondent->id);
        return back()->with('success', 'Respondent removed successfully');
    }
}
