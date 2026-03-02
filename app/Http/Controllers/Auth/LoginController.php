<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Handle a successful authentication attempt.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'respondent') {
            return redirect()->route('cast_member.dashboard');
        }
        
        return redirect()->route('welcome');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the general login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the guest login form.
     *
     * @return \Illuminate\View\View
     */
    public function showGuestLoginForm()
    {
        return view('auth.login', ['role' => 'guest']);
    }

    /**
     * Show the cast member login form.
     *
     * @return \Illuminate\View\View
     */
    public function showCastMemberLoginForm()
    {
        return view('auth.login', ['role' => 'respondent']);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            $role = $request->input('role', 'guest');

            // Check if user has the correct role
            if ($role && $user->role !== $role && $user->role !== 'admin') {
                Auth::logout();
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'These credentials do not match our records for the selected role.']);
            }

            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the post-login redirect path for the user.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'cast_member':
                return route('cast_member.dashboard');
            case 'guest':
                return route('complaints.create');
            default:
                return route('welcome');
        }
    }
}
