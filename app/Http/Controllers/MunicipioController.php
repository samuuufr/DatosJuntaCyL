<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Provincia;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    /**
     * Muestra la página de búsqueda de municipios
     */
    public function index(Request $request)
    {
        $provincias = Provincia::orderBy('nombre')->get();
        $municipios = collect();

        // Si hay búsqueda o filtro, cargar resultados
        if ($request->has('buscar') || $request->has('provincia_id')) {
            $query = Municipio::with('provincia');

            if ($request->filled('buscar')) {
                $termino = $request->buscar;
                $query->where('nombre', 'LIKE', "%{$termino}%");
            }

            if ($request->filled('provincia_id')) {
                $query->where('provincia_id', $request->provincia_id);
            }

            $municipios = $query->orderBy('nombre')->paginate(20);
        }

        return view('municipios.index', compact('provincias', 'municipios'));
    }

    /**
     * API: Buscar municipios por nombre (para autocompletado)
     */
    public function buscar(Request $request)
    {
        $termino = $request->input('q', '');
        $provinciaId = $request->input('provincia_id');

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $query = Municipio::with('provincia')
            ->where('nombre', 'LIKE', "%{$termino}%");

        // Si se proporciona provincia_id, filtrar por esa provincia
        if ($provinciaId) {
            $query->where('provincia_id', $provinciaId);
        }

        $municipios = $query->limit(10)
            ->get()
            ->map(function ($municipio) {
                return [
                    'id' => $municipio->id,
                    'nombre' => $municipio->nombre,
                    'provincia' => $municipio->provincia->nombre,
                    'codigo_ine' => $municipio->codigo_ine,
                    'url' => route('analisis-demografico.municipio-detalle', $municipio->id),
                ];
            });

        return response()->json($municipios);
    }

    /**
     * API: Filtrar municipios por provincia
     */
    public function porProvincia($provinciaId)
    {
        $municipios = Municipio::where('provincia_id', $provinciaId)
            ->orderBy('nombre')
            ->get()
            ->map(function ($municipio) {
                return [
                    'id' => $municipio->id,
                    'nombre' => $municipio->nombre,
                    'codigo_ine' => $municipio->codigo_ine,
                    'registros_mnp' => $municipio->datosMnp()->count(),
                ];
            });

        return response()->json($municipios);
    }

    /**
     * API: Obtener datos de un municipio para gráficos
     */
    public function datos($id)
    {
        $municipio = Municipio::with('provincia', 'datosMnp')->findOrFail($id);

        // Evolución temporal
        $evolucion = $municipio->datosMnp
            ->groupBy('anno')
            ->map(function ($datos, $anno) {
                return [
                    'anno' => $anno,
                    'nacimientos' => $datos->where('tipo_evento', 'nacimiento')->first()?->valor ?? 0,
                    'defunciones' => $datos->where('tipo_evento', 'defuncion')->first()?->valor ?? 0,
                    'matrimonios' => $datos->where('tipo_evento', 'matrimonio')->first()?->valor ?? 0,
                ];
            })
            ->sortBy('anno')
            ->values();

        // Totales por tipo
        $totales = [
            'nacimientos' => $municipio->datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
            'defunciones' => $municipio->datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
            'matrimonios' => $municipio->datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
        ];

        return response()->json([
            'municipio' => [
                'id' => $municipio->id,
                'nombre' => $municipio->nombre,
                'provincia' => $municipio->provincia->nombre,
                'codigo_ine' => $municipio->codigo_ine,
            ],
            'evolucion' => $evolucion,
            'totales' => $totales,
        ]);
    }
}
