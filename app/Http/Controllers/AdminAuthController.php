<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\EcommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    protected $service;

    public function __construct(EcommerceService $service)
    {
        $this->service = $service;
    }
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // 1. Authenticate with Ecommerce Backend
        $loginResponse = $this->service->loginAdmin($credentials['email'], $credentials['password']);

        if ($loginResponse && $loginResponse['status'] === 'OK') {
            // 2. Ensure a local "shadow" admin user exists for Laravel Auth
            $admin = Admin::where('email', $credentials['email'])->first();

            if (!$admin) {
                // If it doesn't exist locally but login is OK in backend, create it
                // We don't necessarily need the password for local auth if we use login()
                $admin = Admin::create([
                    'name' => $loginResponse['payload']['admin']['userId']['firstname'] ?? 'Admin',
                    'email' => $credentials['email'],
                    'password' => $credentials['password'], // Hash is usually on model
                ]);
            }

            // 3. Log in locally
            Auth::guard('admin')->login($admin, $request->remember);
            
            $request->session()->regenerate();

            // Store backend user ID and admin ID AFTER regenerate to ensure persistence
            session([
                'ecommerce_user_id' => $loginResponse['payload']['admin']['userId']['_id'] ?? null,
                'ecommerce_admin_id' => $loginResponse['payload']['admin']['_id'] ?? null,
                'ecommerce_token' => $loginResponse['payload']['token'] ?? session('ecommerce_token')
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our ecommerce records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
