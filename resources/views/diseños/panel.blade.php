<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('titulo', 'Análisis Demográfico') - Portal de Datos CyL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css_adicional')
</head>
<body>

<!-- BARRA DE NAVEGACIÓN -->
@include('componentes.barra-navegacion')

<!-- CONTENIDO PRINCIPAL -->
<main class="contenedor-principal">
    @if(session('success'))
        <div class="max-w-7xl mx-auto mt-4 px-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto mt-4 px-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="contenedor-pagina">
        @yield('contenido')
    </div>

    <!-- PIE DE PÁGINA -->
    @include('componentes.pie-pagina')
</main>

@yield('js_adicional')

<!-- Script de respaldo para el gestor de temas -->
<script>
// Función de respaldo si el gestor de temas no carga correctamente
document.addEventListener('DOMContentLoaded', function() {
    // Si el gestor de temas no está disponible después de 2 segundos, inicializarlo manualmente
    setTimeout(function() {
        if (!window.gestorTema) {
            console.log('🔄 Inicializando gestor de temas como respaldo...');
            
            // Función simple de respaldo
            window.cambiarTemaRespaldo = function() {
                const html = document.documentElement;
                const temaActual = html.getAttribute('data-tema') || 'claro';
                const nuevoTema = temaActual === 'oscuro' ? 'claro' : 'oscuro';
                
                html.setAttribute('data-tema', nuevoTema);
                document.body.setAttribute('data-tema', nuevoTema);
                localStorage.setItem('app-tema', nuevoTema);
                
                // Actualizar icono
                const boton = document.getElementById('boton-toggle-tema');
                if (boton) {
                    boton.textContent = nuevoTema === 'oscuro' ? '☀️' : '🌙';
                }
                
                console.log('🎨 Tema cambiado a:', nuevoTema);
            };
            
            // Adjuntar evento al botón
            const boton = document.getElementById('boton-toggle-tema');
            if (boton) {
                boton.addEventListener('click', window.cambiarTemaRespaldo);
            }
            
            // Cargar tema guardado
            const temaGuardado = localStorage.getItem('app-tema');
            if (temaGuardado) {
                document.documentElement.setAttribute('data-tema', temaGuardado);
                document.body.setAttribute('data-tema', temaGuardado);
                
                // Actualizar icono
                const boton = document.getElementById('boton-toggle-tema');
                if (boton) {
                    boton.textContent = temaGuardado === 'oscuro' ? '☀️' : '🌙';
                }
            }
        }
    }, 2000);
});
</script>

</body>
</html>
