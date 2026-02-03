@extends('diseños.panel')

@section('titulo_pagina', 'Buscador de Municipios')
@section('descripcion_pagina', 'Encuentra y analiza municipios de Castilla y León')

@section('contenido')

<!-- ENCABEZADO -->
<div class="card encabezado-municipios" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none;">
    <div class="card-body">
        <h1 class="titulo-municipios">Buscador de Municipios</h1>
        <p class="subtitulo-municipios">Busca municipios por nombre o filtra por provincia</p>
    </div>
</div>

<!-- BUSCADOR -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title"><span aria-hidden="true">🔍</span> Búsqueda Avanzada</h2>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('municipios.index') }}" id="form-busqueda">
            <div class="grid-busqueda">
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
                        role="combobox"
                        aria-expanded="false"
                        aria-controls="lista-sugerencias"
                        aria-autocomplete="list"
                        aria-describedby="buscar-ayuda"
                    >
                    <span id="buscar-ayuda" class="sr-only">Escribe al menos 2 caracteres para ver sugerencias. Usa las flechas arriba/abajo para navegar y Enter para seleccionar.</span>
                    <!-- Sugerencias de autocompletado -->
                    <div id="sugerencias" style="position: relative; display: none;">
                        <ul id="lista-sugerencias" role="listbox" aria-label="Sugerencias de municipios" style="position: absolute; top: 0; left: 0; right: 0; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; z-index: 100; list-style: none; margin: 0; padding: 0;">
                        </ul>
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

            <div class="botones-busqueda">
                <button type="submit" class="btn btn-primary">
                    <span aria-hidden="true">🔍</span> Buscar
                </button>
                <a href="{{ route('municipios.index') }}" class="btn btn-secondary">
                    <span aria-hidden="true">🔄</span> Limpiar filtros
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
                <h2 class="card-title" id="tabla-titulo"><span aria-hidden="true">📋</span> Resultados de la búsqueda</h2>
                <p class="card-subtitle">{{ $municipios->total() }} municipios encontrados</p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrapper tabla-municipios-wrapper" role="region" aria-labelledby="tabla-titulo" tabindex="0">
                <table class="tabla-municipios" aria-describedby="tabla-descripcion">
                    <caption class="sr-only" id="tabla-descripcion">Lista de municipios de Castilla y León con información demográfica</caption>
                    <thead>
                        <tr>
                            <th scope="col" aria-label="Número de orden">#</th>
                            <th scope="col">Municipio</th>
                            <th scope="col">Provincia</th>
                            <th scope="col">Código INE</th>
                            <th scope="col" style="text-align: center;">Registros MNP</th>
                            <th scope="col" style="text-align: center;">Acción</th>
                            @auth
                                <th scope="col" style="width: 80px; text-align: center;">Favorito</th>
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
                                    <a href="{{ route('analisis-demografico.provincia-detalle', $municipio->provincia->id) }}" style="color: var(--text-secondary); text-decoration: none;" aria-label="Ver análisis de {{ $municipio->provincia->nombre }}">
                                        <span aria-hidden="true">📍</span> {{ $municipio->provincia->nombre }}
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
                                    <a href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}" class="btn btn-primary btn-small" aria-label="Ver ficha demográfica de {{ $municipio->nombre }}">
                                        Ver ficha <span aria-hidden="true">→</span>
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($municipios->hasPages())
                <div style="margin-top: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <p id="paginacion-info" style="color: var(--text-secondary); font-size: 0.875rem;">
                        Mostrando {{ $municipios->firstItem() }} - {{ $municipios->lastItem() }} de {{ $municipios->total() }} resultados
                    </p>
                    <nav aria-label="Paginación de resultados" style="display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap; justify-content: center;">
                        {{-- Botón Anterior --}}
                        @if($municipios->onFirstPage())
                            <button type="button" disabled aria-disabled="true" aria-label="Página anterior (no disponible)"
                               class="paginacion-btn paginacion-btn-disabled">
                                <span aria-hidden="true">←</span> Anterior
                            </button>
                        @else
                            <a href="{{ $municipios->appends(request()->query())->previousPageUrl() }}"
                               aria-label="Ir a la página anterior"
                               class="paginacion-btn paginacion-btn-activo">
                                <span aria-hidden="true">←</span> Anterior
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
                               aria-label="Ir a la página 1"
                               class="paginacion-btn paginacion-btn-numero">
                                1
                            </a>
                            @if($start > 2)
                                <span aria-hidden="true" style="padding: 0.5rem 0.25rem; color: var(--text-secondary);">...</span>
                            @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                                <span aria-current="page" aria-label="Página {{ $page }}, página actual"
                                   class="paginacion-btn paginacion-btn-actual">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $municipios->appends(request()->query())->url($page) }}"
                                   aria-label="Ir a la página {{ $page }}"
                                   class="paginacion-btn paginacion-btn-numero">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span aria-hidden="true" style="padding: 0.5rem 0.25rem; color: var(--text-secondary);">...</span>
                            @endif
                            <a href="{{ $municipios->appends(request()->query())->url($lastPage) }}"
                               aria-label="Ir a la página {{ $lastPage }}"
                               class="paginacion-btn paginacion-btn-numero">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Botón Siguiente --}}
                        @if($municipios->hasMorePages())
                            <a href="{{ $municipios->appends(request()->query())->nextPageUrl() }}"
                               aria-label="Ir a la página siguiente"
                               class="paginacion-btn paginacion-btn-activo">
                                Siguiente <span aria-hidden="true">→</span>
                            </a>
                        @else
                            <button type="button" disabled aria-disabled="true" aria-label="Página siguiente (no disponible)"
                               class="paginacion-btn paginacion-btn-disabled">
                                Siguiente <span aria-hidden="true">→</span>
                            </button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>
