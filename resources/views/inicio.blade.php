<!DOCTYPE html>
<html lang="es" data-tema="claro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Datos de Castilla y León</title>
    <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
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
            box-shadow: 0 4px 15px rgba(139, 26, 63, 0.3);
        }

        .boton-principal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 26, 63, 0.4);
            background-color: var(--primary-dark);
        }

        .seccion-caracteristicas {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .seccion-caracteristicas h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--text-primary);
        }

        .grid-caracteristicas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .tarjeta-caracteristica {
            padding: 2rem;
            background-color: var(--bg-secondary);
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            text-align: center;
        }

        .tarjeta-caracteristica:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .icono-caracteristica {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .tarjeta-caracteristica h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .tarjeta-caracteristica p {
            color: var(--text-secondary);
            line-height: 1.6;
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .tarjeta-acceso {
            padding: 2.5rem;
            background-color: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
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
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .tarjeta-acceso-descripcion {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            flex-grow: 1;
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

        .footer-principal {
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-color);
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .contenido-hero h1 {
                font-size: 2.5rem;
            }

            .contenido-hero p {
                font-size: 1rem;
            }

            .seccion-info-contenido {
                grid-template-columns: 1fr;
            }

            .estadisticas-rapidas {
                grid-template-columns: 1fr;
            }
        }

        .navbar-boton-tema {
            background: transparent;
            border: none;
        /* Corrección de especificidad para el botón de tema */
        #boton-toggle-tema {
            background-color: transparent !important;
            border: none !important;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
            color: var(--text-primary) !important;
            padding: 0.5rem;
            transition: transform 0.3s ease;
        }

        #boton-toggle-tema:hover {
            transform: rotate(20deg);
        }
    </style>
</head>
<body>

<!-- BARRA DE NAVEGACIÓN -->
@include('componentes.barra-navegacion')

<!-- SECCIÓN HERO -->
<section class="seccion-hero">
    <div class="contenido-hero">
        <h1>Portal de Datos Demográficos</h1>
        <p>Análisis integral del Movimiento Natural de la Población de Castilla y León</p>
        <a href="{{ route('analisis-demografico.panel') }}" class="boton-principal">
            Explorar Datos →
        </a>
    </div>
</section>

<!-- SECCIÓN CARACTERÍSTICAS -->
<section class="seccion-caracteristicas">
    <h2>¿Qué puedes hacer aquí?</h2>
    <div class="grid-caracteristicas">
        <div class="tarjeta-caracteristica">
            <div class="icono-caracteristica">📊</div>
            <h3>Análisis Completo</h3>
            <p>Accede a datos detallados de nacimientos, matrimonios y defunciones de todas las provincias y municipios de Castilla y León</p>
        </div>
        <div class="tarjeta-caracteristica">
            <div class="icono-caracteristica">⚖️</div>
            <h3>Comparativas</h3>
            <p>Compara estadísticas demográficas entre provincias para identificar tendencias y diferencias regionales</p>
        </div>
        <div class="tarjeta-caracteristica">
            <div class="icono-caracteristica">📍</div>
            <h3>Detalle Territorial</h3>
            <p>Explora datos municipio por municipio para análisis más granulares de la región</p>
        </div>
    </div>
</section>

<!-- SECCIÓN ACCESO RÁPIDO -->
<section class="seccion-acceso">
    <h2>Acceso Rápido</h2>
    <div class="grid-acceso">
        <a href="{{ route('analisis-demografico.panel') }}" class="tarjeta-acceso">
            <div class="tarjeta-acceso-icono">📈</div>
            <div class="tarjeta-acceso-titulo">Panel Principal</div>
            <div class="tarjeta-acceso-descripcion">
                Visualiza un resumen completo de todas las provincias, municipios y estadísticas clave del MNP.
            </div>
            <div class="tarjeta-acceso-boton">Ver Panel</div>
        </a>

        <a href="{{ route('analisis-demografico.comparar') }}" class="tarjeta-acceso">
            <div class="tarjeta-acceso-icono">⚖️</div>
            <div class="tarjeta-acceso-titulo">Comparar Provincias</div>
            <div class="tarjeta-acceso-descripcion">
                Elige dos provincias y compara sus indicadores demográficos lado a lado.
            </div>
            <div class="tarjeta-acceso-boton">Comparar</div>
        </a>

        <a href="{{ route('analisis-demografico.panel') }}#provincias" class="tarjeta-acceso">
            <div class="tarjeta-acceso-icono">📍</div>
            <div class="tarjeta-acceso-titulo">Por Provincias</div>
            <div class="tarjeta-acceso-descripcion">
                Selecciona una provincia para ver en detalle sus municipios y datos MNP.
            </div>
            <div class="tarjeta-acceso-boton">Explorar</div>
        </a>
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
                <li>Miles de municipios con datos completos</li>
                <li>Estadísticas demográficas desde años anteriores</li>
                <li>Datos de Movimiento Natural de la Población (MNP)</li>
            </ul>
        </div>
        <div>
            <div class="estadisticas-rapidas">
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">9</div>
                    <div class="estadistica-rapida-label">Provincias</div>
                </div>
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">100+</div>
                    <div class="estadistica-rapida-label">Municipios</div>
                </div>
                <div class="estadistica-rapida">
                    <div class="estadistica-rapida-numero">∞</div>
                    <div class="estadistica-rapida-label">Datos MNP</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer-principal">
    <p>&copy; 2026 Portal de Datos Demográficos de Castilla y León. Todos los derechos reservados.</p>
</footer>

<script src="{{ asset('js/tema.js') }}"></script>

</body>
</html>
