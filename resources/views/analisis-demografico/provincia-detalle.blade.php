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
    <div class="card-header">
        <div>
            <h2 class="card-title">🏘️ Municipios de {{ $provincia->nombre }}</h2>
            <p class="card-subtitle">{{ $provincia->municipios->count() }} municipios en total</p>
        </div>
        <div class="municipios-search">
            <div class="search-container">
                <input 
                    type="text" 
                    id="buscar-municipio" 
                    placeholder="🔍 Buscar municipio en {{ $provincia->nombre }}..."
                    class="search-input"
                >
                <div id="resultados-busqueda" class="autocomplete-results"></div>
            </div>
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
.municipios-search {
    margin-top: 1rem;
}

.search-container {
    position: relative;
    max-width: 400px;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.autocomplete-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    margin-top: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.autocomplete-results.show {
    display: block;
}

.autocomplete-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.2s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover {
    background-color: var(--bg-secondary);
}

.autocomplete-item strong {
    color: var(--primary-color);
}

.no-results {
    padding: 1rem;
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
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

    // Funcionalidad de búsqueda de municipios con autocompletado
    const buscarInput = document.getElementById('buscar-municipio');
    const resultadosDiv = document.getElementById('resultados-busqueda');
    const tbody = document.querySelector('tbody');
    const todosLosMunicipios = Array.from(tbody.querySelectorAll('tr'));
    
    let debounceTimer;

    buscarInput.addEventListener('input', function() {
        const termino = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (termino.length < 2) {
            resultadosDiv.classList.remove('show');
            mostrarTodosMunicipios();
            return;
        }
        
        debounceTimer = setTimeout(() => {
            buscarMunicipios(termino);
        }, 300);
    });

    function buscarMunicipios(termino) {
        const url = `/api/municipios/buscar?q=${encodeURIComponent(termino)}&provincia_id={{ $provincia->id }}`;
        
        fetch(url)
            .then(response => response.json())
            .then(municipios => {
                mostrarResultados(municipios);
                filtrarTablaMunicipios(termino);
            })
            .catch(error => {
                console.error('Error buscando municipios:', error);
            });
    }

    function mostrarResultados(municipios) {
        if (municipios.length === 0) {
            resultadosDiv.innerHTML = '<div class="no-results">No se encontraron municipios</div>';
        } else {
            resultadosDiv.innerHTML = municipios.map(municipio => `
                <div class="autocomplete-item" onclick="window.location.href='${municipio.url}'">
                    <strong>${municipio.nombre}</strong> (${municipio.codigo_ine})
                </div>
            `).join('');
        }
        
        resultadosDiv.classList.add('show');
    }

    function filtrarTablaMunicipios(termino) {
        const terminoLower = termino.toLowerCase();
        
        todosLosMunicipios.forEach(fila => {
            const nombreMunicipio = fila.cells[1]?.textContent.toLowerCase() || '';
            
            if (nombreMunicipio.includes(terminoLower)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar contador de municipios visibles
        const municipiosVisibles = todosLosMunicipios.filter(fila => fila.style.display !== 'none').length;
        const subtitle = document.querySelector('.card-subtitle');
        if (subtitle) {
            subtitle.textContent = `${municipiosVisibles} de {{ $provincia->municipios->count() }} municipios`;
        }
    }

    function mostrarTodosMunicipios() {
        todosLosMunicipios.forEach(fila => {
            fila.style.display = '';
        });
        
        // Restaurar contador original
        const subtitle = document.querySelector('.card-subtitle');
        if (subtitle) {
            subtitle.textContent = '{{ $provincia->municipios->count() }} municipios en total';
        }
    }

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.search-container')) {
            resultadosDiv.classList.remove('show');
        }
    });
});
</script>
@endsection
