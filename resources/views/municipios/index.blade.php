@extends('diseños.panel')

@section('titulo_pagina', 'Buscador de Municipios')
@section('descripcion_pagina', 'Encuentra y analiza municipios de Castilla y León')

@section('contenido')

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏘️ Buscador de Municipios</h1>
        <p style="font-size: 1rem; opacity: 0.9;">Busca municipios por nombre o filtra por provincia</p>
    </div>
</div>

<!-- BUSCADOR -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">🔍 Búsqueda Avanzada</h2>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('municipios.index') }}" id="form-busqueda">
            <div class="grid grid-2" style="gap: 1rem; margin-bottom: 1rem;">
                <!-- Búsqueda por nombre -->
                <div>
                    <label for="buscar" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                        Nombre del municipio
                    </label>
                    <input
                        type="text"
                        name="buscar"
                        id="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Ej: Valladolid, Salamanca, Burgos..."
                        style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary);"
                        autocomplete="off"
                    >
                    <!-- Sugerencias de autocompletado -->
                    <div id="sugerencias" style="position: relative; display: none;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; z-index: 100;">
                        </div>
                    </div>
                </div>

                <!-- Filtro por provincia -->
                <div>
                    <label for="provincia_id" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                        Filtrar por provincia
                    </label>
                    <select
                        name="provincia_id"
                        id="provincia_id"
                        style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary);"
                        onchange="document.getElementById('form-busqueda').submit()"
                    >
                        <option value="">Todas las provincias</option>
                        @foreach($provincias as $provincia)
                            <option value="{{ $provincia->id }}" {{ request('provincia_id') == $provincia->id ? 'selected' : '' }}>
                                {{ $provincia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    🔍 Buscar
                </button>
                <a href="{{ route('municipios.index') }}" class="btn btn-secondary">
                    🔄 Limpiar filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- RESULTADOS -->
@if($municipios->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">📋 Resultados de la búsqueda</h2>
                <p class="card-subtitle">{{ $municipios->total() }} municipios encontrados</p>
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
                            <th>Código INE</th>
                            <th style="text-align: center;">Registros MNP</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($municipios as $index => $municipio)
                            <tr>
                                <td><strong>{{ $municipios->firstItem() + $index }}</strong></td>
                                <td>
                                    <strong style="color: var(--primary-color);">{{ $municipio->nombre }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('analisis-demografico.provincia-detalle', $municipio->provincia->id) }}" style="color: var(--text-secondary); text-decoration: none;">
                                        📍 {{ $municipio->provincia->nombre }}
                                    </a>
                                </td>
                                <td>
                                    <code style="background-color: var(--bg-tertiary); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem;">
                                        {{ $municipio->codigo_ine }}
                                    </code>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-primary">{{ $municipio->datosMnp->count() }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small">
                                        Ver ficha →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($municipios->hasPages())
                <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.5rem;">
                    {{ $municipios->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@elseif(request()->has('buscar') || request()->has('provincia_id'))
    <!-- Sin resultados -->
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-primary);">
                No se encontraron municipios
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                Intenta con otros términos de búsqueda o ajusta los filtros
            </p>
            <a href="{{ route('municipios.index') }}" class="btn btn-primary">
                🔄 Limpiar búsqueda
            </a>
        </div>
    </div>
@else
    <!-- Estado inicial -->
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🏘️</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-primary);">
                Comienza tu búsqueda
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                Introduce el nombre de un municipio o selecciona una provincia para ver resultados
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                @foreach($provincias->take(3) as $provincia)
                    <a href="{{ route('municipios.index', ['provincia_id' => $provincia->id]) }}" class="btn btn-secondary">
                        📍 {{ $provincia->nombre }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<script>
// Autocompletado de búsqueda con Fetch API
let timeoutId = null;

document.getElementById('buscar').addEventListener('input', function(e) {
    const termino = e.target.value;
    const sugerenciasDiv = document.getElementById('sugerencias');
    const sugerenciasContent = sugerenciasDiv.querySelector('div');

    // Limpiar timeout anterior
    clearTimeout(timeoutId);

    // Si el término es muy corto, ocultar sugerencias
    if (termino.length < 2) {
        sugerenciasDiv.style.display = 'none';
        return;
    }

    // Esperar 300ms antes de hacer la búsqueda
    timeoutId = setTimeout(() => {
        // Hacer petición AJAX con Fetch API
        fetch(`/api/municipios/buscar?q=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(municipios => {
                if (municipios.length === 0) {
                    sugerenciasDiv.style.display = 'none';
                    return;
                }

                // Construir HTML de sugerencias
                let html = '';
                municipios.forEach(municipio => {
                    html += `
                        <a href="${municipio.url}"
                           style="display: block; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); text-decoration: none; color: var(--text-primary); transition: background 0.2s;"
                           onmouseover="this.style.background='var(--bg-secondary)'"
                           onmouseout="this.style.background='var(--bg-primary)'">
                            <div style="font-weight: 500;">${municipio.nombre}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                📍 ${municipio.provincia} • INE: ${municipio.codigo_ine}
                            </div>
                        </a>
                    `;
                });

                sugerenciasContent.innerHTML = html;
                sugerenciasDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
            });
    }, 300);
});

// Ocultar sugerencias al hacer clic fuera
document.addEventListener('click', function(e) {
    const buscar = document.getElementById('buscar');
    const sugerencias = document.getElementById('sugerencias');

    if (e.target !== buscar && !sugerencias.contains(e.target)) {
        sugerencias.style.display = 'none';
    }
});
</script>

@endsection
