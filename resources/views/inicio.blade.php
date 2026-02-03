<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Datos de Castilla y León</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Estilos específicos para la página principal */

        .seccion-hero {
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            text-align: center;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .seccion-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--primary-color) 0%, transparent 70%);
            opacity: 0.1;
            border-radius: 50%;
        }

        .contenido-hero {
            max-width: 800px;
            position: relative;
            z-index: 1;
        }

        .contenido-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contenido-hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .boton-principal {
            display: inline-block;
            padding: 1rem 2.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: var(--shadow-lg);
        }

        .boton-principal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow);
            background-color: var(--primary-dark);
        }

        .seccion-acceso {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .seccion-acceso h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--text-primary);
        }

        .grid-acceso {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .tarjeta-acceso {
            padding: 2rem;
            background-color: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            min-height: 240px;
            overflow: hidden;
        }

        .tarjeta-acceso:hover {
            border-color: var(--primary-color);
            background-color: var(--bg-tertiary);
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .tarjeta-acceso-icono {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .tarjeta-acceso-titulo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .tarjeta-acceso-descripcion {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            flex-grow: 1;
            line-height: 1.6;
            font-size: 0.95rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .tarjeta-acceso-boton {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.375rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            align-self: flex-start;
        }

        .tarjeta-acceso:hover .tarjeta-acceso-boton {
            background-color: var(--primary-dark);
            transform: translateX(5px);
        }

        .seccion-info {
            padding: 4rem 2rem;
            background-color: var(--bg-secondary);
            margin-top: 2rem;
        }

        .seccion-info-contenido {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .seccion-info h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .seccion-info p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .lista-puntos {
            list-style: none;
            padding: 0;
        }

        .lista-puntos li {
            padding: 0.75rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .lista-puntos li::before {
            content: '✓';
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.25rem;
        }

        .estadisticas-rapidas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .estadistica-rapida {
            text-align: center;
            padding: 1.5rem;
            background-color: var(--bg-tertiary);
            border-radius: 0.5rem;
        }

        .estadistica-rapida-numero {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .estadistica-rapida-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .contenido-hero h1 {
                font-size: 2.5rem;
            }

            .contenido-hero p {
                font-size: 1rem;
            }

            .grid-acceso {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .tarjeta-acceso {
                min-height: auto;
                padding: 1.5rem;
            }

            .tarjeta-acceso-titulo {
                font-size: 1.25rem;
            }

            .seccion-info-contenido {
                grid-template-columns: 1fr;
            }

            .estadisticas-rapidas {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .grid-acceso {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Sobrescritura del botón de tema para la página de inicio */
        .navbar-boton-tema {
            background-color: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-boton-tema:hover {
            transform: rotate(20deg);
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
        }

        /* Mejora de botones de autenticación para la página de inicio */
        .navbar-derecha {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .navbar-auth-botones {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .navbar-boton-secundario {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1.25rem;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
            min-height: 42px;
        }

        .navbar-boton-secundario:hover {
            background-color: var(--bg-tertiary);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .navbar-boton-primario {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #ffffff;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35);
            white-space: nowrap;
            min-height: 42px;
        }

        .navbar-boton-primario:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(59, 130, 246, 0.45);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .navbar-boton-primario:active {
            transform: translateY(-1px);
        }

        /* Ajuste del botón de tema para mejor alineación */
        .navbar-boton-tema {
            background-color: var(--bg-secondary);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            min-width: 42px;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-boton-tema:hover {
            transform: rotate(20deg) translateY(-2px);
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>

<!-- BARRA DE NAVEGACIÓN -->
@include('componentes.barra-navegacion')

<!-- Saltar al contenido principal (accesibilidad) -->
<a href="#contenido-principal" class="sr-only focus:not-sr-only" style="position: absolute; top: 0; left: 0; background: var(--primary-color); color: white; padding: 0.5rem 1rem; z-index: 9999;">
    Saltar al contenido principal
</a>

<!-- SECCIÓN HERO -->
<main id="contenido-principal">
<section class="seccion-hero" aria-labelledby="titulo-hero">
    <div class="contenido-hero">
        <h1 id="titulo-hero">Portal de Datos Demográficos</h1>
        <p>Análisis integral del Movimiento Natural de la Población de Castilla y León</p>
        <a href="{{ route('analisis-demografico.panel') }}" class="boton-principal" aria-label="Explorar datos demográficos">
            Explorar Datos <span aria-hidden="true">→</span>
        </a>
    </div>
</section>

<!-- SECCIÓN ACCESO RÁPIDO -->
<section class="seccion-acceso" aria-labelledby="titulo-acceso">
    <h2 id="titulo-acceso">Acceso Rápido</h2>
    <div class="grid-acceso" role="list">
        <a href="{{ route('analisis-demografico.panel') }}" class="tarjeta-acceso" role="listitem" aria-label="Panel Principal: Visualiza un resumen completo de todas las provincias, municipios y estadísticas clave del MNP">
            <div class="tarjeta-acceso-icono" aria-hidden="true">📈</div>
            <div class="tarjeta-acceso-titulo">Panel Principal</div>
            <div class="tarjeta-acceso-descripcion">
                Visualiza un resumen completo de todas las provincias, municipios y estadísticas clave del MNP.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Panel</div>
        </a>

        <a href="{{ route('provincias.index') }}" class="tarjeta-acceso" role="listitem" aria-label="Provincias: Explora las 9 provincias de Castilla y León con datos demográficos detallados">
            <div class="tarjeta-acceso-icono" aria-hidden="true">🗺️</div>
            <div class="tarjeta-acceso-titulo">Provincias</div>
            <div class="tarjeta-acceso-descripcion">
                Explora las 9 provincias de Castilla y León con datos demográficos detallados.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Provincias</div>
        </a>

        <a href="{{ route('municipios.index') }}" class="tarjeta-acceso" role="listitem" aria-label="Municipios: Busca y analiza datos de municipios específicos con información detallada">
            <div class="tarjeta-acceso-icono" aria-hidden="true">🏘️</div>
            <div class="tarjeta-acceso-titulo">Municipios</div>
            <div class="tarjeta-acceso-descripcion">
                Busca y analiza datos de municipios específicos con información detallada.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Municipios</div>
        </a>

        <a href="{{ route('analisis-demografico.comparar') }}" class="tarjeta-acceso" role="listitem" aria-label="Comparar Provincias: Compara dos provincias lado a lado para identificar tendencias y diferencias">
            <div class="tarjeta-acceso-icono" aria-hidden="true">⚖️</div>
            <div class="tarjeta-acceso-titulo">Comparar Provincias</div>
            <div class="tarjeta-acceso-descripcion">
                Compara dos provincias lado a lado para identificar tendencias y diferencias.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Comparar</div>
        </a>

        <a href="{{ route('analisis-demografico.mapa-calor') }}" class="tarjeta-acceso" role="listitem" aria-label="Mapa de Calor: Visualiza la distribución geográfica de datos demográficos en un mapa interactivo">
            <div class="tarjeta-acceso-icono" aria-hidden="true">🔥</div>
            <div class="tarjeta-acceso-titulo">Mapa de Calor</div>
            <div class="tarjeta-acceso-descripcion">
                Visualiza la distribución geográfica de datos demográficos en un mapa interactivo.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Mapa</div>
        </a>

        @auth
        <a href="{{ route('perfil.mostrar') }}" class="tarjeta-acceso" role="listitem" aria-label="Mi Perfil: Gestiona tu información personal y preferencias de la cuenta">
            <div class="tarjeta-acceso-icono" aria-hidden="true">👤</div>
            <div class="tarjeta-acceso-titulo">Mi Perfil</div>
            <div class="tarjeta-acceso-descripcion">
                Gestiona tu información personal y preferencias de la cuenta.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Perfil</div>
        </a>

        <a href="{{ route('perfil.favoritos') }}" class="tarjeta-acceso" role="listitem" aria-label="Mis Favoritos: Accede rápidamente a tus municipios favoritos y sigue sus estadísticas">
            <div class="tarjeta-acceso-icono" aria-hidden="true">⭐</div>
            <div class="tarjeta-acceso-titulo">Mis Favoritos</div>
            <div class="tarjeta-acceso-descripcion">
                Accede rápidamente a tus municipios favoritos y sigue sus estadísticas.
            </div>
            <div class="tarjeta-acceso-boton" aria-hidden="true">Ver Favoritos</div>
        </a>
        @endauth
    </div>
</section>

<!-- SECCIÓN INFORMACIÓN CASTILLA Y LEÓN -->
<section class="seccion-info">
    <div class="seccion-info-contenido">
        <div>
            <h2>Castilla y León</h2>
            <p>
                Castilla y León es una comunidad autónoma del noroeste de España. Con una extensión de 94,223 km² y dividida en 9 provincias, es una de las regiones más grandes de la península ibérica.
            </p>
            <ul class="lista-puntos">
                <li>9 provincias históricas y administrativas</li>
                <li>2,249 municipios con datos completos</li>
                <li>Estadísticas demográficas desde años anteriores</li>
                <li>Más de 14,000 registros del MNP</li>
            </ul>
        </div>
        <div>
            <div class="estadisticas-rapidas">
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">9</div>
                    <div class="estadistica-rapida-label">Provincias</div>
                </div>
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">2,249</div>
                    <div class="estadistica-rapida-label">Municipios</div>
                </div>
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">14,000+</div>
                    <div class="estadistica-rapida-label">Datos MNP</div>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<!-- PIE DE PÁGINA -->
@include('componentes.pie-pagina')

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
.sr-only.focus\:not-sr-only:focus {
    position: static;
    width: auto;
    height: auto;
    padding: 0.5rem 1rem;
    margin: 0;
    overflow: visible;
    clip: auto;
    white-space: normal;
}
/* Estilos de focus visible */
a:focus-visible, button:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}
.tarjeta-acceso:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 4px;
}
</style>
</body>
</html>
