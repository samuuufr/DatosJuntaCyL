@extends('diseños.panel')

@section('titulo_pagina', $municipio->nombre)
@section('descripcion_pagina', 'Análisis detallado del municipio')

@section('contenido')

<!-- BREADCRUMB -->
<div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
    <a href="{{ route('analisis-demografico.panel') }}" style="color: var(--primary-color); text-decoration: none;">Panel</a>
    <span>/</span>
    <a href="{{ route('analisis-demografico.provincia-detalle', $municipio->provincia->id) }}" style="color: var(--primary-color); text-decoration: none;">
        {{ $municipio->provincia->nombre }}
    </a>
    <span>/</span>
    <span>{{ $municipio->nombre }}</span>
</div>

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏘️ {{ $municipio->nombre }}</h1>
        <p style="font-size: 1rem; opacity: 0.9;">
            📍 {{ $municipio->provincia->nombre }}
            <span style="margin: 0 0.5rem;">•</span>
            INE: {{ $municipio->codigo_ine }}
        </p>
    </div>
</div>

<!-- ESTADÍSTICAS PRINCIPALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">📊 Estadísticas Principales</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-3">
            <div class="stat-box">
                <div class="stat-box-value" style="color: #10b981;">
                    {{ number_format($estadisticas['nacimientos'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Nacimientos</div>
                <span class="badge badge-success">Total histórico</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #f59e0b;">
                    {{ number_format($estadisticas['matrimonios'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Matrimonios</div>
                <span class="badge badge-warning">Total histórico</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-value" style="color: #ef4444;">
                    {{ number_format($estadisticas['defunciones'], 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Defunciones</div>
                <span class="badge badge-danger">Total histórico</span>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICOS INTERACTIVOS -->
<div class="grid grid-2" style="gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Gráfico de evolución temporal -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📈 Evolución Temporal</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-evolucion" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Gráfico de distribución -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📊 Distribución Total de Eventos</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-distribucion" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<!-- EVOLUCIÓN POR AÑOS -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title">📈 Evolución por Años</h2>
            <p class="card-subtitle">Registro histórico de eventos MNP</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Año</th>
                        <th style="text-align: center; color: #10b981;">👶 Nacimientos</th>
                        <th style="text-align: center; color: #f59e0b;">💍 Matrimonios</th>
                        <th style="text-align: center; color: #ef4444;">⚰️ Defunciones</th>
                        <th style="text-align: center;">Total MNP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evolucion as $datos)
                        <tr>
                            <td><strong>{{ $datos['anno'] }}</strong></td>
                            <td style="text-align: center;">
                                <strong style="color: #10b981;">{{ number_format($datos['nacimientos'], 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <strong style="color: #f59e0b;">{{ number_format($datos['matrimonios'], 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <strong style="color: #ef4444;">{{ number_format($datos['defunciones'], 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $total = $datos['nacimientos'] + $datos['matrimonios'] + $datos['defunciones'];
                                @endphp
                                <strong>{{ number_format($total, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                No hay datos de evolución disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TODOS LOS REGISTROS MNP -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">📋 Todos los Registros MNP</h2>
            <p class="card-subtitle">{{ $municipio->datosMnp->count() }} registros en total</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Tipo Evento</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($municipio->datosMnp->sortByDesc('anno') as $dato)
                        <tr>
                            <td><strong>{{ $dato->anno }}</strong></td>
                            <td>
                                @php
                                    $badges = [
                                        'nacimiento' => 'badge-success',
                                        'matrimonio' => 'badge-warning',
                                        'defuncion' => 'badge-danger',
                                    ];
                                    $badgeClass = $badges[$dato->tipo_evento] ?? 'badge-primary';
                                    $icons = [
                                        'nacimiento' => '👶',
                                        'matrimonio' => '💍',
                                        'defuncion' => '⚰️',
                                    ];
                                    $icon = $icons[$dato->tipo_evento] ?? '📊';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $icon }} {{ ucfirst($dato->tipo_evento) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ number_format($dato->valor, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem;">
                                No hay registros MNP disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js_adicional')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preparar datos de evolución
    const evolucion = @json($evolucion);
    const anos = evolucion.map(d => d.anno);
    const nacimientos = evolucion.map(d => d.nacimientos);
    const defunciones = evolucion.map(d => d.defunciones);
    const matrimonios = evolucion.map(d => d.matrimonios);

    // Gráfico de evolución temporal
    const ctxEvolucion = document.getElementById('grafico-evolucion').getContext('2d');
    new Chart(ctxEvolucion, {
        type: 'line',
        data: {
            labels: anos,
            datasets: [
                {
                    label: 'Nacimientos',
                    data: nacimientos,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Defunciones',
                    data: defunciones,
                    borderColor: 'rgba(239, 68, 68, 1)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Matrimonios',
                    data: matrimonios,
                    borderColor: 'rgba(245, 158, 11, 1)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico de distribución total
    const ctxDistribucion = document.getElementById('grafico-distribucion').getContext('2d');
    new Chart(ctxDistribucion, {
        type: 'doughnut',
        data: {
            labels: ['Nacimientos', 'Defunciones', 'Matrimonios'],
            datasets: [{
                data: [
                    {{ $estadisticas['nacimientos'] }},
                    {{ $estadisticas['defunciones'] }},
                    {{ $estadisticas['matrimonios'] }}
                ],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgba(16, 185, 129, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
