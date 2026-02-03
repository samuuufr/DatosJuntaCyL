@extends('diseños.panel')

@section('titulo_pagina', $municipio->nombre)
@section('descripcion_pagina', 'Análisis detallado del municipio')

@section('contenido')

<!-- BREADCRUMB -->
<nav aria-label="Ruta de navegación" style="margin-bottom: 2rem;">
    <ol style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary); list-style: none; margin: 0; padding: 0;">
        <li><a href="{{ route('analisis-demografico.panel') }}" style="color: var(--primary-color); text-decoration: none;">Panel</a></li>
        <li aria-hidden="true">/</li>
        <li><a href="{{ route('analisis-demografico.provincia-detalle', $municipio->provincia->id) }}" style="color: var(--primary-color); text-decoration: none;">{{ $municipio->provincia->nombre }}</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page">{{ $municipio->nombre }}</li>
    </ol>
</nav>

<!-- ENCABEZADO -->
<div class="card encabezado-municipio" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <div class="encabezado-municipio-contenido">
            <div class="encabezado-municipio-info">
                <h1 class="titulo-municipio"><span aria-hidden="true">🏘️</span> {{ $municipio->nombre }}</h1>
                <p class="subtitulo-municipio">
                    <span aria-hidden="true">📍</span> {{ $municipio->provincia->nombre }}
                    <span class="separador-movil" aria-hidden="true">•</span>
                    <span class="sr-only">Código </span>INE: {{ $municipio->codigo_ine }}
                    @if($municipio->poblacion)
                        <span class="separador-movil" aria-hidden="true">•</span>
                        <span aria-hidden="true">👥</span> <span class="texto-poblacion">Población:</span> {{ number_format($municipio->poblacion, 0, ',', '.') }} <span class="texto-habitantes">habitantes</span>
                    @endif
                </p>
            </div>

            @auth
                <!-- Botón de favorito -->
                <button
                    data-favorito-municipio="{{ $municipio->id }}"
                    class="boton-favorito boton-favorito-municipio"
                    style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;"
                    aria-label="Añadir {{ $municipio->nombre }} a favoritos"
                    aria-pressed="false"
                >
                    <span aria-hidden="true">☆</span> <span class="texto-favorito">Añadir a favoritos</span>
                </button>
            @else
                <!-- Mensaje para usuarios no autenticados -->
                <a
                    href="{{ route('login') }}"
                    class="boton-favorito boton-favorito-municipio"
                    style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;"
                    aria-label="Iniciar sesión para añadir a favoritos"
                >
                    <span aria-hidden="true">☆</span> <span class="texto-favorito">Iniciar sesión</span>
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- ESTADÍSTICAS PRINCIPALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title"><span aria-hidden="true">📊</span> Estadísticas Principales</h2>
    </div>
    <div class="card-body">
        <div class="grid {{ $municipio->poblacion ? 'grid-4' : 'grid-3' }}">
            @if($municipio->poblacion)
            <div class="stat-box">
                <div class="stat-box-value" style="color: #3b82f6;">
                    {{ number_format($municipio->poblacion, 0, ',', '.') }}
                </div>
                <div class="stat-box-label">Población Total</div>
                <span class="badge badge-primary">👥 Habitantes</span>
            </div>
            @endif
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
            <h2 class="card-title" id="titulo-grafico-evolucion"><span aria-hidden="true">📈</span> Evolución Temporal</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-evolucion" style="max-height: 300px;" role="img" aria-labelledby="titulo-grafico-evolucion" aria-describedby="desc-grafico-evolucion"></canvas>
            <p id="desc-grafico-evolucion" class="sr-only">Gráfico de líneas mostrando la evolución de nacimientos, defunciones y matrimonios a lo largo del tiempo</p>
        </div>
    </div>

    <!-- Gráfico de distribución -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title" id="titulo-grafico-distribucion"><span aria-hidden="true">📊</span> Distribución Total de Eventos</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-distribucion" style="max-height: 300px;" role="img" aria-labelledby="titulo-grafico-distribucion" aria-describedby="desc-grafico-distribucion"></canvas>
            <p id="desc-grafico-distribucion" class="sr-only">Gráfico circular mostrando la proporción de nacimientos, defunciones y matrimonios</p>
        </div>
    </div>
