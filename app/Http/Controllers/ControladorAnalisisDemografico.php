<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\DatoMnp;
use Illuminate\Http\Request;

class ControladorAnalisisDemografico extends Controller
{
    /**
     * Vista principal de análisis demográfico
     * Muestra comparativas entre provincias y municipios
     */
    public function panel()
    {
        // Obtener todas las provincias con sus municipios
        $provincias = Provincia::with('municipios')->get();

        // Obtener estadísticas generales
        $estadisticas = $this->obtenerEstadisticas();

        // Obtener todas las provincias
        $provinciasDestacadas = $provincias;

        // Obtener municipios más activos (con más datos MNP)
        $municipiosDestacados = Municipio::with('datosMnp')
            ->withCount('datosMnp')
            ->orderByDesc('datos_mnp_count')
            ->limit(10)
            ->get();

        // Obtener resumen de datos MNP
        $resumenMnp = $this->obtenerResumenMnp();

        return view('analisis-demografico.panel', [
            'provincias' => $provincias,
            'provinciasDestacadas' => $provinciasDestacadas,
            'municipiosDestacados' => $municipiosDestacados,
            'estadisticas' => $estadisticas,
            'resumenMnp' => $resumenMnp,
        ]);
    }

    /**
     * Comparativa entre dos provincias
     */
    public function comparar(Request $request)
    {
        $provincias = Provincia::all();

        $provinciaA = null;
        $provinciaB = null;
        $comparativa = null;

        if ($request->has('provincia_a') && $request->has('provincia_b')) {
            $provinciaA = Provincia::with('municipios')->find($request->provincia_a);
            $provinciaB = Provincia::with('municipios')->find($request->provincia_b);

            if ($provinciaA && $provinciaB) {
                $comparativa = $this->generarComparativa($provinciaA, $provinciaB);
            }
        }

        return view('analisis-demografico.comparar', [
            'provincias' => $provincias,
            'provinciaA' => $provinciaA,
            'provinciaB' => $provinciaB,
            'comparativa' => $comparativa,
        ]);
    }

    /**
     * Detalle de una provincia específica
     */
    public function provinciaDetalle($id)
    {
        $provincia = Provincia::with('municipios.datosMnp')->find($id);

        if (!$provincia) {
            abort(404, 'Provincia no encontrada');
        }

        $estadisticas = $this->obtenerEstadisticasProvincia($provincia);

        return view('analisis-demografico.provincia-detalle', [
            'provincia' => $provincia,
            'estadisticas' => $estadisticas,
        ]);
    }

    /**
     * Detalle de un municipio específico
     */
    public function municipioDetalle($id)
    {
        $municipio = Municipio::with('provincia', 'datosMnp')->find($id);

        if (!$municipio) {
            abort(404, 'Municipio no encontrado');
        }

        $estadisticas = $this->obtenerEstadisticasMunicipio($municipio);
        $evolucion = $this->obtenerEvolucionMunicipio($municipio);

        return view('analisis-demografico.municipio-detalle', [
            'municipio' => $municipio,
            'estadisticas' => $estadisticas,
            'evolucion' => $evolucion,
        ]);
    }

    /**
     * Obtiene estadísticas generales del sistema
     */
    private function obtenerEstadisticas()
    {
        return [
            'total_provincias' => Provincia::count(),
            'total_municipios' => Municipio::count(),
            'total_registros_mnp' => DatoMnp::count(),
            'nacimientos_totales' => DatoMnp::where('tipo_evento', 'nacimiento')->sum('valor'),
            'defunciones_totales' => DatoMnp::where('tipo_evento', 'defuncion')->sum('valor'),
            'matrimonios_totales' => DatoMnp::where('tipo_evento', 'matrimonio')->sum('valor'),
            'anos_disponibles' => DatoMnp::distinct('anno')->pluck('anno')->sort()->values(),
        ];
    }

    /**
     * Obtiene resumen de datos MNP
     */
    private function obtenerResumenMnp()
    {
        $datos = DatoMnp::selectRaw('tipo_evento, SUM(valor) as total')
            ->groupBy('tipo_evento')
            ->get()
            ->keyBy('tipo_evento');

        return [
            'nacimientos' => $datos['nacimiento']->total ?? 0,
            'defunciones' => $datos['defuncion']->total ?? 0,
            'matrimonios' => $datos['matrimonio']->total ?? 0,
        ];
    }

    /**
     * Genera una comparativa entre dos provincias
     */
    private function generarComparativa($provinciaA, $provinciaB)
    {
        return [
            'provincia_a' => [
                'nombre' => $provinciaA->nombre,
                'municipios' => $provinciaA->municipios->count(),
                'nacimientos' => $provinciaA->municipios->flatMap->datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
                'defunciones' => $provinciaA->municipios->flatMap->datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
                'matrimonios' => $provinciaA->municipios->flatMap->datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
            ],
            'provincia_b' => [
                'nombre' => $provinciaB->nombre,
                'municipios' => $provinciaB->municipios->count(),
                'nacimientos' => $provinciaB->municipios->flatMap->datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
                'defunciones' => $provinciaB->municipios->flatMap->datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
                'matrimonios' => $provinciaB->municipios->flatMap->datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
            ],
        ];
    }

    /**
     * Obtiene estadísticas de una provincia
     */
    private function obtenerEstadisticasProvincia($provincia)
    {
        $datosMnp = $provincia->municipios->flatMap->datosMnp;

        return [
            'total_municipios' => $provincia->municipios->count(),
            'nacimientos' => $datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
            'defunciones' => $datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
            'matrimonios' => $datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
        ];
    }

    /**
     * Obtiene estadísticas de un municipio
     */
    private function obtenerEstadisticasMunicipio($municipio)
    {
        $datosMnp = $municipio->datosMnp;

        return [
            'nacimientos' => $datosMnp->where('tipo_evento', 'nacimiento')->sum('valor'),
            'defunciones' => $datosMnp->where('tipo_evento', 'defuncion')->sum('valor'),
            'matrimonios' => $datosMnp->where('tipo_evento', 'matrimonio')->sum('valor'),
            'anos' => $datosMnp->pluck('anno')->unique()->sort()->values(),
        ];
    }

    /**
     * Obtiene evolución de un municipio por años
     */
    private function obtenerEvolucionMunicipio($municipio)
    {
        $anos = $municipio->datosMnp->pluck('anno')->unique()->sort()->values();
        $evolucion = [];

        foreach ($anos as $ano) {
            $datosDeLano = $municipio->datosMnp->where('anno', $ano);
            $evolucion[] = [
                'anno' => $ano,
                'nacimientos' => $datosDeLano->where('tipo_evento', 'nacimiento')->first()?->valor ?? 0,
                'defunciones' => $datosDeLano->where('tipo_evento', 'defuncion')->first()?->valor ?? 0,
                'matrimonios' => $datosDeLano->where('tipo_evento', 'matrimonio')->first()?->valor ?? 0,
            ];
        }

        return $evolucion;
    }
}
