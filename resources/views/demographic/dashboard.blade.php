@extends('layouts.dashboard')

@section('page_title', 'Dashboard')
@section('page_description', 'Resumen de datos demográficos de Castilla y León')

@section('content')

<!-- SECCIÓN: ESTADÍSTICAS GENERALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title">📊 Estadísticas Generales</h2>
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
            <h2 class="card-title">👶 Resumen de Eventos MNP</h2>
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
            <h2 class="card-title">🏘️ Top 10 Municipios Más Activos</h2>
            <p class="card-subtitle">Con más registros de datos MNP</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Municipio</th>
                        <th>Provincia</th>
                        <th>Registros MNP</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($municipiosDestacados as $index => $municipio)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td>{{ $municipio->nombre }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $municipio->provincia->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $municipio->datos_mnp_count }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('demographic.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small">
                                    Ver detalles →
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
            <h2 class="card-title">📍 Provincias de Castilla y León</h2>
            <p class="card-subtitle">Datos disponibles por provincia</p>
        </div>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            @forelse($provinciasDestacadas as $provincia)
                <div style="padding: 1.5rem; background-color: var(--bg-tertiary); border-radius: 0.375rem; cursor: pointer; transition: all 0.3s ease;"
                     onclick="window.location.href='{{ route('demographic.provincia-detalle', $provincia->id) }}'">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600;">{{ $provincia->nombre }}</h3>
                        <span style="font-size: 2rem;">→</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.875rem;">
                        <div>
                            <span style="color: var(--text-secondary);">Municipios:</span><br>
                            <strong style="color: var(--primary-color);">{{ $provincia->municipios->count() }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary);">Registros:</span><br>
                            <strong style="color: var(--primary-color);">{{ $provincia->municipios->flatMap->datosMnp->count() }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary);">
                    No hay provincias disponibles
                </p>
            @endforelse
        </div>
    </div>
</div>

@endsection
