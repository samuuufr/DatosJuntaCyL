@extends('diseños.panel')

@section('titulo_pagina', 'Provincias de Castilla y León')
@section('descripcion_pagina', 'Análisis demográfico provincial')

@section('contenido')

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><span aria-hidden="true">📍</span> Provincias de Castilla y León</h1>
        <p style="font-size: 1rem; opacity: 0.9;">Selecciona una provincia para ver su análisis demográfico detallado</p>
    </div>
</div>

<!-- FILTRO Y VISTA -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="card-title"> Selector de Provincia</h2>
            <p class="card-subtitle">{{ $provincias->count() }} provincias disponibles</p>
        </div>
        <div style="display: flex; gap: 0.5rem;" role="group" aria-label="Cambiar vista">
            <button onclick="cambiarVista('grid')" id="btn-grid" class="btn btn-secondary btn-small" aria-pressed="true" aria-label="Vista cuadrícula">
                <span aria-hidden="true">📊</span> Vista cuadricula
            </button>
            <button onclick="cambiarVista('lista')" id="btn-lista" class="btn btn-secondary btn-small" aria-pressed="false" aria-label="Vista lista">
                <span aria-hidden="true">📋</span> Vista Lista
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Buscador rápido -->
        <div style="margin-bottom: 1.5rem;">
            <label for="buscador-provincia" class="sr-only">Buscar provincia por nombre</label>
            <input
                type="text"
                id="buscador-provincia"
                placeholder="Buscar provincia por nombre..."
                aria-label="Buscar provincia por nombre"
                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary);"
                onkeyup="filtrarProvincias()"
            >
        </div>

        <!-- VISTA GRID -->
        <div id="vista-grid" class="grid grid-3" style="gap: 1.5rem;" role="list" aria-label="Lista de provincias">
            @foreach($provincias as $provincia)
                <article class="provincia-card" data-nombre="{{ strtolower($provincia->nombre) }}" role="listitem">
                    <div class="card provincia-card-interactiva" style="height: 100%;" tabindex="0"
                         onclick="window.location='{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}'"
                         onkeydown="if(event.key==='Enter')window.location='{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}'"
                         role="link"
                         aria-label="Ver análisis de {{ $provincia->nombre }}">
                        <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; display: flex; align-items: center; justify-content: center;">
                            <h3 style="font-size: 1.25rem; margin: 0; text-align: center;">{{ $provincia->nombre }}</h3>
                        </div>
                        <div class="card-body">
                            <dl style="display: flex; flex-direction: column; gap: 0.75rem; margin: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <dt style="color: var(--text-secondary); font-size: 0.875rem;"><span aria-hidden="true">🏘️</span> Municipios</dt>
                                    <dd style="font-size: 1.25rem; font-weight: bold; margin: 0;">{{ $provincia->municipios_count }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(16, 185, 129, 0.1); border-radius: 0.375rem;">
                                    <dt style="color: #10b981; font-size: 0.875rem;"><span aria-hidden="true">👶</span> Nacimientos</dt>
                                    <dd style="color: #10b981; font-weight: bold; margin: 0;">{{ number_format($provincia->estadisticas['nacimientos'], 0, ',', '.') }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 0.375rem;">
                                    <dt style="color: #ef4444; font-size: 0.875rem;"><span aria-hidden="true">⚰️</span> Defunciones</dt>
                                    <dd style="color: #ef4444; font-weight: bold; margin: 0;">{{ number_format($provincia->estadisticas['defunciones'], 0, ',', '.') }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border-radius: 0.375rem;">
                                    <dt style="color: #f59e0b; font-size: 0.875rem;"><span aria-hidden="true">💍</span> Matrimonios</dt>
                                    <dd style="color: #f59e0b; font-weight: bold; margin: 0;">{{ number_format($provincia->estadisticas['matrimonios'], 0, ',', '.') }}</dd>
                                </div>
                                <div style="margin-top: 0.5rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                    <dt style="font-size: 0.875rem; color: var(--text-secondary);">Crecimiento vegetativo</dt>
                                    <dd style="font-size: 1.1rem; font-weight: bold; margin: 0; color: {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '#10b981' : '#ef4444' }};">
                                        {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '+' : '' }}{{ number_format($provincia->estadisticas['crecimiento_vegetativo'], 0, ',', '.') }}
                                    </dd>
                                </div>
                            </dl>
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}" class="btn btn-primary" style="width: 100%; text-align: center;" aria-label="Ver análisis completo de {{ $provincia->nombre }}">
                                    Ver análisis completo <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- VISTA LISTA -->
        <div id="vista-lista" style="display: none;">
            <div class="table-wrapper" role="region" aria-label="Tabla de provincias" tabindex="0">
                <table aria-describedby="tabla-provincias-desc">
                    <caption class="sr-only" id="tabla-provincias-desc">Datos demográficos de las provincias de Castilla y León</caption>
                    <thead>
                        <tr>
                            <th scope="col">Provincia</th>
                            <th scope="col" style="text-align: center;">Municipios</th>
                            <th scope="col" style="text-align: center;">Nacimientos</th>
                            <th scope="col" style="text-align: center;">Defunciones</th>
                            <th scope="col" style="text-align: center;">Matrimonios</th>
                            <th scope="col" style="text-align: center;">Crecimiento</th>
                            <th scope="col" style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provincias as $provincia)
                            <tr class="provincia-row" data-nombre="{{ strtolower($provincia->nombre) }}">
                                <th scope="row" style="text-align: center;"><span aria-hidden="true">📍</span> {{ $provincia->nombre }}</th>
                                <td style="text-align: center;">
                                    <span class="badge badge-primary">{{ $provincia->municipios_count }}</span>
                                </td>
                                <td style="text-align: center; color: #10b981;">
                                    <strong>{{ number_format($provincia->estadisticas['nacimientos'], 0, ',', '.') }}</strong>
                                </td>
                                <td style="text-align: center; color: #ef4444;">
                                    <strong>{{ number_format($provincia->estadisticas['defunciones'], 0, ',', '.') }}</strong>
                                </td>
                                <td style="text-align: center; color: #f59e0b;">
                                    <strong>{{ number_format($provincia->estadisticas['matrimonios'], 0, ',', '.') }}</strong>
                                </td>
                                <td style="text-align: center;">
                                    <strong style="color: {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '#10b981' : '#ef4444' }};">
                                        {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '+' : '' }}{{ number_format($provincia->estadisticas['crecimiento_vegetativo'], 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}" class="btn btn-primary btn-small" aria-label="Ver detalles de {{ $provincia->nombre }}">
                                        Ver detalles <span aria-hidden="true">→</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mensaje sin resultados -->
        <div id="sin-resultados" role="status" aria-live="polite" style="display: none; text-align: center; padding: 3rem; color: var(--text-secondary);">
            <div style="font-size: 3rem; margin-bottom: 1rem;" aria-hidden="true">🔍</div>
            <p style="font-size: 1.125rem;">No se encontraron provincias con ese nombre</p>
        </div>
    </div>
</div>

<script>
// Estado de la vista actual
let vistaActual = 'grid';

// Cambiar entre vista grid y lista
function cambiarVista(vista) {
    vistaActual = vista;
    const vistaGrid = document.getElementById('vista-grid');
    const vistaLista = document.getElementById('vista-lista');
    const btnGrid = document.getElementById('btn-grid');
    const btnLista = document.getElementById('btn-lista');

    if (vista === 'grid') {
        vistaGrid.style.display = 'grid';
        vistaLista.style.display = 'none';
        btnGrid.classList.remove('btn-secondary');
        btnGrid.classList.add('btn-primary');
        btnGrid.setAttribute('aria-pressed', 'true');
        btnLista.classList.remove('btn-primary');
        btnLista.classList.add('btn-secondary');
        btnLista.setAttribute('aria-pressed', 'false');
    } else {
        vistaGrid.style.display = 'none';
        vistaLista.style.display = 'block';
        btnLista.classList.remove('btn-secondary');
        btnLista.classList.add('btn-primary');
        btnLista.setAttribute('aria-pressed', 'true');
        btnGrid.classList.remove('btn-primary');
        btnGrid.classList.add('btn-secondary');
        btnGrid.setAttribute('aria-pressed', 'false');
    }
}

// Filtrar provincias por nombre
function filtrarProvincias() {
    const termino = document.getElementById('buscador-provincia').value.toLowerCase();
    const cards = document.querySelectorAll('.provincia-card');
    const rows = document.querySelectorAll('.provincia-row');
    let visibles = 0;

    // Filtrar cards (vista grid)
    cards.forEach(card => {
        const nombre = card.dataset.nombre;
        if (nombre.includes(termino)) {
            card.style.display = '';
            visibles++;
        } else {
            card.style.display = 'none';
        }
    });

    // Filtrar rows (vista lista)
    rows.forEach(row => {
        const nombre = row.dataset.nombre;
        if (nombre.includes(termino)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Mostrar mensaje si no hay resultados
    const sinResultados = document.getElementById('sin-resultados');
    if (visibles === 0 && termino !== '') {
        sinResultados.style.display = 'block';
        document.getElementById('vista-grid').style.display = 'none';
        document.getElementById('vista-lista').style.display = 'none';
    } else {
        sinResultados.style.display = 'none';
        cambiarVista(vistaActual);
    }
}

// Inicializar vista grid por defecto
document.addEventListener('DOMContentLoaded', function() {
    cambiarVista('grid');
});
</script>

@endsection

@push('estilos_adicionales')
<style>
/* Clase sr-only para accesibilidad */
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

/* Estilos de focus visible */
a:focus-visible, button:focus-visible, [tabindex]:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}

/* Cards interactivas */
.provincia-card-interactiva {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.provincia-card-interactiva:hover,
.provincia-card-interactiva:focus-visible {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.table-wrapper:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: -3px;
}
</style>
@endpush
