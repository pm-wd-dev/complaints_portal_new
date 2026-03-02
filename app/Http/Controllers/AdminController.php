<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        // Get counts for different statuses
        $openCount = Complaint::whereIn('status', ['submitted', 'under_review','escalated'])->count();
        $closedCount = Complaint::whereIn('status', ['closed', 'resolved'])->count();
        $users = User::where('role', '=', 'respondent')->get();

        // Get all complaints with basic info
        $complaints = Complaint::with([
            'user:id,name',  // Complainant
            'adminUser:id,name',  // Respondent/Admin
            'respondents.user:id,name',  // All respondents with their user information
            'latestResolution.signatures.user',  // Latest resolution with signatures and their users
            'stage'  // Stage relationship for display_status
        ])
        ->select(
            'id',
            'case_number',
            'description',
            'status',
            'stage_id',
            'created_at',
            'submitted_by_admin_id',
            'name',
            'location'
        )
        ->latest()
        ->take(5)
        ->get();

        return view('admin.dashboard', compact('openCount', 'closedCount', 'complaints','users'));
    }

    public function dashboard()
    {
        return $this->index();
    }

    public function complaints()
    {
        $complaints = Complaint::with('user')
                              ->latest()
                              ->paginate(10);
        return view('admin.complaints', compact('complaints'));
    }

    public function investigate(Complaint $complaint)
    {
        return view('admin.complaints.investigate', compact('complaint'));
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_progress', 'closed'])],
            'admin_notes' => ['required', 'string'],
        ]);

        $complaint->update($validated);

        return redirect()->route('admin.complaints')
                         ->with('success', 'Complaint status updated successfully');
    }

    public function users(Request $request)
    {

        $search = $request->input('search');

        $users = User::where('role', '!=', 'admin')
                     ->when($search, function($query) use ($search) {
                         $query->where(function($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                         });
                     })
                     ->latest()
                     ->paginate(5);

        if ($request->ajax()) {
            return response()->json([
                'users' => $users,
                'links' => $users->links()->toHtml()
            ]);
        }

        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['guest', 'cast_member'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users')
                         ->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['guest', 'cast_member'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')
                         ->with('success', 'User updated successfully');
    }

    public function destroyUser(User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.users')
                         ->with('success', 'User deleted successfully');
    }

    public function documents()
    {
        return view('admin.documents');
    }

    public function reports()
    {
        return view('admin.reports');
    }
}
