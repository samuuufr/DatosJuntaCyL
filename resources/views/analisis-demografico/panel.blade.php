@extends('diseños.panel')

@section('titulo_pagina', 'Panel de Control')
@section('descripcion_pagina', 'Resumen de datos demográficos de Castilla y León')

@section('contenido')

<!-- SECCIÓN: ESTADÍSTICAS GENERALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title"> Estadísticas Generales</h2>
            <p class="card-subtitle">Resumen de todos los datos disponibles</p>
        </div>
    </div>
    <div class="card-body">
        <div class="grid grid-4">
            <div class="stat-box">
                <div class="stat-box-value">{{ $estadisticas['total_provincias'] }}</div>
                <div class="stat-box-label">Provincias</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">{{ $estadisticas['total_municipios'] }}</div>
                <div class="stat-box-label">Municipios</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">{{ $estadisticas['total_registros_mnp'] }}</div>
                <div class="stat-box-label">Registros MNP</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">{{ count($estadisticas['anos_disponibles']) }}</div>
                <div class="stat-box-label">Años Disponibles</div>
            </div>
        </div>
    </div>
</div>

<!-- SECCIÓN: RESUMEN DE EVENTOS MNP -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title"> Resumen de Eventos MNP</h2>
            <p class="card-subtitle">Nacimientos, Matrimonios y Defunciones</p>
        </div>
    </div>
    <div class="card-body">
        <div class="grid grid-3">
            <div class="stat-box">
                <div class="stat-box-value" style="color: #10b981;">
                    {{ number_format($resumenMnp['nacimientos'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Nacimientos</div>
                <span class="badge badge-success">Total histórico</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #f59e0b;">
                    {{ number_format($resumenMnp['matrimonios'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Matrimonios</div>
                <span class="badge badge-warning">Total histórico</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #ef4444;">
                    {{ number_format($resumenMnp['defunciones'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Defunciones</div>
                <span class="badge badge-danger">Total histórico</span>
            </div>
        </div>
    </div>
</div>

<!-- SECCIÓN: MUNICIPIOS MÁS ACTIVOS -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title" id="titulo-top-municipios"><span aria-hidden="true">🏆</span> Top 10 Municipios Más Activos</h2>
            <p class="card-subtitle">Con más registros de datos MNP</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper" role="region" aria-labelledby="titulo-top-municipios" tabindex="0">
            <table>
                <caption class="sr-only">Top 10 municipios con más registros de datos MNP</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Municipio</th>
                        <th scope="col">Provincia</th>
                        <th scope="col">Registros MNP</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($municipiosDestacados as $index => $municipio)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <th scope="row">{{ $municipio->nombre }}</th>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $municipio->provincia->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $municipio->datos_mnp_count }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small" aria-label="Ver detalles de {{ $municipio->nombre }}">
                                    Ver detalles <span aria-hidden="true">→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                No hay datos disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECCIÓN: PROVINCIAS -->
<div class="card" id="provincias">
    <div class="card-header">
        <div>
            <h2 class="card-title" id="titulo-provincias"><span aria-hidden="true">📍</span> Provincias de Castilla y León</h2>
            <p class="card-subtitle">Datos disponibles por provincia</p>
        </div>
    </div>
    <div class="card-body">
        <div class="grid grid-3" role="list" aria-labelledby="titulo-provincias">
            @forelse($provinciasDestacadas as $provincia)
                <article role="listitem" tabindex="0"
                     style="padding: 1.5rem; background-color: var(--bg-tertiary); border-radius: 0.375rem; cursor: pointer; transition: all 0.3s ease;"
                     onclick="window.location.href='{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}'"
                     onkeydown="if(event.key==='Enter')window.location.href='{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}'"
                     aria-label="Ver análisis de {{ $provincia->nombre }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600;">{{ $provincia->nombre }}</h3>
                        <span style="font-size: 2rem;" aria-hidden="true">→</span>
                    </div>
                    <dl style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.875rem; margin: 0;">
                        <div>
                            <dt style="color: var(--text-secondary);">Municipios:</dt>
                            <dd style="margin: 0;"><strong style="color: var(--primary-color);">{{ $provincia->municipios->count() }}</strong></dd>
                        </div>
                        <div>
                            <dt style="color: var(--text-secondary);">Registros:</dt>
                            <dd style="margin: 0;"><strong style="color: var(--primary-color);">{{ $provincia->municipios->flatMap->datosMnp->count() }}</strong></dd>
                        </div>
                    </dl>
                </article>
            @empty
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary);">
                    No hay provincias disponibles
                </p>
            @endforelse
        </div>
    </div>
</div>

@push('estilos_adicionales')
<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
a:focus-visible, button:focus-visible, [tabindex]:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}
.table-wrapper:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: -3px;
}
</style>
@endpush

@endsection
