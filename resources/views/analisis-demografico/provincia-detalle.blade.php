@extends('diseños.panel')

@section('titulo_pagina', $provincia->nombre)
@section('descripcion_pagina', 'Análisis detallado de la provincia')

@section('contenido')

<!-- BREADCRUMB -->
<nav aria-label="Ruta de navegación" style="margin-bottom: 2rem;">
    <ol style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary); list-style: none; margin: 0; padding: 0;">
        <li><a href="{{ route('analisis-demografico.panel') }}" style="color: var(--primary-color); text-decoration: none;">Panel</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page">{{ $provincia->nombre }}</li>
    </ol>
</nav>

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><span aria-hidden="true">📍</span> {{ $provincia->nombre }}</h1>
        <p style="font-size: 1rem; opacity: 0.9;">Datos demográficos completos</p>
    </div>
</div>

<!-- ESTADÍSTICAS PRINCIPALES -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title"><span aria-hidden="true">📊</span> Estadísticas Generales</h2>
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
            <h2 class="card-title" id="titulo-grafico-dist"><span aria-hidden="true">📊</span> Distribución de Eventos MNP</h2>
        </div>
        <div class="card-body">
            <canvas id="grafico-distribucion" style="max-height: 300px;" role="img" aria-labelledby="titulo-grafico-dist" aria-describedby="desc-grafico-dist"></canvas>
            <p id="desc-grafico-dist" class="sr-only">Gráfico circular mostrando la proporción de nacimientos, defunciones y matrimonios en la provincia</p>
        </div>
    </div>

    <!-- Gráfico de top municipios -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h2 class="card-title" id="titulo-grafico-top"><span aria-hidden="true">🏆</span> Top 5 Municipios</h2>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" id="excluir-capital" style="cursor: pointer;" aria-describedby="desc-excluir-capital">
                    <span id="desc-excluir-capital">Excluir capital</span>
                </label>
                <label for="selector-tipo-evento" class="sr-only">Tipo de evento</label>
                <select id="selector-tipo-evento" aria-label="Seleccionar tipo de evento" style="padding: 0.5rem 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                    <option value="nacimiento">Nacimientos</option>
                    <option value="defuncion">Defunciones</option>
                    <option value="matrimonio">Matrimonios</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <canvas id="grafico-top-municipios" style="max-height: 300px;" role="img" aria-labelledby="titulo-grafico-top" aria-describedby="desc-grafico-top"></canvas>
            <p id="desc-grafico-top" class="sr-only">Gráfico de barras mostrando los 5 municipios con más eventos del tipo seleccionado</p>
        </div>
    </div>
</div>

<!-- LISTADO DE MUNICIPIOS -->
<div class="card">
    <div class="card-header" style="flex-direction: column; align-items: stretch;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="card-title" id="titulo-tabla-municipios"><span aria-hidden="true">🏘️</span> Municipios de {{ $provincia->nombre }}</h2>
                <p class="card-subtitle" id="contador-municipios" aria-live="polite">{{ $provincia->municipios->count() }} municipios en total</p>
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
                    aria-describedby="contador-municipios"
                    style="width: 100%; padding: 0.6rem 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary); font-size: 0.9rem;"
                >
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper" role="region" aria-labelledby="titulo-tabla-municipios" tabindex="0">
            <table id="tabla-municipios">
                <caption class="sr-only">Lista de municipios de {{ $provincia->nombre }} con sus datos demográficos</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Municipio</th>
                        <th scope="col">Código INE</th>
                        <th scope="col">Registros MNP</th>
                        <th scope="col">Acción</th>
                        @auth
                            <th scope="col" style="width: 60px; text-align: center;">Favorito</th>
                        @endauth
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
                                <a href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small" aria-label="Ver detalles de {{ $municipio->nombre }}">
                                    Ver detalles <span aria-hidden="true">→</span>
                                </a>
                            </td>
                            @auth
                                <td style="text-align: center;">
                                    <button
                                        data-estrella-municipio="{{ $municipio->id }}"
                                        class="boton-favorito-estrella"
                                        aria-label="Añadir {{ $municipio->nombre }} a favoritos"
                                        aria-pressed="false"
                                        title="Añadir a favoritos"
                                    >
                                        <span aria-hidden="true">☆</span>
                                    </button>
                                </td>
                            @endauth
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() ? 6 : 5 }}" style="text-align: center; padding: 2rem;">
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

