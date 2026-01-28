<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo_pagina', 'Análisis Demográfico') - Portal de Datos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css_adicional')
</head>
<body>

<!-- BARRA DE NAVEGACIÓN -->
@include('componentes.barra-navegacion')

<!-- CONTENIDO PRINCIPAL -->
<main class="contenedor-principal">
    <div class="contenedor-pagina">
        <!-- ENCABEZADO DE PÁGINA -->
        <div class="encabezado-pagina">
            <div>
                <h1>@yield('titulo_pagina', 'Análisis Demográfico')</h1>
                <p>@yield('descripcion_pagina', 'Análisis de datos MNP de Castilla y León')</p>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="contenido-pagina">
            @yield('contenido')
        </div>
    </div>
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
