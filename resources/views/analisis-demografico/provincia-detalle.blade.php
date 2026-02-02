@extends('diseños.panel')

@section('titulo_pagina', $provincia->nombre)
@section('descripcion_pagina', 'Análisis detallado de la provincia')

@section('contenido')

<!-- BREADCRUMB -->
<div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
    <a href="{{ route('analisis-demografico.panel') }}" style="color: var(--primary-color); text-decoration: none;">Panel</a>
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

<!-- GRÁFICOS INTERACTIVOS -->
<div class="grid grid-2" style="gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Gráfico de distribución -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📊 Distribución de Eventos MNP</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-distribucion" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Gráfico de top municipios -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">🏆 Top 5 Municipios por Población</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-top-municipios" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<!-- LISTADO DE MUNICIPIOS -->
<div class="card">
    <div class="card-header" style="flex-direction: column; align-items: stretch;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="card-title">🏘️ Municipios de {{ $provincia->nombre }}</h2>
                <p class="card-subtitle" id="contador-municipios">{{ $provincia->municipios->count() }} municipios en total</p>
            </div>

            <!-- Buscador que filtra la tabla -->
            <div style="min-width: 280px;">
                <label for="buscar-municipio" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                    Buscar Municipio
                </label>
                <input
                    type="text"
                    id="buscar-municipio"
                    placeholder="Escribe para filtrar..."
                    autocomplete="off"
                    style="width: 100%; padding: 0.6rem 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary); font-size: 0.9rem;"
                >
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tabla-municipios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Municipio</th>
                        <th>Código INE</th>
                        <th>Registros MNP</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tbody-municipios">
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
                                <a href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small">
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

@push('estilos_adicionales')
<style>
/* Buscador de municipios */
#buscar-municipio:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* Filas de tabla filtradas */
#tbody-municipios tr.oculto {
    display: none !important;
}

@media (max-width: 768px) {
    #buscar-municipio {
        width: 100% !important;
    }
}
</style>
@endpush

@section('js_adicional')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de distribución de eventos MNP
    const ctxDistribucion = document.getElementById('grafico-distribucion').getContext('2d');
    new Chart(ctxDistribucion, {
        type: 'pie',
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

    // Cargar datos para top municipios con Fetch API
    fetch('/api/provincias/{{ $provincia->id }}/datos')
        .then(response => response.json())
        .then(data => {
            const ctxTop = document.getElementById('grafico-top-municipios').getContext('2d');
            new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: data.top_municipios.map(m => m.nombre),
                    datasets: [{
                        label: 'Nacimientos',
                        data: data.top_municipios.map(m => m.total),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Nacimientos: ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error cargando datos de municipios:', error);
        });

    // ========================================
    // BUSCADOR DE MUNICIPIOS - FILTRO DE TABLA
    // ========================================
    const searchInput = document.getElementById('buscar-municipio');
    const tableRows = document.querySelectorAll('#tbody-municipios tr');
    const contadorMunicipios = document.getElementById('contador-municipios');
    const totalMunicipios = tableRows.length;

    console.log('Filas encontradas:', totalMunicipios); // Debug

    // Filtrar la tabla según la búsqueda
    function filterTable(query) {
        const normalizedQuery = query.toLowerCase().trim();
        let visibles = 0;

        tableRows.forEach((row, index) => {
            const nombre = row.cells[1]?.textContent.toLowerCase() || '';
            const codigo = row.cells[2]?.textContent.toLowerCase() || '';

            const coincide = !normalizedQuery || nombre.includes(normalizedQuery) || codigo.includes(normalizedQuery);

            if (coincide) {
                row.style.display = ''; // Mostrar
                row.classList.remove('oculto');
                row.cells[0].innerHTML = '<strong>' + (++visibles) + '</strong>';
            } else {
                row.style.display = 'none'; // Ocultar directamente
                row.classList.add('oculto');
            }
        });

        // Actualizar contador
        if (normalizedQuery) {
            contadorMunicipios.textContent = visibles + ' de ' + totalMunicipios + ' municipios';
        } else {
            contadorMunicipios.textContent = totalMunicipios + ' municipios en total';
        }
    }

    // Event: Input
    searchInput.addEventListener('input', function() {
        filterTable(this.value);
    });

    // Event: Escape para limpiar
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterTable('');
            this.blur();
        }
    });
});
</script>
@endsection