/* Botón de favorito estrella */
.boton-favorito-estrella {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    background-color: var(--bg-tertiary);
    color: var(--text-secondary);
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.boton-favorito-estrella:hover {
    border-color: #f59e0b;
    color: #f59e0b;
    transform: scale(1.1);
}

.boton-favorito-estrella:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.boton-favorito-estrella.favorito-activo {
    background-color: #fbbf24;
    border-color: #f59e0b;
    color: #78350f;
}

.boton-favorito-estrella.favorito-activo:hover {
    background-color: #f59e0b;
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

    // Configuración de colores por tipo de evento
    const coloresEvento = {
        nacimiento: { bg: 'rgba(16, 185, 129, 0.8)', border: 'rgba(16, 185, 129, 1)', label: 'Nacimientos' },
        defuncion: { bg: 'rgba(239, 68, 68, 0.8)', border: 'rgba(239, 68, 68, 1)', label: 'Defunciones' },
        matrimonio: { bg: 'rgba(245, 158, 11, 0.8)', border: 'rgba(245, 158, 11, 1)', label: 'Matrimonios' }
    };

    let chartTopMunicipios = null;
    let datosTopMunicipios = null;
    let datosTopMunicipiosSinCapital = null;

    // Función para actualizar el gráfico
    function actualizarGraficoTop() {
        const tipoEvento = document.getElementById('selector-tipo-evento').value;
        const excluirCapital = document.getElementById('excluir-capital').checked;

        const fuente = excluirCapital ? datosTopMunicipiosSinCapital : datosTopMunicipios;

        if (!fuente || !fuente[tipoEvento]) {
            console.warn('No hay datos para:', tipoEvento);
            return;
        }

        const datos = fuente[tipoEvento];
        const colores = coloresEvento[tipoEvento];

        if (chartTopMunicipios) {
            chartTopMunicipios.data.labels = datos.map(m => m.nombre);
            chartTopMunicipios.data.datasets[0].data = datos.map(m => m.total);
            chartTopMunicipios.data.datasets[0].label = colores.label;
            chartTopMunicipios.data.datasets[0].backgroundColor = colores.bg;
            chartTopMunicipios.data.datasets[0].borderColor = colores.border;
            chartTopMunicipios.options.plugins.tooltip.callbacks.label = function(context) {
                return colores.label + ': ' + context.parsed.y.toLocaleString();
            };
            chartTopMunicipios.update();
        }
    }

    // Cargar datos para top municipios con Fetch API
    fetch('{{ route("api.provincias.datos", $provincia->id) }}')
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta: ' + response.status);
            return response.json();
        })
        .then(data => {
            datosTopMunicipios = data.top_municipios;
            datosTopMunicipiosSinCapital = data.top_municipios_sin_capital;

            const tipoInicial = 'nacimiento';
            const datosIniciales = datosTopMunicipios[tipoInicial] || [];
            const coloresIniciales = coloresEvento[tipoInicial];

            const ctxTop = document.getElementById('grafico-top-municipios').getContext('2d');
            chartTopMunicipios = new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: datosIniciales.map(m => m.nombre),
                    datasets: [{
                        label: coloresIniciales.label,
                        data: datosIniciales.map(m => m.total),
                        backgroundColor: coloresIniciales.bg,
                        borderColor: coloresIniciales.border,
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
                                    return coloresIniciales.label + ': ' + context.parsed.y.toLocaleString();
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

    // Event listeners para el selector y checkbox
    document.getElementById('selector-tipo-evento').addEventListener('change', actualizarGraficoTop);
    document.getElementById('excluir-capital').addEventListener('change', actualizarGraficoTop);

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

    // ========================================
    // BOTONES DE FAVORITO ESTRELLA
    // ========================================
    @auth
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const botonesEstrella = document.querySelectorAll('[data-estrella-municipio]');
    let procesando = false;

    // Cargar estado inicial de favoritos
    async function cargarFavoritosEstrella() {
        try {
            const response = await fetch(`${baseUrl}/api/perfil/favoritos/lista`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                const favoritosIds = new Set(data.favoritos);

                botonesEstrella.forEach(boton => {
                    const municipioId = parseInt(boton.getAttribute('data-estrella-municipio'));
                    actualizarBotonEstrella(boton, favoritosIds.has(municipioId));
                });
            }
        } catch (error) {
            console.log('Error al cargar favoritos:', error);
        }
    }

    // Actualizar estado visual del botón
    function actualizarBotonEstrella(boton, esFavorito) {
        const municipioNombre = boton.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        if (esFavorito) {
            boton.classList.add('favorito-activo');
            boton.innerHTML = '<span aria-hidden="true">⭐</span>';
            boton.setAttribute('aria-pressed', 'true');
            boton.setAttribute('aria-label', `Quitar ${municipioNombre} de favoritos`);
            boton.setAttribute('title', 'Quitar de favoritos');
        } else {
            boton.classList.remove('favorito-activo');
            boton.innerHTML = '<span aria-hidden="true">☆</span>';
            boton.setAttribute('aria-pressed', 'false');
            boton.setAttribute('aria-label', `Añadir ${municipioNombre} a favoritos`);
            boton.setAttribute('title', 'Añadir a favoritos');
        }
    }

    // Toggle favorito
    async function toggleFavoritoEstrella(municipioId, boton) {
        if (procesando || boton.disabled) return;

        procesando = true;
        boton.disabled = true;

        const esFavorito = boton.classList.contains('favorito-activo');
        const endpoint = esFavorito ? 'eliminar' : 'agregar';

        try {
            const response = await fetch(`${baseUrl}/api/perfil/favoritos/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ municipio_id: municipioId })
            });

            if (response.ok) {
                actualizarBotonEstrella(boton, !esFavorito);
            }
        } catch (error) {
            console.log('Error al actualizar favorito:', error);
        } finally {
            boton.disabled = false;
            procesando = false;
        }
    }

    // Añadir event listeners a los botones
    botonesEstrella.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const municipioId = parseInt(this.getAttribute('data-estrella-municipio'));
            toggleFavoritoEstrella(municipioId, this);
        });
    });

    // Cargar estado inicial
    cargarFavoritosEstrella();
    @endauth
});
</script>
@endsection
