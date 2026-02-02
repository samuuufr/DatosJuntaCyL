<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Muestra el perfil del usuario
     */
    public function mostrar()
    {
        $usuario = Auth::user();

        return view('perfil.index', compact('usuario'));
    }

    /**
     * Muestra los municipios favoritos del usuario
     */
    public function favoritos()
    {
        $usuario = Auth::user();

        // Cargar favoritos con relaciones para mostrar información completa
        $favoritos = $usuario->municipios()
            ->with('provincia')
            ->orderBy('nombre')
            ->get();

        return view('perfil.favoritos', compact('favoritos'));
    }

    /**
     * Actualiza la información del perfil
     */
    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está en uso',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->save();

        return redirect()->route('perfil.mostrar')
            ->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Actualiza la contraseña del usuario
     */
    public function actualizarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password_actual.required' => 'La contraseña actual es obligatoria',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $usuario = Auth::user();

        // Verificar que la contraseña actual es correcta
        if (!Hash::check($request->password_actual, $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        return redirect()->route('perfil.mostrar')
            ->with('success', 'Contraseña actualizada correctamente');
    }

    /**
     * Añade un municipio a favoritos (API)
     */
    public function agregarFavorito(Request $request)
    {
        $request->validate([
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $usuario = Auth::user();
        $municipioId = $request->municipio_id;
        $municipio = Municipio::find($municipioId);

        // Usar firstOrCreate para evitar duplicados (condiciones de carrera)
        $favorito = Favorito::firstOrCreate([
            'usuario_id' => $usuario->id,
            'municipio_id' => $municipioId,
        ]);

        // Si fue creado ahora o ya existía, retornar éxito
        return response()->json([
            'success' => true,
            'message' => $municipio->nombre . ' añadido a favoritos'
        ]);
    }

    /**
     * Elimina un municipio de favoritos (API)
     */
    public function eliminarFavorito(Request $request)
    {
        $request->validate([
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $usuario = Auth::user();
        $municipioId = $request->municipio_id;

        $favorito = Favorito::where('usuario_id', $usuario->id)
            ->where('municipio_id', $municipioId)
            ->first();

        if (!$favorito) {
            return response()->json([
                'success' => false,
                'message' => 'Este municipio no está en tus favoritos'
            ], 404);
        }

        $municipio = Municipio::find($municipioId);
        $favorito->delete();

        return response()->json([
            'success' => true,
            'message' => $municipio->nombre . ' eliminado de favoritos'
        ]);
    }

    /**
     * Verifica si un municipio es favorito (API)
     */
    public function esFavorito($municipioId)
    {
        $usuario = Auth::user();

        $esFavorito = Favorito::where('usuario_id', $usuario->id)
            ->where('municipio_id', $municipioId)
            ->exists();

        return response()->json([
            'esFavorito' => $esFavorito
        ]);
    }

    /**
     * Obtiene la lista de IDs de municipios favoritos del usuario (API)
     */
    public function listaFavoritosIds()
    {
        $usuario = Auth::user();

        $favoritosIds = $usuario->favoritos()
            ->pluck('municipio_id')
            ->toArray();

        return response()->json([
            'favoritos' => $favoritosIds
        ]);
    }

    /**
     * Obtiene la lista completa de municipios favoritos con datos (API)
     */
    public function listaFavoritosCompleta()
    {
        $usuario = Auth::user();

        $favoritos = $usuario->municipios()
            ->with('provincia')
            ->orderBy('nombre')
            ->get()
            ->map(function ($municipio) {
                return [
                    'id' => $municipio->id,
                    'nombre' => $municipio->nombre,
                    'codigo_ine' => $municipio->codigo_ine,
                    'provincia' => $municipio->provincia->nombre ?? ''
                ];
            });

        return response()->json([
            'favoritos' => $favoritos
        ]);
    }
}
