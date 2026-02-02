@extends('diseños.panel')

@section('titulo_pagina', 'Mapa de Calor Demográfico')
@section('descripcion_pagina', 'Visualización geográfica de eventos demográficos por municipio en Castilla y León')

@section('css_adicional')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #mapa-container {
        height: 70vh;
        min-height: 500px;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .leyenda {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .leyenda-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .leyenda-color {
        width: 30px;
        height: 20px;
        border-radius: 0.25rem;
        border: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        font-size: 1.5rem;
        color: var(--text-secondary);
        flex-direction: column;
        gap: 1rem;
    }

    .spinner {
        border: 4px solid var(--bg-tertiary);
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Leaflet dark mode compatibility */
    [data-tema="oscuro"] .leaflet-container {
        background-color: var(--bg-tertiary);
    }

    [data-tema="oscuro"] .leaflet-popup-content-wrapper {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
    }

    [data-tema="oscuro"] .leaflet-popup-tip {
        background-color: var(--bg-secondary);
    }

    [data-tema="oscuro"] .leaflet-control-zoom a {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        border-color: var(--border-color);
    }

    [data-tema="oscuro"] .leaflet-control-zoom a:hover {
        background-color: var(--bg-tertiary);
    }

    /* Tooltips de provincias */
    .provincia-tooltip {
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        border: none !important;
        border-radius: 0.25rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    [data-tema="oscuro"] .provincia-tooltip {
        background-color: rgba(255, 255, 255, 0.9);
        color: #1f2937;
    }

    /* Eliminar todas las flechas/bordes del tooltip de provincias */
    .leaflet-tooltip.provincia-tooltip::before,
    .leaflet-tooltip-left.provincia-tooltip::before,
    .leaflet-tooltip-right.provincia-tooltip::before,
    .leaflet-tooltip-top.provincia-tooltip::before,
    .leaflet-tooltip-bottom.provincia-tooltip::before {
        display: none !important;
        border: none !important;
    }

    /* Eliminar efectos visuales al hacer clic en provincias */
    .leaflet-interactive:focus {
        outline: none !important;
    }

    .leaflet-container a.leaflet-popup-close-button:focus {
        outline: none !important;
    }

    /* Prevenir resaltado de selección */
    .leaflet-container {
        -webkit-tap-highlight-color: transparent;
    }

    /* Grid layouts */
    .grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    /* Buscador de municipios */
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 0.25rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .search-results.show {
        display: block;
    }

    .search-result-item {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        transition: background-color 0.15s;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover {
        background: var(--bg-tertiary);
    }

    .search-result-item .municipio-nombre {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .search-result-item .municipio-provincia {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* Municipio resaltado en el mapa */
    .municipio-destacado {
        stroke: #fbbf24 !important;
        stroke-width: 4 !important;
        stroke-dasharray: none !important;
    }

    @media (max-width: 1024px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        #mapa-container { height: 50vh; min-height: 400px; }
        .leyenda { font-size: 0.75rem; padding: 0.75rem; }
        .leyenda-color { width: 20px; height: 15px; }

        .grid-4 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('contenido')

<!-- CONTROLES SUPERIORES -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <div class="grid grid-4">
            <!-- Selector de Año -->
            <div>
                <label for="select-ano" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Año
                </label>
                <select id="select-ano" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary);">
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                </select>
            </div>

            <!-- Selector de Tipo de Evento -->
            <div>
                <label for="select-evento" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Tipo de Evento
                </label>
                <select id="select-evento" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary);">
                    <option value="nacimiento">Nacimientos</option>
                    <option value="defuncion">Defunciones</option>
                    <option value="matrimonio">Matrimonios</option>
                </select>
            </div>

            <!-- Buscador de Municipios -->
            <div style="position: relative;">
                <label for="search-municipio" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Buscar Municipio
                </label>
                <input
                    type="text"
                    id="search-municipio"
                    placeholder="Buscar por nombre..."
                    autocomplete="off"
                    style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary);">
                <div id="search-results" class="search-results"></div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div style="display: flex; flex-direction: column; justify-content: center; background: var(--bg-tertiary); padding: 1rem; border-radius: 0.375rem;">
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                    Total Regional
                </div>
                <div id="stat-total" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">
                    Cargando...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAPA Y LEYENDA -->
<div class="card">
    <div class="card-body" style="padding: 0; position: relative;">
        <div id="mapa-container">
            <div class="loading">
                <div class="spinner"></div>
                <span>Cargando mapa...</span>
            </div>
        </div>

        <!-- Leyenda -->
        <div style="position: absolute; bottom: 2rem; left: 2rem; z-index: 1000;">
            <div class="leyenda">
                <h3 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
                    Leyenda
                </h3>
                <div id="legend-content">
                    <!-- Se llena dinámicamente con JS -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js_adicional')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Mapa de Calor Script -->
<script>
    // URL base para enlaces
    const BASE_URL = '{{ url('/') }}';

    // Estado de autenticación del usuario
    const USUARIO_AUTENTICADO = {{ auth()->check() ? 'true' : 'false' }};

    // Configuración
    const CONFIG = {
        apiUrl: '{{ url('/api/mapa-calor/datos') }}',
        geojsonUrl: '{{ asset('geojson/municipios-cyl-simplified.json') }}',
        provinciasUrl: '{{ asset('geojson/provincias-cyl.json') }}',
        colorScales: {
            nacimiento: ['#d1fae5', '#86efac', '#10b981', '#059669', '#047857'],
            defuncion: ['#fee2e2', '#fca5a5', '#ef4444', '#dc2626', '#991b1b'],
            matrimonio: ['#fef3c7', '#fcd34d', '#f59e0b', '#d97706', '#92400e']
        },
        defaultCenter: [41.6523, -4.7245], // Centro de Castilla y León
        defaultZoom: 7
    };

    // Estado de la aplicación
    let state = {
        map: null,
        geojsonLayer: null,
        provinciasLayer: null,
        geojsonData: null,
        provinciasData: null,
        currentData: null,
        currentYear: 2023,
        currentEvent: 'nacimiento'
    };

    // Inicializar mapa
    async function initMap() {
        try {
            // Crear mapa
            state.map = L.map('mapa-container').setView(CONFIG.defaultCenter, CONFIG.defaultZoom);

            // Añadir capa base (cambiar según tema)
            const isDark = document.documentElement.getAttribute('data-tema') === 'oscuro';
            const tileUrl = isDark
                ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

            L.tileLayer(tileUrl, {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(state.map);

            // Listener para inicializar botones de favoritos cuando se abre un popup
            if (USUARIO_AUTENTICADO) {
                state.map.on('popupopen', inicializarBotonFavoritoEnPopup);
            }

            // Cargar GeoJSON de municipios
            await loadGeoJSON();

            // Cargar GeoJSON de provincias
            await loadProvincias();

            // Cargar datos iniciales
            await loadData();
        } catch (error) {
            console.error('Error inicializando mapa:', error);
            document.getElementById('mapa-container').innerHTML = '<div class="loading"><span style="color: #ef4444;">Error al cargar el mapa</span></div>';
        }
    }

    // Cargar GeoJSON de municipios
    async function loadGeoJSON() {
        try {
            const response = await fetch(CONFIG.geojsonUrl);
            if (!response.ok) throw new Error('Error cargando GeoJSON');
            state.geojsonData = await response.json();
            console.log(`GeoJSON cargado: ${state.geojsonData.features.length} municipios`);
        } catch (error) {
            console.error('Error cargando GeoJSON:', error);
            throw error;
        }
    }

    // Cargar GeoJSON de provincias
    async function loadProvincias() {
        try {
            const response = await fetch(CONFIG.provinciasUrl);
            if (!response.ok) throw new Error('Error cargando límites provinciales');
            state.provinciasData = await response.json();
            console.log(`Límites provinciales cargados: ${state.provinciasData.features.length} provincias`);
        } catch (error) {
            console.error('Error cargando provincias:', error);
            throw error;
        }
    }

    // Cargar datos de la API
    async function loadData() {
        try {
            const url = `${CONFIG.apiUrl}?ano=${state.currentYear}&tipo_evento=${state.currentEvent}`;
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error cargando datos');

            state.currentData = await response.json();

            // Actualizar estadística total
            const total = Object.values(state.currentData.datos).reduce((sum, m) => sum + m.valor, 0);
            document.getElementById('stat-total').textContent = total.toLocaleString('es-ES');

            // Renderizar mapa
            renderMap();

            // Actualizar leyenda
            updateLegend();
        } catch (error) {
            console.error('Error cargando datos:', error);
            alert('Error al cargar los datos demográficos');
        }
    }

    // Renderizar mapa con coropletas
    function renderMap() {
        // Remover capa anterior
        if (state.geojsonLayer) {
            state.map.removeLayer(state.geojsonLayer);
        }

        // Obtener escala de colores
        const colors = CONFIG.colorScales[state.currentEvent];
        const quantiles = state.currentData.estadisticas.quantiles;

        console.log('Renderizando mapa con cuantiles:', quantiles);
        console.log('Total datos:', Object.keys(state.currentData.datos).length);

        // Función para obtener color según valor
        function getColor(valor) {
            if (!valor || valor === 0) return '#e5e7eb'; // Sin datos: gris claro
            if (valor <= quantiles.q20) return colors[0];
            if (valor <= quantiles.q40) return colors[1];
            if (valor <= quantiles.q60) return colors[2];
            if (valor <= quantiles.q80) return colors[3];
            return colors[4];
        }

        let municipiosConDatos = 0;
        let municipiosSinDatos = 0;

        // Crear capa GeoJSON
        state.geojsonLayer = L.geoJSON(state.geojsonData, {
            style: function(feature) {
                const codigoIne = feature.properties.c_mun;
                const municipio = state.currentData.datos[codigoIne];
                const valor = municipio ? municipio.valor : 0;
                const color = getColor(valor);

                if (municipio) {
                    municipiosConDatos++;
                } else {
                    municipiosSinDatos++;
                }

                return {
                    fillColor: color,
                    weight: 1,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            },
            onEachFeature: function(feature, layer) {
                const codigoIne = feature.properties.c_mun;
                const municipio = state.currentData.datos[codigoIne];
                const nombreMunicipio = feature.properties.n_mun;

                if (municipio) {
                    // Popup con información
                    const botonFavorito = USUARIO_AUTENTICADO
                        ? `<button
                             data-favorito-municipio="${municipio.id}"
                             class="boton-favorito-mapa"
                             style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background: var(--bg-tertiary); color: var(--text-primary); cursor: pointer; margin-top: 0.5rem;"
                             title="Añadir a favoritos"
                           >☆</button>`
                        : '';

                    layer.bindPopup(`
                        <div style="min-width: 200px; color: var(--text-primary);">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                                <h3 style="margin: 0 0 0.5rem 0; font-weight: 600; flex: 1;">${municipio.nombre}</h3>
                                ${botonFavorito}
                            </div>
                            <p style="margin: 0; font-size: 0.875rem;">
                                <strong>${capitalize(state.currentEvent)}s:</strong> ${municipio.valor}
                            </p>
                            <a href="${BASE_URL}/analisis-demografico/municipio/${municipio.id}"
                               style="font-size: 0.875rem; color: var(--primary-color); text-decoration: none;">
                                Ver detalles →
                            </a>
                        </div>
                    `);

                    // Efectos hover
                    layer.on({
                        mouseover: function(e) {
                            const layer = e.target;
                            layer.setStyle({
                                weight: 3,
                                color: '#666',
                                fillOpacity: 0.9
                            });
                            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                layer.bringToFront();
                            }
                        },
                        mouseout: function(e) {
                            state.geojsonLayer.resetStyle(e.target);
                        }
                    });
                } else {
                    // Municipio sin datos
                    layer.bindPopup(`
                        <div style="min-width: 200px; color: var(--text-primary);">
                            <h3 style="margin: 0 0 0.5rem 0; font-weight: 600;">${nombreMunicipio}</h3>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
                                Sin datos para este año
                            </p>
                        </div>
                    `);
                }
            }
        }).addTo(state.map);

        console.log(`Mapa renderizado: ${municipiosConDatos} con datos, ${municipiosSinDatos} sin datos`);

        // Renderizar límites provinciales
        renderProvincias();

        // Ajustar vista para centrar en Castilla y León
        if (state.geojsonLayer.getBounds().isValid()) {
            state.map.fitBounds(state.geojsonLayer.getBounds(), { padding: [20, 20] });
        }
    }

    // Renderizar límites provinciales
    function renderProvincias() {
        // Remover capa anterior si existe
        if (state.provinciasLayer) {
            state.map.removeLayer(state.provinciasLayer);
        }

        // Crear capa de provincias con líneas negras marcadas
        state.provinciasLayer = L.geoJSON(state.provinciasData, {
            style: {
                fillColor: 'transparent',
                fillOpacity: 0,
                weight: 3,
                opacity: 0.8,
                color: '#000000',  // Negro para mejor contraste
                dashArray: '8, 4'
            },
            interactive: false,  // Las líneas no capturan eventos de clic
            onEachFeature: function(feature, layer) {
                // Agregar tooltip con nombre de la provincia
                layer.bindTooltip(feature.properties.n_prov, {
                    permanent: false,
                    direction: 'center',
                    className: 'provincia-tooltip',
                    opacity: 1
                });
            }
        }).addTo(state.map);

        // Traer la capa de provincias al frente (solo visualmente, no para eventos)
        state.provinciasLayer.bringToFront();
    }

    // Actualizar leyenda
    function updateLegend() {
        const colors = CONFIG.colorScales[state.currentEvent];
        const quantiles = state.currentData.estadisticas.quantiles;

        const labels = [
            { color: '#e5e7eb', label: 'Sin datos' },
            { color: colors[0], label: `1 - ${Math.round(quantiles.q20)}` },
            { color: colors[1], label: `${Math.round(quantiles.q20 + 1)} - ${Math.round(quantiles.q40)}` },
            { color: colors[2], label: `${Math.round(quantiles.q40 + 1)} - ${Math.round(quantiles.q60)}` },
            { color: colors[3], label: `${Math.round(quantiles.q60 + 1)} - ${Math.round(quantiles.q80)}` },
            { color: colors[4], label: `${Math.round(quantiles.q80 + 1)}+` }
        ];

        document.getElementById('legend-content').innerHTML = labels.map(item => `
            <div class="leyenda-item">
                <div class="leyenda-color" style="background-color: ${item.color};"></div>
                <span style="color: var(--text-primary);">${item.label}</span>
            </div>
        `).join('');
    }

    // Utilidad: Capitalizar
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ========== FUNCIONALIDAD DE FAVORITOS EN POPUP ==========

    // Inicializar botón de favorito cuando se abre un popup
    function inicializarBotonFavoritoEnPopup(e) {
        const popup = e.popup;
        const contenido = popup.getElement();
        const boton = contenido.querySelector('[data-favorito-municipio]');

        if (!boton || boton.dataset.inicializado) return;

        const municipioId = parseInt(boton.getAttribute('data-favorito-municipio'));
        boton.dataset.inicializado = 'true';

        // Verificar si es favorito y actualizar estado visual
        if (window.gestorFavoritos) {
            const esFavorito = window.gestorFavoritos.esFavorito(municipioId);
            actualizarEstadoBotonMapa(boton, esFavorito);
        }

        // Añadir event listener
        boton.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (!window.gestorFavoritos) {
                alert('Error: El sistema de favoritos no está disponible');
                return;
            }

            boton.disabled = true;
            const esFavorito = window.gestorFavoritos.esFavorito(municipioId);

            try {
                if (esFavorito) {
                    await window.gestorFavoritos.eliminarFavorito(municipioId);
                } else {
                    await window.gestorFavoritos.agregarFavorito(municipioId);
                }
                actualizarEstadoBotonMapa(boton, !esFavorito);
            } catch (error) {
                alert(error.message || 'Error al actualizar favorito');
            } finally {
                boton.disabled = false;
            }
        });
    }

    // Actualizar estado visual del botón de favorito en el mapa
    function actualizarEstadoBotonMapa(boton, esFavorito) {
        if (esFavorito) {
            boton.innerHTML = '⭐';
            boton.title = 'Eliminar de favoritos';
            boton.style.background = 'var(--primary-color)';
            boton.style.color = 'white';
            boton.style.borderColor = 'var(--primary-color)';
        } else {
            boton.innerHTML = '☆';
            boton.title = 'Añadir a favoritos';
            boton.style.background = 'var(--bg-tertiary)';
            boton.style.color = 'var(--text-primary)';
            boton.style.borderColor = 'var(--border-color)';
        }
    }

    // ========== FUNCIONALIDAD DE BÚSQUEDA ==========
    let municipiosList = [];
    let highlightedLayer = null;

    // Construir lista de municipios desde el GeoJSON
    function buildMunicipiosList() {
        municipiosList = state.geojsonData.features.map(feature => ({
            nombre: feature.properties.n_mun || '',
            codigo: feature.properties.c_mun || '',
            provincia: feature.properties.n_prov || '',
            bounds: L.geoJSON(feature).getBounds()
        })).sort((a, b) => a.nombre.localeCompare(b.nombre));
    }

    // Buscar municipios por nombre
    function searchMunicipios(query) {
        if (!query || query.length < 2) {
            return [];
        }

        const normalizedQuery = query.toLowerCase().trim();
        return municipiosList
            .filter(m => m.nombre.toLowerCase().includes(normalizedQuery))
            .slice(0, 10); // Limitar a 10 resultados
    }

    // Mostrar resultados de búsqueda
    function showSearchResults(results) {
        const resultsContainer = document.getElementById('search-results');

        if (results.length === 0) {
            resultsContainer.classList.remove('show');
            return;
        }

        resultsContainer.innerHTML = results.map(m => `
            <div class="search-result-item" data-codigo="${m.codigo}">
                <div class="municipio-nombre">${m.nombre}</div>
                <div class="municipio-provincia">${m.provincia}</div>
            </div>
        `).join('');

        resultsContainer.classList.add('show');

        // Agregar event listeners a los resultados
        resultsContainer.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const codigo = this.dataset.codigo;
                zoomToMunicipio(codigo);
                document.getElementById('search-municipio').value = this.querySelector('.municipio-nombre').textContent;
                resultsContainer.classList.remove('show');
            });
        });
    }

    // Hacer zoom a un municipio
    function zoomToMunicipio(codigoIne) {
        // Quitar resaltado anterior
        if (highlightedLayer) {
            state.geojsonLayer.resetStyle(highlightedLayer);
            highlightedLayer = null;
        }

        // Buscar el municipio en el GeoJSON
        const municipio = municipiosList.find(m => m.codigo === codigoIne);
        if (!municipio) return;

        // Hacer zoom al municipio
        state.map.fitBounds(municipio.bounds, {
            padding: [50, 50],
            maxZoom: 12
        });

        // Resaltar el municipio en el mapa
        state.geojsonLayer.eachLayer(layer => {
            if (layer.feature && layer.feature.properties.c_mun === codigoIne) {
                // Resaltar con estilo especial
                layer.setStyle({
                    weight: 4,
                    color: '#fbbf24',
                    fillOpacity: 0.9
                });

                // Abrir popup si existe
                if (layer.getPopup()) {
                    layer.openPopup();
                }

                // Guardar referencia para poder quitar el resaltado después
                highlightedLayer = layer;

                // Traer al frente
                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToFront();
                }
            }
        });
    }

    // Event listeners para búsqueda
    const searchInput = document.getElementById('search-municipio');
    const searchResults = document.getElementById('search-results');

    searchInput.addEventListener('input', function() {
        const query = this.value;
        const results = searchMunicipios(query);
        showSearchResults(results);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            const results = searchMunicipios(this.value);
            showSearchResults(results);
        }
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });

    // Construir lista de municipios cuando se carga el GeoJSON
    const originalLoadGeoJSON = loadGeoJSON;
    loadGeoJSON = async function() {
        await originalLoadGeoJSON();
        buildMunicipiosList();
    };

    // Event listeners
    document.getElementById('select-ano').addEventListener('change', function() {
        state.currentYear = parseInt(this.value);
        loadData();
    });

    document.getElementById('select-evento').addEventListener('change', function() {
        state.currentEvent = this.value;
        loadData();
    });

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
</script>
@endsection
