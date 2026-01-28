<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo_pagina', 'Análisis Demográfico') - Portal de Datos</title>
    <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
    @yield('css_adicional')
</head>
<body>

<div class="container-dashboard">
    <!-- SIDEBAR LATERAL -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span>📊</span>
                <span>CyL Data</span>
            </div>
        </div>

        <!-- Sección: Análisis -->
        <div class="sidebar-section">
            <h3 class="sidebar-section-title">Análisis</h3>
            <nav class="sidebar-menu">
                <a href="{{ route('analisis-demografico.panel') }}" class="sidebar-menu-item @if(Route::currentRouteName() === 'analisis-demografico.panel') active @endif">
                    <span class="sidebar-icon">📈</span>
                    <span>Panel</span>
                </a>
                <a href="{{ route('analisis-demografico.comparar') }}" class="sidebar-menu-item @if(Route::currentRouteName() === 'analisis-demografico.comparar') active @endif">
                    <span class="sidebar-icon">⚖️</span>
                    <span>Comparar</span>
                </a>
            </nav>
        </div>

        <!-- Sección: Datos -->
        <div class="sidebar-section">
            <h3 class="sidebar-section-title">Datos</h3>
            <nav class="sidebar-menu">
                <a href="#provincias" class="sidebar-menu-item">
                    <span class="sidebar-icon">📍</span>
                    <span>Provincias</span>
                </a>
                <a href="#municipios" class="sidebar-menu-item">
                    <span class="sidebar-icon">🏘️</span>
                    <span>Municipios</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <!-- ENCABEZADO -->
        <header class="header">
            <div class="header-title">
                <h1>@yield('titulo_pagina', 'Análisis Demográfico')</h1>
                <p>@yield('descripcion_pagina', 'Análisis de datos MNP de Castilla y León')</p>
            </div>
            <div class="header-controls">
                <button id="boton-toggle-tema" class="theme-toggle" data-alternar-tema title="Cambiar tema">
                    🌙
                </button>
            </div>
        </header>

        <!-- CONTENIDO -->
        <div class="content">
            @yield('contenido')
        </div>
    </div>
</div>

@yield('js_adicional')
<script src="{{ asset('js/tema.js') }}"></script>

</body>
</html>
