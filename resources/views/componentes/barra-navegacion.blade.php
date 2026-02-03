<!-- BARRA DE NAVEGACIÓN SUPERIOR -->
<nav class="navbar" aria-label="Navegación principal">
    <div class="navbar-contenedor">
        <div class="navbar-izquierda">
            <a href="{{ url('/') }}" class="navbar-logo" aria-label="Ir al inicio - Demografía CyL">
                <img src="{{ asset('img/LOGOSINTEXTO.png') }}" alt="Demografía CyL - Portal de datos demográficos">
            </a>
        </div>

        <!-- Botón hamburguesa para móvil -->
        <button id="boton-menu-hamburguesa" class="navbar-hamburguesa" aria-expanded="false" aria-controls="menu-principal" aria-label="Abrir menú de navegación">
            <span class="hamburguesa-linea"></span>
            <span class="hamburguesa-linea"></span>
            <span class="hamburguesa-linea"></span>
        </button>

        <!-- Menú principal (se oculta en móvil) -->
        <div id="menu-principal" class="navbar-menu-contenedor">
            <div class="navbar-centro">
                <ul class="navbar-menu" role="menubar">
                    <li role="none">
                        <a href="{{ route('analisis-demografico.panel') }}"
                           class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.panel') active @endif"
                           role="menuitem"
                           @if(Route::currentRouteName() === 'analisis-demografico.panel') aria-current="page" @endif>
                             Panel
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ route('analisis-demografico.comparar') }}"
                           class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.comparar') active @endif"
                           role="menuitem"
                           @if(Route::currentRouteName() === 'analisis-demografico.comparar') aria-current="page" @endif>
                             Comparar
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ route('provincias.index') }}"
                           class="navbar-enlace @if(Route::currentRouteName() === 'provincias.index') active @endif"
                           role="menuitem"
                           @if(Route::currentRouteName() === 'provincias.index') aria-current="page" @endif>
                             Provincias
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ route('municipios.index') }}"
                           class="navbar-enlace @if(Route::currentRouteName() === 'municipios.index') active @endif"
                           role="menuitem"
                           @if(Route::currentRouteName() === 'municipios.index') aria-current="page" @endif>
                             Municipios
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ route('analisis-demografico.mapa-calor') }}"
                           class="navbar-enlace @if(Route::currentRouteName() === 'analisis-demografico.mapa-calor') active @endif"
                           role="menuitem"
                           @if(Route::currentRouteName() === 'analisis-demografico.mapa-calor') aria-current="page" @endif>
                             Mapa de Calor
                        </a>
                    </li>
                </ul>
            </div>

            <div class="navbar-derecha">
                @auth
                    <!-- Menú de usuario autenticado -->
                    <div class="navbar-usuario-menu">
                        <button id="boton-menu-usuario" class="navbar-usuario-boton" aria-expanded="false" aria-haspopup="true" aria-controls="menu-desplegable-usuario">
                            <span aria-hidden="true">👤</span>
                            <span class="navbar-usuario-nombre">{{ Auth::user()->nombre }}</span>
                            <span class="navbar-usuario-flecha" aria-hidden="true">▼</span>
                        </button>
                        <div id="menu-desplegable-usuario" class="navbar-dropdown hidden" role="menu" aria-label="Menú de usuario">
                            <a href="{{ route('perfil.mostrar') }}" class="navbar-dropdown-item" role="menuitem">
                                <span aria-hidden="true">⚙️</span> Mi Perfil
                            </a>
                            <a href="{{ route('perfil.favoritos') }}" class="navbar-dropdown-item" role="menuitem">
                                <span aria-hidden="true">⭐</span> Mis Favoritos
                            </a>
                            <hr class="navbar-dropdown-divider" role="separator">
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="navbar-dropdown-item navbar-dropdown-item-logout" role="menuitem">
                                    <span aria-hidden="true">🚪</span> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Botones para usuarios no autenticados -->
                    <div class="navbar-auth-botones">
                        <a href="{{ route('login') }}" class="navbar-boton-secundario">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('registro') }}" class="navbar-boton-primario">
                            Registrarse
                        </a>
                    </div>
                @endauth

                <button id="boton-toggle-tema" class="navbar-boton-tema" data-alternar-tema aria-label="Cambiar tema de color" aria-pressed="false">
                    <span aria-hidden="true">🌙</span>
                    <span class="sr-only">Cambiar a tema oscuro</span>
                </button>
            </div>
        </div>
    </div>
    <!-- Overlay para cerrar menú en móvil -->
    <div id="navbar-overlay" class="navbar-overlay"></div>
</nav>
