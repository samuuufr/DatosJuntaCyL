@extends('diseños.panel')

@section('titulo', 'Registro')

@section('contenido')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background-color: var(--bg-primary);">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-4 transform hover:scale-105 transition-transform duration-300" style="background-color: var(--primary-color);">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-2" style="color: var(--text-primary);">
                Únete a Nosotros
            </h1>
            <p style="color: var(--text-secondary);">Crea tu cuenta en Demografía CyL</p>
        </div>

        <!-- Card Principal -->
        <div class="rounded-2xl p-8 transform hover:scale-[1.01] transition-all duration-300" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('registro.post') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nombre -->
                <div class="group">
                    <label for="nombre" class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                        Nombre Completo
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 transition-colors" style="color: var(--text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            required
                            class="w-full pr-4 py-3 rounded-lg transition-all duration-200"
                            style="background-color: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); padding-left: 3rem;"
                            onfocus="this.style.borderColor='var(--primary-color)'; this.style.outline='none';"
                            onblur="this.style.borderColor='var(--border-color)';"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 transition-colors" style="color: var(--text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full pr-4 py-3 rounded-lg transition-all duration-200"
                            style="background-color: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); padding-left: 3rem;"
                            onfocus="this.style.borderColor='var(--primary-color)'; this.style.outline='none';"
                            onblur="this.style.borderColor='var(--border-color)';"
                        >
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 transition-colors" style="color: var(--text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full pr-4 py-3 rounded-lg transition-all duration-200"
                            style="background-color: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); padding-left: 3rem;"
                            onfocus="this.style.borderColor='var(--primary-color)'; this.style.outline='none';"
                            onblur="this.style.borderColor='var(--border-color)';"
                        >
                    </div>
                    <p class="mt-2 text-xs" style="color: var(--text-secondary);">Usa al menos 6 caracteres</p>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                        Confirmar Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 transition-colors" style="color: var(--text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="w-full pr-4 py-3 rounded-lg transition-all duration-200"
                            style="background-color: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); padding-left: 3rem;"
                            onfocus="this.style.borderColor='var(--primary-color)'; this.style.outline='none';"
                            onblur="this.style.borderColor='var(--border-color)';"
                        >
                    </div>
                </div>

                <!-- Cloudflare Turnstile -->
                <div class="group mt-2">
                    <label class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                        Verificación de Seguridad
                    </label>
                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-theme="light"></div>
                    </div>
                </div>

                <!-- Botón Submit -->
                <button
                    type="submit"
                    class="w-full text-white font-bold py-3.5 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 mt-8"
                    style="background-color: var(--primary-color);"
                    onmouseover="this.style.backgroundColor='var(--primary-dark)';"
                    onmouseout="this.style.backgroundColor='var(--primary-color)';"
                >
                    Crear Cuenta
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full" style="border-top: 1px solid var(--border-color);"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4" style="background-color: var(--bg-secondary); color: var(--text-secondary);">
                        ¿Ya tienes una cuenta?
                    </span>
                </div>
            </div>

            <!-- Link Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full px-4 py-3 font-semibold rounded-lg transition-all duration-200 group"
                   style="border: 1px solid var(--border-color); color: var(--text-primary);"
                   onmouseover="this.style.backgroundColor='var(--bg-tertiary)';"
                   onmouseout="this.style.backgroundColor='transparent';">
                    <svg class="mr-2 w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    <span>Iniciar sesión</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-sm" style="color: var(--text-secondary);">
            Demografía CyL - Datos Abiertos de Castilla y León
        </p>
    </div>
</div>

<!-- Cloudflare Turnstile Script -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endsection