</div>

<!-- EVOLUCIÓN POR AÑOS -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title" id="titulo-evolucion"><span aria-hidden="true">📈</span> Evolución por Años</h2>
            <p class="card-subtitle">Registro histórico de eventos MNP</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper" role="region" aria-labelledby="titulo-evolucion" tabindex="0">
            <table>
                <caption class="sr-only">Evolución anual de nacimientos, matrimonios y defunciones</caption>
                <thead>
                    <tr>
                        <th scope="col">Año</th>
                        <th scope="col" style="text-align: center; color: #10b981;"><span aria-hidden="true">👶</span> Nacimientos</th>
                        <th scope="col" style="text-align: center; color: #f59e0b;"><span aria-hidden="true">💍</span> Matrimonios</th>
                        <th scope="col" style="text-align: center; color: #ef4444;"><span aria-hidden="true">⚰️</span> Defunciones</th>
                        <th scope="col" style="text-align: center;">Total MNP</th>
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
            <h2 class="card-title" id="titulo-registros"><span aria-hidden="true">📋</span> Todos los Registros MNP</h2>
            <p class="card-subtitle">{{ $municipio->datosMnp->count() }} registros en total</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper" role="region" aria-labelledby="titulo-registros" tabindex="0">
            <table>
                <caption class="sr-only">Listado completo de registros del Movimiento Natural de Población</caption>
                <thead>
                    <tr>
                        <th scope="col">Año</th>
                        <th scope="col">Tipo Evento</th>
                        <th scope="col">Valor</th>
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
.boton-favorito:focus-visible {
    outline: 3px solid white;
    outline-offset: 2px;
}

/* ========================================
   ESTILOS BASE - MUNICIPIO DETALLE
   ======================================== */
.titulo-municipio {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.subtitulo-municipio {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
}

.separador-movil {
    margin: 0 0.5rem;
}

.encabezado-municipio-contenido {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.encabezado-municipio-info {
    flex: 1;
}

.boton-favorito-municipio {
    flex-shrink: 0;
}

/* ========================================
   RESPONSIVE - MUNICIPIO DETALLE
   ======================================== */
@media (max-width: 768px) {
    .titulo-municipio {
        font-size: 1.75rem;
    }

    .subtitulo-municipio {
        font-size: 0.9rem;
    }

    .encabezado-municipio-contenido {
        flex-direction: column;
        gap: 1rem;
    }

    .boton-favorito-municipio {
        width: 100%;
        justify-content: center;
    }

    /* Gráficos en columna */
    .grid-2[style*="gap: 1.5rem"] {
        grid-template-columns: 1fr !important;
    }

    /* Tablas responsive */
    .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-wrapper table {
        min-width: 400px;
    }
}

@media (max-width: 576px) {
    .encabezado-municipio .card-body {
        padding: 1rem;
    }

    .titulo-municipio {
        font-size: 1.4rem;
    }

    .subtitulo-municipio {
        font-size: 0.8rem;
        line-height: 1.6;
    }

    /* Separadores en líneas diferentes */
    .separador-movil {
        display: none;
    }

    .subtitulo-municipio {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .texto-poblacion,
    .texto-habitantes {
        display: none;
    }

    .texto-favorito {
        display: none;
    }

    .boton-favorito-municipio {
        padding: 0.75rem;
        font-size: 1.25rem;
    }

    /* Breadcrumb compacto */
    nav[aria-label="Ruta de navegación"] ol {
        font-size: 0.75rem !important;
        flex-wrap: wrap;
    }

    /* Grid de estadísticas */
    .grid-4, .grid-3 {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .stat-box {
        padding: 0.75rem;
    }

    .stat-box-value {
        font-size: 1.25rem;
    }

    .stat-box-label {
        font-size: 0.7rem;
    }

    /* Tablas más compactas */
    .table-wrapper table {
        font-size: 0.8rem;
    }

    th, td {
        padding: 0.5rem 0.4rem;
    }
}
</style>
@endpush
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
