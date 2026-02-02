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

            <!-- Buscador con autocompletado -->
            <div style="position: relative; min-width: 280px;">
                <label for="buscar-municipio" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                    Buscar Municipio
                </label>
                <input
                    type="text"
                    id="buscar-municipio"
                    placeholder="Escribe para buscar..."
                    autocomplete="off"
                    style="width: 100%; padding: 0.6rem 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary); font-size: 0.9rem;"
                >
                <div id="search-results" class="search-results"></div>
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
/* Buscador de municipios con autocompletado */
#buscar-municipio:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    max-height: 280px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    margin-top: 4px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

.search-results.show {
    display: block;
}

.search-result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.6rem 0.85rem;
    cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.1s;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover,
.search-result-item.selected {
    background: var(--primary-color);
}

.search-result-item:hover .municipio-nombre,
.search-result-item.selected .municipio-nombre {
    color: white;
}

.search-result-item:hover .municipio-codigo,
.search-result-item.selected .municipio-codigo {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.municipio-nombre {
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--text-primary);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.municipio-nombre .match {
    color: var(--primary-color);
    font-weight: 700;
}

.search-result-item:hover .municipio-nombre .match,
.search-result-item.selected .municipio-nombre .match {
    color: #fbbf24;
}

.municipio-codigo {
    font-size: 0.7rem;
    font-family: monospace;
    color: var(--text-secondary);
    background: var(--bg-tertiary);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    white-space: nowrap;
}

.search-no-results {
    padding: 0.75rem 1rem;
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.8rem;
}

/* Filas de tabla filtradas */
tbody tr.oculto {
    display: none;
}

tbody tr.resaltado {
    background: rgba(59, 130, 246, 0.1);
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
    // BUSCADOR DE MUNICIPIOS CON AUTOCOMPLETADO
    // ========================================
    const searchInput = document.getElementById('buscar-municipio');
    const searchResults = document.getElementById('search-results');
    const tableRows = document.querySelectorAll('tbody tr');

    // Construir lista de municipios desde la tabla
    const municipiosList = [];
    tableRows.forEach(row => {
        const link = row.querySelector('a.btn-primary');
        if (link) {
            municipiosList.push({
                nombre: row.cells[1]?.textContent.trim() || '',
                codigo: row.cells[2]?.textContent.trim() || '',
                registros: row.cells[3]?.textContent.trim() || '0',
                url: link.href,
                row: row
            });
        }
    });

    let selectedIndex = -1;

    // Buscar municipios
    function searchMunicipios(query) {
        if (!query || query.length < 1) return [];

        const normalizedQuery = query.toLowerCase().trim();
        return municipiosList
            .filter(m =>
                m.nombre.toLowerCase().includes(normalizedQuery) ||
                m.codigo.toLowerCase().includes(normalizedQuery)
            )
            .slice(0, 10);
    }

    // Mostrar resultados
    function showResults(results, query) {
        if (results.length === 0) {
            if (query.length >= 1) {
                searchResults.innerHTML = '<div class="search-no-results">Sin resultados para "' + escapeHtml(query) + '"</div>';
                searchResults.classList.add('show');
            } else {
                searchResults.classList.remove('show');
            }
            return;
        }

        searchResults.innerHTML = results.map((m, index) => `
            <div class="search-result-item${index === selectedIndex ? ' selected' : ''}" data-index="${index}" data-url="${m.url}">
                <span class="municipio-nombre">${highlightMatch(m.nombre, query)}</span>
                <span class="municipio-codigo">${m.codigo}</span>
            </div>
        `).join('');

        searchResults.classList.add('show');

        // Event listeners para los resultados
        searchResults.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                window.location.href = this.dataset.url;
            });

            item.addEventListener('mouseenter', function() {
                selectedIndex = parseInt(this.dataset.index);
                updateSelection();
            });
        });
    }

    // Resaltar coincidencias
    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
        return escapeHtml(text).replace(regex, '<span class="match">$1</span>');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Actualizar selección visual
    function updateSelection() {
        const items = searchResults.querySelectorAll('.search-result-item');
        items.forEach((item, index) => {
            item.classList.toggle('selected', index === selectedIndex);
        });

        // Scroll al elemento seleccionado
        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    // Filtrar también la tabla
    function filterTable(query) {
        const normalizedQuery = query.toLowerCase().trim();

        tableRows.forEach(row => {
            const nombre = row.cells[1]?.textContent.toLowerCase() || '';
            const codigo = row.cells[2]?.textContent.toLowerCase() || '';

            if (!normalizedQuery || nombre.includes(normalizedQuery) || codigo.includes(normalizedQuery)) {
                row.classList.remove('oculto');
                row.classList.toggle('resaltado', normalizedQuery.length > 0 && (nombre.includes(normalizedQuery) || codigo.includes(normalizedQuery)));
            } else {
                row.classList.add('oculto');
                row.classList.remove('resaltado');
            }
        });
    }

    // Event: Input
    searchInput.addEventListener('input', function() {
        const query = this.value;
        selectedIndex = -1;
        const results = searchMunicipios(query);
        showResults(results, query);
        filterTable(query);
    });

    // Event: Focus
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 1) {
            const results = searchMunicipios(this.value);
            showResults(results, this.value);
        }
    });

    // Event: Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = searchResults.querySelectorAll('.search-result-item');

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    updateSelection();
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    updateSelection();
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    window.location.href = items[selectedIndex].dataset.url;
                }
                break;

            case 'Escape':
                searchResults.classList.remove('show');
                this.value = '';
                filterTable('');
                this.blur();
                break;
        }
    });

    // Event: Click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });
});
</script>
@endsection
