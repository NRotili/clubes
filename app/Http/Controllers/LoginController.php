<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'El correo o la contraseña son incorrectos.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->rol === 'socio') {
            if ($user->socio_id) {
                return redirect()->route('socios.show', $user->socio_id);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta no tiene un perfil de socio vinculado. Contactá a la administración.',
            ])->onlyInput('email');
        }

        return redirect()->intended(route('socios.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
