<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Attempt the login
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Safe explicit redirect check
            $redirectTo = $this->safeRedirectPath($request->input('redirect'));
            if ($redirectTo) {
                if (str_starts_with($redirectTo, '/admin') && $user->role !== 'admin') {
                    $redirectTo = null;
                } else {
                    return redirect($redirectTo);
                }
            }

            // Check intended URL if available
            $intended = session()->get('url.intended');
            if ($intended) {
                $safeIntended = $this->safeRedirectPath($intended);
                if ($safeIntended) {
                    if (str_starts_with($safeIntended, '/admin') && $user->role !== 'admin') {
                        // ignore admin intended for non-admin
                    } else {
                        return redirect()->intended(
                            $user->role === 'admin' ? route('admin.dashboard') : route('dashboard')
                        );
                    }
                }
            }

            // Redirect automatically based on user role: admin -> admin dashboard, customer -> user dashboard
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('dashboard');
            }
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->withInput($request->only('email', 'redirect'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create new user with 'user' role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Default role for new registrations
        ]);

        // Auto-login the user after registration
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to Google for authentication
     */
    public function redirectToGoogle(Request $request)
    {
        $redirectTo = $this->safeRedirectPath($request->query('redirect'));
        if ($redirectTo) {
            $request->session()->put('login.redirect_to', $redirectTo);
        } else {
            $request->session()->forget('login.redirect_to');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google authentication callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update user with Google info if needed
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            } else {
                // Create new user (default to user role)
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getEmail(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'password' => Hash::make(uniqid()), // Random password
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            $redirectTo = $this->safeRedirectPath($request->session()->pull('login.redirect_to'));
            if ($redirectTo) {
                if (str_starts_with($redirectTo, '/admin') && $user->role !== 'admin') {
                    $redirectTo = null;
                } else {
                    return redirect($redirectTo);
                }
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            $redirectTo = $this->safeRedirectPath($request->session()->get('login.redirect_to'));

            return redirect()
                ->route('login', $redirectTo ? ['redirect' => $redirectTo] : [])
                ->withErrors(['google' => 'Unable to login with Google. Please try again.']);
        }
    }

    private function safeRedirectPath(?string $redirect): ?string
    {
        if (!$redirect) {
            return null;
        }

        $redirect = trim($redirect);

        if (
            $redirect === '' ||
            !str_starts_with($redirect, '/') ||
            str_starts_with($redirect, '//') ||
            str_contains($redirect, "\n") ||
            str_contains($redirect, "\r")
        ) {
            return null;
        }

        return $redirect;
    }
}
