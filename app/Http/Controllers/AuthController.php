<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $usuariosPrueba = User::with('roles')->get();

        return view('auth.login', compact('usuariosPrueba'));
    }

    /**
     * Procesa la autenticación del usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regla: un usuario desactivado no puede entrar aunque la contraseña sea correcta
            if (! Auth::user()->active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Su usuario está inactivo. Contacte al administrador.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            // Cada inicio de sesión vuelve a preguntar el rol (si el usuario tiene varios)
            $request->session()->forget('active_role');

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Sesión iniciada correctamente como ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión activa.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión exitosamente.');
    }

    /**
     * Inicio rápido de sesión para pruebas de roles.
     */
    public function quickLogin(User $user, Request $request)
    {
        if (! $user->active) {
            return back()->withErrors([
                'email' => 'Su usuario está inactivo. Contacte al administrador.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('active_role');

        return redirect()->route('dashboard')
            ->with('success', "Has iniciado sesión rápidamente como {$user->name} (" . ($user->roles->first()?->name ?? 'sin rol') . ")");
    }
}
