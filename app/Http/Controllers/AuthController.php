<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Genera un captcha matemático y lo almacena en sesión
     */
    private function generarCaptcha()
    {
        $numero1 = rand(1, 10);
        $numero2 = rand(1, 10);
        $operacion = rand(0, 1) ? '+' : '-';

        if ($operacion === '-' && $numero1 < $numero2) {
            // Asegurar que no haya resultados negativos
            $temp = $numero1;
            $numero1 = $numero2;
            $numero2 = $temp;
        }

        $respuesta = $operacion === '+' ? $numero1 + $numero2 : $numero1 - $numero2;

        session([
            'captcha_pregunta' => "$numero1 $operacion $numero2",
            'captcha_respuesta' => $respuesta
        ]);

        return "$numero1 $operacion $numero2";
    }

    /**
     * Valida el captcha ingresado por el usuario
     */
    private function validarCaptcha($respuestaUsuario)
    {
        $respuestaCorrecta = session('captcha_respuesta');

        // Limpiar la sesión del captcha después de validar
        session()->forget(['captcha_pregunta', 'captcha_respuesta']);

        return $respuestaUsuario != null && (int)$respuestaUsuario === (int)$respuestaCorrecta;
    }

    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin()
    {
        $captcha = $this->generarCaptcha();
        return view('auth.login', compact('captcha'));
    }

    /**
     * Procesa el login del usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'captcha' => 'required|numeric',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'captcha.required' => 'Debes resolver el captcha',
            'captcha.numeric' => 'El captcha debe ser un número',
        ]);

        // Validar captcha
        if (!$this->validarCaptcha($request->captcha)) {
            throw ValidationException::withMessages([
                'captcha' => ['La respuesta del captcha es incorrecta. Intenta nuevamente.'],
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
        $captcha = $this->generarCaptcha();
        return view('auth.registro', compact('captcha'));
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
            'captcha' => 'required|numeric',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'captcha.required' => 'Debes resolver el captcha',
            'captcha.numeric' => 'El captcha debe ser un número',
        ]);

        // Validar captcha
        if (!$this->validarCaptcha($request->captcha)) {
            throw ValidationException::withMessages([
                'captcha' => ['La respuesta del captcha es incorrecta. Intenta nuevamente.'],
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
