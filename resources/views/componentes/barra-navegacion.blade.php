<!-- BARRA DE NAVEGACIÓN SUPERIOR -->
<nav class="navbar">
    <div class="navbar-contenedor">
        <div class="navbar-izquierda">
            <a href="{{ url('/') }}" class="navbar-logo">
                <span class="navbar-logo-icon">📊</span>
                <span class="navbar-logo-texto">CyL Data</span>
            </a>
        </div>

        <div class="navbar-centro">
            <ul class="navbar-menu">
                <li>
                    <a href="{{ route('analisis-demografico.panel') }}"
                       class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.panel') active @endif">
                        <span>📈</span> Panel
                    </a>
                </li>
                <li>
                    <a href="{{ route('analisis-demografico.comparar') }}"
                       class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.comparar') active @endif">
                        <span>⚖️</span> Comparar
                    </a>
                </li>
                <li>
                    <a href="{{ route('provincias.index') }}"
                       class="navbar-enlace @if(Route::currentRouteName() === 'provincias.index') active @endif">
                        <span>📍</span> Provincias
                    </a>
                </li>
                <li>
                    <a href="{{ route('municipios.index') }}"
                       class="navbar-enlace @if(Route::currentRouteName() === 'municipios.index') active @endif">
                        <span>🏘️</span> Municipios
                    </a>
                </li>
                <li>
                    <a href="{{ route('analisis-demografico.mapa-calor') }}"
                       class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.mapa-calor') active @endif">
                        <span>🗺️</span> Mapa de Calor
                    </a>
                </li>
            </ul>
        </div>

        <div class="navbar-derecha">
            <button id="boton-toggle-tema" class="navbar-boton-tema" data-alternar-tema aria-label="Cambiar tema">
                🌙
            </button>
        </div>
    </div>
</nav>
