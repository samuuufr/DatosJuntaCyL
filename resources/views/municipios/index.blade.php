@extends('diseños.panel')

@section('titulo_pagina', 'Buscador de Municipios')
@section('descripcion_pagina', 'Encuentra y analiza municipios de Castilla y León')

@section('contenido')

<!-- ENCABEZADO -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"> Buscador de Municipios</h1>
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
                            @auth
                                <th style="width: 80px; text-align: center;">Favorito</th>
                            @endauth
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
                                @auth
                                    <td style="text-align: center;">
                                        <button
                                            data-estrella-municipio="{{ $municipio->id }}"
                                            class="boton-favorito-estrella"
                                        >
                                            ☆
                                        </button>
                                    </td>
                                @endauth
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($municipios->hasPages())
                <div style="margin-top: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        Mostrando {{ $municipios->firstItem() }} - {{ $municipios->lastItem() }} de {{ $municipios->total() }} resultados
                    </p>
                    <nav style="display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap; justify-content: center;">
                        {{-- Botón Anterior --}}
                        @if($municipios->onFirstPage())
                            <span style="padding: 0.5rem 0.75rem; color: var(--text-muted); background: var(--bg-tertiary); border-radius: 0.375rem; cursor: not-allowed; font-size: 0.875rem;">
                                ← Anterior
                            </span>
                        @else
                            <a href="{{ $municipios->appends(request()->query())->previousPageUrl() }}"
                               style="padding: 0.5rem 0.75rem; color: var(--text-primary); background: var(--bg-secondary); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; transition: background 0.2s;"
                               onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                               onmouseout="this.style.background='var(--bg-secondary)'; this.style.color='var(--text-primary)';">
                                ← Anterior
                            </a>
                        @endif

                        {{-- Números de página --}}
                        @php
                            $currentPage = $municipios->currentPage();
                            $lastPage = $municipios->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $municipios->appends(request()->query())->url(1) }}"
                               style="padding: 0.5rem 0.75rem; min-width: 2.5rem; text-align: center; color: var(--text-primary); background: var(--bg-secondary); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem;">
                                1
                            </a>
                            @if($start > 2)
                                <span style="padding: 0.5rem 0.25rem; color: var(--text-secondary);">...</span>
                            @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                                <span style="padding: 0.5rem 0.75rem; min-width: 2.5rem; text-align: center; color: white; background: var(--primary-color); border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem;">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $municipios->appends(request()->query())->url($page) }}"
                                   style="padding: 0.5rem 0.75rem; min-width: 2.5rem; text-align: center; color: var(--text-primary); background: var(--bg-secondary); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; transition: background 0.2s;"
                                   onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                                   onmouseout="this.style.background='var(--bg-secondary)'; this.style.color='var(--text-primary)';">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span style="padding: 0.5rem 0.25rem; color: var(--text-secondary);">...</span>
                            @endif
                            <a href="{{ $municipios->appends(request()->query())->url($lastPage) }}"
                               style="padding: 0.5rem 0.75rem; min-width: 2.5rem; text-align: center; color: var(--text-primary); background: var(--bg-secondary); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem;">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Botón Siguiente --}}
                        @if($municipios->hasMorePages())
                            <a href="{{ $municipios->appends(request()->query())->nextPageUrl() }}"
                               style="padding: 0.5rem 0.75rem; color: var(--text-primary); background: var(--bg-secondary); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; transition: background 0.2s;"
                               onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                               onmouseout="this.style.background='var(--bg-secondary)'; this.style.color='var(--text-primary)';">
                                Siguiente →
                            </a>
                        @else
                            <span style="padding: 0.5rem 0.75rem; color: var(--text-muted); background: var(--bg-tertiary); border-radius: 0.375rem; cursor: not-allowed; font-size: 0.875rem;">
                                Siguiente →
                            </span>
                        @endif
                    </nav>
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

// ========================================
// BOTONES DE FAVORITO ESTRELLA
// ========================================
@auth
const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const botonesEstrella = document.querySelectorAll('[data-estrella-municipio]');
let procesandoFavorito = false;

// Mostrar notificación temporal
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion-favorito notificacion-${tipo}`;
    notificacion.textContent = mensaje;

    document.body.appendChild(notificacion);

    setTimeout(() => notificacion.classList.add('mostrar'), 100);

    setTimeout(() => {
        notificacion.classList.remove('mostrar');
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

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
    if (esFavorito) {
        boton.classList.add('favorito-activo');
        boton.textContent = '⭐';
    } else {
        boton.classList.remove('favorito-activo');
        boton.textContent = '☆';
    }
}

// Toggle favorito
async function toggleFavoritoEstrella(municipioId, boton) {
    if (procesandoFavorito || boton.disabled) return;

    procesandoFavorito = true;
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
            const data = await response.json();
            actualizarBotonEstrella(boton, !esFavorito);
            mostrarNotificacion(data.message, esFavorito ? 'info' : 'success');
        } else {
            mostrarNotificacion('Error al actualizar favorito', 'error');
        }
    } catch (error) {
        console.log('Error al actualizar favorito:', error);
        mostrarNotificacion('Error de conexión', 'error');
    } finally {
        boton.disabled = false;
        procesandoFavorito = false;
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
</script>

@endsection

@push('estilos_adicionales')
<style>
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
</style>
@endpush
