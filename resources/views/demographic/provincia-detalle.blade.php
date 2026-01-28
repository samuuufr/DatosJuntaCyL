@extends('layouts.dashboard')

@section('page_title', $provincia->nombre)
@section('page_description', 'Análisis detallado de la provincia')

@section('content')

<!-- BREADCRUMB -->
<div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
    <a href="{{ route('demographic.dashboard') }}" style="color: var(--primary-color); text-decoration: none;">Dashboard</a>
    <span>/</span>
    <span>{{ $provincia->nombre }}</span>
</div>

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">📍 {{ $provincia->nombre }}</h1>
        <p style="font-size: 1rem; opacity: 0.9;">Datos demográficos completos</p>
    </div>
</div>

<!-- ESTADÍSTICAS PRINCIPALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">📊 Estadísticas Generales</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-4">
            <div class="stat-box">
                <div class="stat-box-value">{{ $estadisticas['total_municipios'] }}</div>
                <div class="stat-box-label">Municipios</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #10b981;">
                    {{ number_format($estadisticas['nacimientos'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Nacimientos</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #f59e0b;">
                    {{ number_format($estadisticas['matrimonios'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Matrimonios</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #ef4444;">
                    {{ number_format($estadisticas['defunciones'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Defunciones</div>
            </div>
        </div>
    </div>
</div>

<!-- LISTADO DE MUNICIPIOS -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">🏘️ Municipios de {{ $provincia->nombre }}</h2>
            <p class="card-subtitle">{{ $provincia->municipios->count() }} municipios en total</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Municipio</th>
                        <th>Código INE</th>
                        <th>Registros MNP</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provincia->municipios as $index => $municipio)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td>{{ $municipio->nombre }}</td>
                            <td>
                                <code style="background-color: var(--bg-tertiary); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem;">
                                    {{ $municipio->codigo_ine }}
                                </code>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $municipio->datosMnp->count() }}</span>
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
                                No hay municipios disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