@elseif(request()->has('buscar') || request()->has('provincia_id'))
    <!-- Sin resultados -->
    <div class="card" role="status" aria-live="polite">
        <div class="card-body estado-vacio">
            <div class="estado-vacio-icono" aria-hidden="true">🔍</div>
            <h3 class="estado-vacio-titulo">
                No se encontraron municipios
            </h3>
            <p class="estado-vacio-texto">
                Intenta con otros términos de búsqueda o ajusta los filtros
            </p>
            <a href="{{ route('municipios.index') }}" class="btn btn-primary">
                <span aria-hidden="true">🔄</span> Limpiar búsqueda
            </a>
        </div>
    </div>
@else
    <!-- Estado inicial -->
    <div class="card">
        <div class="card-body estado-vacio">
            <div class="estado-vacio-icono" aria-hidden="true">🏘️</div>
            <h3 class="estado-vacio-titulo">
                Comienza tu búsqueda
            </h3>
            <p class="estado-vacio-texto">
                Introduce el nombre de un municipio o selecciona una provincia para ver resultados
            </p>
            <div class="provincias-sugeridas" role="group" aria-label="Provincias sugeridas">
                @foreach($provincias->take(3) as $provincia)
                    <a href="{{ route('municipios.index', ['provincia_id' => $provincia->id]) }}" class="btn btn-secondary" aria-label="Filtrar municipios de {{ $provincia->nombre }}">
                        <span aria-hidden="true">📍</span> {{ $provincia->nombre }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<script>
// Autocompletado de búsqueda con Fetch API - Accesible con teclado
let timeoutId = null;
let indiceSeleccionado = -1;
let municipiosActuales = [];

const inputBuscar = document.getElementById('buscar');
const sugerenciasDiv = document.getElementById('sugerencias');
const listaSugerencias = document.getElementById('lista-sugerencias');

// Función para actualizar el estado visual de las sugerencias
function actualizarSeleccion() {
    const opciones = listaSugerencias.querySelectorAll('[role="option"]');
    opciones.forEach((opcion, index) => {
        if (index === indiceSeleccionado) {
            opcion.setAttribute('aria-selected', 'true');
            opcion.style.background = 'var(--bg-secondary)';
            opcion.scrollIntoView({ block: 'nearest' });
            inputBuscar.setAttribute('aria-activedescendant', opcion.id);
        } else {
            opcion.setAttribute('aria-selected', 'false');
            opcion.style.background = 'var(--bg-primary)';
        }
    });
}

// Función para cerrar sugerencias
function cerrarSugerencias() {
    sugerenciasDiv.style.display = 'none';
    inputBuscar.setAttribute('aria-expanded', 'false');
    inputBuscar.removeAttribute('aria-activedescendant');
    indiceSeleccionado = -1;
}

// Función para abrir sugerencias
function abrirSugerencias() {
    if (listaSugerencias.children.length > 0) {
        sugerenciasDiv.style.display = 'block';
        inputBuscar.setAttribute('aria-expanded', 'true');
    }
}

// Función para seleccionar una opción
function seleccionarOpcion(index) {
    if (index >= 0 && index < municipiosActuales.length) {
        window.location.href = municipiosActuales[index].url;
    }
}

inputBuscar.addEventListener('input', function(e) {
    const termino = e.target.value;

    // Limpiar timeout anterior
    clearTimeout(timeoutId);
    indiceSeleccionado = -1;

    // Si el término es muy corto, ocultar sugerencias
    if (termino.length < 2) {
        cerrarSugerencias();
        listaSugerencias.innerHTML = '';
        municipiosActuales = [];
        return;
    }

    // Esperar 300ms antes de hacer la búsqueda
    timeoutId = setTimeout(() => {
        // Hacer petición AJAX con Fetch API
        fetch(`/api/municipios/buscar?q=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(municipios => {
                municipiosActuales = municipios;

                if (municipios.length === 0) {
                    cerrarSugerencias();
                    listaSugerencias.innerHTML = '';
                    return;
                }

                // Construir HTML de sugerencias accesibles
                let html = '';
                municipios.forEach((municipio, index) => {
                    html += `
                        <li id="sugerencia-${index}"
                            role="option"
                            aria-selected="false"
                            tabindex="-1"
                            data-url="${municipio.url}"
                            data-index="${index}"
                            style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s;">
                            <div style="font-weight: 500;">${municipio.nombre}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                <span aria-hidden="true">📍</span> ${municipio.provincia} • INE: ${municipio.codigo_ine}
                            </div>
                        </li>
                    `;
                });

                listaSugerencias.innerHTML = html;
                abrirSugerencias();

                // Añadir eventos a las opciones
                listaSugerencias.querySelectorAll('[role="option"]').forEach(opcion => {
                    opcion.addEventListener('click', function() {
                        window.location.href = this.dataset.url;
                    });
                    opcion.addEventListener('mouseenter', function() {
                        indiceSeleccionado = parseInt(this.dataset.index);
                        actualizarSeleccion();
                    });
                });
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
            });
    }, 300);
});

// Navegación con teclado
inputBuscar.addEventListener('keydown', function(e) {
    const opciones = listaSugerencias.querySelectorAll('[role="option"]');
    const numOpciones = opciones.length;

    if (numOpciones === 0) return;

    switch(e.key) {
        case 'ArrowDown':
            e.preventDefault();
            if (sugerenciasDiv.style.display === 'none') {
                abrirSugerencias();
            }
            indiceSeleccionado = (indiceSeleccionado + 1) % numOpciones;
            actualizarSeleccion();
            break;

        case 'ArrowUp':
            e.preventDefault();
            if (sugerenciasDiv.style.display === 'none') {
                abrirSugerencias();
            }
            indiceSeleccionado = indiceSeleccionado <= 0 ? numOpciones - 1 : indiceSeleccionado - 1;
            actualizarSeleccion();
            break;

        case 'Enter':
            if (indiceSeleccionado >= 0) {
                e.preventDefault();
                seleccionarOpcion(indiceSeleccionado);
            }
            break;

        case 'Escape':
            cerrarSugerencias();
            break;

        case 'Tab':
            cerrarSugerencias();
            break;
    }
});

// Ocultar sugerencias al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target !== inputBuscar && !sugerenciasDiv.contains(e.target)) {
        cerrarSugerencias();
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
    const municipioNombre = boton.closest('tr').querySelector('td:nth-child(2) strong').textContent;
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
/* ========================================
   ESTILOS BASE
   ======================================== */

/* Clase para ocultar visualmente pero mantener accesible para lectores de pantalla */
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

/* Encabezado */
.titulo-municipios {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.subtitulo-municipios {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
}

/* Grid de búsqueda */
.grid-busqueda {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

/* Botones de búsqueda */
.botones-busqueda {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Tabla de municipios */
.tabla-municipios-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.tabla-municipios {
    min-width: 650px;
}

/* ========================================
   RESPONSIVE - TABLET (max-width: 768px)
   ======================================== */
@media (max-width: 768px) {
    .titulo-municipios {
        font-size: 1.75rem;
    }

    .subtitulo-municipios {
        font-size: 0.9rem;
    }

    .grid-busqueda {
        grid-template-columns: 1fr;
    }

    .tabla-municipios {
        font-size: 0.875rem;
    }

    .tabla-municipios th,
    .tabla-municipios td {
        padding: 0.5rem 0.4rem;
    }

    /* Ocultar columnas menos importantes en tablet */
    .tabla-municipios th:nth-child(4),
    .tabla-municipios td:nth-child(4) {
        display: none;
    }

    .paginacion-btn {
        padding: 0.4rem 0.5rem;
        font-size: 0.8rem;
    }

    .paginacion-btn-numero {
        min-width: 2rem;
    }
}

/* ========================================
   RESPONSIVE - MÓVIL (max-width: 576px)
   ======================================== */
@media (max-width: 576px) {
    .encabezado-municipios .card-body {
        padding: 1rem;
    }

    .titulo-municipios {
        font-size: 1.4rem;
    }

    .subtitulo-municipios {
        font-size: 0.85rem;
    }

    .botones-busqueda {
        flex-direction: column;
    }

    .botones-busqueda .btn {
        width: 100%;
        justify-content: center;
    }

    /* Tabla más compacta en móvil */
    .tabla-municipios {
        font-size: 0.8rem;
        min-width: 500px;
    }

    .tabla-municipios th,
    .tabla-municipios td {
        padding: 0.4rem 0.3rem;
    }

    /* Ocultar más columnas en móvil */
    .tabla-municipios th:nth-child(1),
    .tabla-municipios td:nth-child(1),
    .tabla-municipios th:nth-child(5),
    .tabla-municipios td:nth-child(5) {
        display: none;
    }

    .btn-small {
        padding: 0.3rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Paginación compacta */
    .paginacion-btn {
        padding: 0.35rem 0.4rem;
        font-size: 0.75rem;
    }

    .paginacion-btn-numero {
        min-width: 1.8rem;
    }

    /* Ocultar texto "Anterior/Siguiente" en móvil muy pequeño */
    @media (max-width: 400px) {
        .paginacion-btn-activo,
        .paginacion-btn-disabled {
            padding: 0.35rem 0.5rem;
        }
    }

    /* Botón favorito más pequeño */
    .boton-favorito-estrella {
        width: 36px;
        height: 36px;
        font-size: 1.2rem;
    }
}

/* ========================================
   ESTADOS VACÍOS
   ======================================== */
.estado-vacio {
    text-align: center;
    padding: 4rem 2rem;
}

.estado-vacio-icono {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.estado-vacio-titulo {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.estado-vacio-texto {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.provincias-sugeridas {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 576px) {
    .estado-vacio {
        padding: 2rem 1rem;
    }

    .estado-vacio-icono {
        font-size: 3rem;
    }

    .estado-vacio-titulo {
        font-size: 1.2rem;
    }

    .estado-vacio-texto {
        font-size: 0.9rem;
    }

    .provincias-sugeridas {
        flex-direction: column;
        gap: 0.5rem;
    }

    .provincias-sugeridas .btn {
        width: 100%;
    }
}

/* ========================================
   INDICADOR DE SCROLL EN TABLA
   ======================================== */
.tabla-municipios-wrapper {
    position: relative;
}

@media (max-width: 768px) {
    .tabla-municipios-wrapper::after {
        content: '← Desliza →';
        display: block;
        text-align: center;
        font-size: 0.75rem;
        color: var(--text-secondary);
        padding: 0.5rem;
        background: var(--bg-secondary);
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .tabla-municipios-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .tabla-municipios-wrapper::-webkit-scrollbar-track {
        background: var(--bg-secondary);
        border-radius: 3px;
    }

    .tabla-municipios-wrapper::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 3px;
    }
}

/* Estilos de focus visible para accesibilidad */
a:focus-visible,
button:focus-visible,
input:focus-visible,
select:focus-visible,
[tabindex]:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
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

.boton-favorito-estrella:focus-visible {
    outline: 3px solid #f59e0b;
    outline-offset: 2px;
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

/* Estilos de paginación accesible */
.paginacion-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.paginacion-btn-activo {
    color: var(--text-primary);
    background: var(--bg-secondary);
}

.paginacion-btn-activo:hover,
.paginacion-btn-activo:focus-visible {
    background: var(--primary-color);
    color: white;
}

.paginacion-btn-disabled {
    color: var(--text-muted);
    background: var(--bg-tertiary);
    cursor: not-allowed;
}

.paginacion-btn-numero {
    min-width: 2.5rem;
    text-align: center;
    color: var(--text-primary);
    background: var(--bg-secondary);
}

.paginacion-btn-numero:hover,
.paginacion-btn-numero:focus-visible {
    background: var(--primary-color);
    color: white;
}

.paginacion-btn-actual {
    min-width: 2.5rem;
    text-align: center;
    color: white;
    background: var(--primary-color);
    font-weight: 600;
}

/* Estilos para las opciones del autocompletado */
#lista-sugerencias [role="option"] {
    outline: none;
}

#lista-sugerencias [role="option"]:focus-visible,
#lista-sugerencias [role="option"][aria-selected="true"] {
    background: var(--bg-secondary);
}

/* Hacer visible el scroll del table-wrapper cuando tiene foco */
.table-wrapper:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: -3px;
}
</style>
@endpush
