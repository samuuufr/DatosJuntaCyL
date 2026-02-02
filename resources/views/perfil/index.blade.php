@extends('diseños.panel')

@section('titulo', 'Mi Perfil')

@section('css_adicional')
<style>
    .perfil-input {
        width: 100%;
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .perfil-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(139, 26, 63, 0.1);
    }
    .perfil-input:disabled {
        background-color: var(--bg-tertiary);
        color: var(--text-secondary);
        cursor: not-allowed;
    }
    .perfil-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('contenido')
<div class="max-w-4xl mx-auto mt-8">
    <h1 class="text-3xl font-bold mb-8" style="color: var(--text-primary);">Mi Perfil</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información del perfil -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">
                Información Personal
            </h2>

            <form action="{{ route('perfil.actualizar') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nombre" class="perfil-label">
                        Nombre
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $usuario->nombre) }}"
                        required
                        class="perfil-input"
                    >
                </div>

                <div class="mb-4">
                    <label for="email" class="perfil-label">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $usuario->email) }}"
                        required
                        class="perfil-input"
                    >
                </div>

                <div class="mb-4">
                    <label class="perfil-label">
                        Rol
                    </label>
                    <input
                        type="text"
                        value="{{ ucfirst($usuario->rol) }}"
                        disabled
                        class="perfil-input"
                    >
                </div>

                <div class="mb-4">
                    <label class="perfil-label">
                        Miembro desde
                    </label>
                    <input
                        type="text"
                        value="{{ $usuario->fecha_registro->format('d/m/Y') }}"
                        disabled
                        class="perfil-input"
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-full"
                    style="margin-top: 1rem"
                >
                    Actualizar Información
                </button>
            </form>
        </div>

        <!-- Cambiar contraseña -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">
                Cambiar Contraseña
            </h2>

            <form action="{{ route('perfil.actualizar-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="password_actual" class="perfil-label">
                        Contraseña Actual
                    </label>
                    <input
                        type="password"
                        id="password_actual"
                        name="password_actual"
                        required
                        class="perfil-input"
                        placeholder="••••••••"
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="perfil-label">
                        Nueva Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="perfil-input"
                        placeholder="Mínimo 6 caracteres"
                    >
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="perfil-label">
                        Confirmar Nueva Contraseña
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="perfil-input"
                        placeholder="Repite la contraseña"
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-full"
                    style="margin-top: 1rem"
                >
                    Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>

    <!-- Enlace a favoritos -->
    <div class="mt-8 text-center">
        <a
            href="{{ route('perfil.favoritos') }}"
            class="inline-block font-semibold py-3 px-6 rounded-lg transition-colors duration-200"
            style="background-color: #36902a; color: white; padding-left: 1rem; padding-right: 1rem; margin-top: 1rem;"
            onmouseover="this.style.backgroundColor='#059669'"
            onmouseout="this.style.backgroundColor='#4ab73b'"
        >
              Municipios Favoritos
        </a>
    </div>
</div>
@endsection
