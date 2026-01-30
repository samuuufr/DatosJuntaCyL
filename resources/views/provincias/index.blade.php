@extends('diseños.panel')

@section('titulo_pagina', 'Provincias de Castilla y León')
@section('descripcion_pagina', 'Análisis demográfico provincial')

@section('contenido')

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">📍 Provincias de Castilla y León</h1>
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
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="cambiarVista('grid')" id="btn-grid" class="btn btn-secondary btn-small">
                📊 Vista cuadricula
            </button>
            <button onclick="cambiarVista('lista')" id="btn-lista" class="btn btn-secondary btn-small">
                📋 Vista Lista
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Buscador rápido -->
        <div style="margin-bottom: 1.5rem;">
            <input
                type="text"
                id="buscador-provincia"
                placeholder="🔍 Buscar provincia por nombre..."
                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary);"
                onkeyup="filtrarProvincias()"
            >
        </div>

        <!-- VISTA GRID -->
        <div id="vista-grid" class="grid grid-3" style="gap: 1.5rem;">
            @foreach($provincias as $provincia)
                <div class="provincia-card" data-nombre="{{ strtolower($provincia->nombre) }}">
                    <div class="card" style="height: 100%; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                         onclick="window.location='{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}'"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                        <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; display: flex; align-items: center; justify-content: center;">
                            <h3 style="font-size: 1.25rem; margin: 0; text-align: center;">{{ $provincia->nombre }}</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: var(--text-secondary); font-size: 0.875rem;">🏘️ Municipios</span>
                                    <strong style="font-size: 1.25rem;">{{ $provincia->municipios_count }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(16, 185, 129, 0.1); border-radius: 0.375rem;">
                                    <span style="color: #10b981; font-size: 0.875rem;">👶 Nacimientos</span>
                                    <strong style="color: #10b981;">{{ number_format($provincia->estadisticas['nacimientos'], 0, ',', '.') }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 0.375rem;">
                                    <span style="color: #ef4444; font-size: 0.875rem;">⚰️ Defunciones</span>
                                    <strong style="color: #ef4444;">{{ number_format($provincia->estadisticas['defunciones'], 0, ',', '.') }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border-radius: 0.375rem;">
                                    <span style="color: #f59e0b; font-size: 0.875rem;">💍 Matrimonios</span>
                                    <strong style="color: #f59e0b;">{{ number_format($provincia->estadisticas['matrimonios'], 0, ',', '.') }}</strong>
                                </div>
                                <div style="margin-top: 0.5rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.875rem; color: var(--text-secondary);">Crecimiento vegetativo</span>
                                    <strong style="font-size: 1.1rem; color: {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '#10b981' : '#ef4444' }};">
                                        {{ $provincia->estadisticas['crecimiento_vegetativo'] >= 0 ? '+' : '' }}{{ number_format($provincia->estadisticas['crecimiento_vegetativo'], 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}" class="btn btn-primary" style="width: 100%; text-align: center;">
                                    Ver análisis completo →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- VISTA LISTA -->
        <div id="vista-lista" style="display: none;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Provincia</th>
                            <th style="text-align: center;">Municipios</th>
                            <th style="text-align: center;">Nacimientos</th>
                            <th style="text-align: center;">Defunciones</th>
                            <th style="text-align: center;">Matrimonios</th>
                            <th style="text-align: center;">Crecimiento</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provincias as $provincia)
                            <tr class="provincia-row" data-nombre="{{ strtolower($provincia->nombre) }}">
                                <td style="text-align: center;"><strong>📍 {{ $provincia->nombre }}</strong></td>
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
                                    <a href="{{ route('analisis-demografico.provincia-detalle', $provincia->id) }}" class="btn btn-primary btn-small">
                                        Ver detalles →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mensaje sin resultados -->
        <div id="sin-resultados" style="display: none; text-align: center; padding: 3rem; color: var(--text-secondary);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
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
        btnLista.classList.remove('btn-primary');
        btnLista.classList.add('btn-secondary');
    } else {
        vistaGrid.style.display = 'none';
        vistaLista.style.display = 'block';
        btnLista.classList.remove('btn-secondary');
        btnLista.classList.add('btn-primary');
        btnGrid.classList.remove('btn-primary');
        btnGrid.classList.add('btn-secondary');
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
