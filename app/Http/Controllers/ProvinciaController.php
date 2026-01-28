<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\DatoMnp;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    /**
     * Muestra el listado de todas las provincias
     */
    public function index()
    {
        $provincias = Provincia::withCount('municipios')->get();

        // Obtener estadísticas para cada provincia
        $provincias->transform(function ($provincia) {
            $datosMnp = $provincia->datosMnp;

            $estadisticas = [
                'nacimientos' => $datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
                'defunciones' => $datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
                'matrimonios' => $datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
            ];

            $estadisticas['crecimiento_vegetativo'] =
                $estadisticas['nacimientos'] - $estadisticas['defunciones'];

            $provincia->estadisticas = $estadisticas;

            return $provincia;
        });

        return view('provincias.index', compact('provincias'));
    }

    /**
     * API: Obtiene datos de una provincia para gráficos
     */
    public function datos($id)
    {
        $provincia = Provincia::with('municipios')->findOrFail($id);

        // Obtener evolución temporal por año
        $evolucion = DatoMnp::whereHas('municipio', function ($query) use ($provincia) {
                $query->where('provincia_id', $provincia->id);
            })
            ->selectRaw('anno, tipo_evento, SUM(valor) as total')
            ->groupBy('anno', 'tipo_evento')
            ->orderBy('anno')
            ->get()
            ->groupBy('anno')
            ->map(function ($datos, $anno) {
                return [
                    'anno' => $anno,
                    'nacimientos' => $datos->where('tipo_evento', 'nacimiento')->first()?->total ?? 0,
                    'defunciones' => $datos->where('tipo_evento', 'defuncion')->first()?->total ?? 0,
                    'matrimonios' => $datos->where('tipo_evento', 'matrimonio')->first()?->total ?? 0,
                ];
            })
            ->values();

        // Top 5 municipios por nacimientos
        $topMunicipios = $provincia->municipios()
            ->withSum(['datosMnp as total_nacimientos' => function ($query) {
                $query->where('tipo_evento', 'nacimiento');
            }], 'valor')
            ->orderByDesc('total_nacimientos')
            ->limit(5)
            ->get()
            ->map(function ($municipio) {
                return [
                    'nombre' => $municipio->nombre,
                    'total' => $municipio->total_nacimientos ?? 0,
                ];
            });

        return response()->json([
            'provincia' => [
                'id' => $provincia->id,
                'nombre' => $provincia->nombre,
                'municipios_count' => $provincia->municipios->count(),
            ],
            'evolucion' => $evolucion,
            'top_municipios' => $topMunicipios,
        ]);
    }

    /**
     * API: Obtiene resumen de todas las provincias para comparación
     */
    public function resumen()
    {
        $provincias = Provincia::all()->map(function ($provincia) {
            $datosMnp = $provincia->datosMnp;

            return [
                'id' => $provincia->id,
                'nombre' => $provincia->nombre,
                'municipios_count' => $provincia->municipios()->count(),
                'nacimientos' => $datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
                'defunciones' => $datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
                'matrimonios' => $datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
            ];
        });

        return response()->json($provincias);
    }
}
