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

        // Nombre de la capital (generalmente coincide con el nombre de la provincia)
        $nombreCapital = $provincia->nombre;

        // Función para obtener top municipios por tipo
        $getTop = function ($relacion, $sumColumn, $excluirCapital = false) use ($provincia, $nombreCapital) {
            $query = $provincia->municipios()
                ->withSum($relacion, 'valor')
                ->orderByDesc($sumColumn);

            if ($excluirCapital) {
                $query->whereRaw('UPPER(nombre) != ?', [mb_strtoupper($nombreCapital)]);
            }

            return $query->limit(5)->get()
                ->map(fn($m) => ['nombre' => $m->nombre, 'total' => (int) ($m->$sumColumn ?? 0)]);
        };

        // Top 5 municipios por cada tipo de evento (con y sin capital)
        $topMunicipios = [
            'nacimiento' => $getTop('nacimientos', 'nacimientos_sum_valor', false),
            'defuncion' => $getTop('defunciones', 'defunciones_sum_valor', false),
            'matrimonio' => $getTop('matrimonios', 'matrimonios_sum_valor', false),
        ];

        $topMunicipiosSinCapital = [
            'nacimiento' => $getTop('nacimientos', 'nacimientos_sum_valor', true),
            'defuncion' => $getTop('defunciones', 'defunciones_sum_valor', true),
            'matrimonio' => $getTop('matrimonios', 'matrimonios_sum_valor', true),
        ];

        return response()->json([
            'provincia' => [
                'id' => $provincia->id,
                'nombre' => $provincia->nombre,
                'municipios_count' => $provincia->municipios->count(),
            ],
            'evolucion' => $evolucion,
            'top_municipios' => $topMunicipios,
            'top_municipios_sin_capital' => $topMunicipiosSinCapital,
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
