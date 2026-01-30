<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Valida el token de Cloudflare Turnstile
     */
    private function validarTurnstile($token)
    {
        if (empty($token)) {
            return false;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET_KEY'),
            'response' => $token,
        ]);

        $result = $response->json();

        return isset($result['success']) && $result['success'] === true;
    }

    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'cf-turnstile-response' => 'required',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'cf-turnstile-response.required' => 'Debes completar la verificación de seguridad',
        ]);

        // Validar Turnstile
        if (!$this->validarTurnstile($request->input('cf-turnstile-response'))) {
            throw ValidationException::withMessages([
                'captcha' => ['La verificación de seguridad falló. Por favor, intenta nuevamente.'],
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('analisis-demografico.panel'))
                ->with('success', '¡Bienvenido de nuevo, ' . Auth::user()->nombre . '!');
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas no son correctas.'],
        ]);
    }

    /**
     * Muestra el formulario de registro
     */
    public function mostrarRegistro()
    {
        return view('auth.registro');
    }

    /**
     * Procesa el registro de un nuevo usuario
     */
    public function registro(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'cf-turnstile-response' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'cf-turnstile-response.required' => 'Debes completar la verificación de seguridad',
        ]);

        // Validar Turnstile
        if (!$this->validarTurnstile($request->input('cf-turnstile-response'))) {
            throw ValidationException::withMessages([
                'captcha' => ['La verificación de seguridad falló. Por favor, intenta nuevamente.'],
            ]);
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'usuario',
            'fecha_registro' => now(),
        ]);

        Auth::login($usuario);

        return redirect()->route('analisis-demografico.panel')
            ->with('success', '¡Cuenta creada exitosamente! Bienvenido, ' . $usuario->nombre . '.');
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio')
            ->with('success', 'Sesión cerrada correctamente');
    }
}
