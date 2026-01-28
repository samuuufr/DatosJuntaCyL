<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Análisis Demográfico') - Portal de Datos</title>
    @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js', 'resources/js/theme.js'])
    @yield('extra_css')
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
                <a href="{{ route('demographic.dashboard') }}" class="sidebar-menu-item @if(Route::currentRouteName() === 'demographic.dashboard') active @endif">
                    <span class="sidebar-icon">📈</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('demographic.comparar') }}" class="sidebar-menu-item @if(Route::currentRouteName() === 'demographic.comparar') active @endif">
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
        <!-- HEADER -->
        <header class="header">
            <div class="header-title">
                <h1>@yield('page_title', 'Análisis Demográfico')</h1>
                <p>@yield('page_description', 'Análisis de datos MNP de Castilla y León')</p>
            </div>
            <div class="header-controls">
                <button id="theme-toggle-btn" class="theme-toggle" data-toggle-theme title="Cambiar tema">
                    🌙
                </button>
            </div>
        </header>

        <!-- CONTENIDO -->
        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

@yield('extra_js')

</body>
</html>
