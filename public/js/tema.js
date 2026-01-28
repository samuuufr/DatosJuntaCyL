/**
 * Gestor de temas oscuro/claro
 * Almacena la preferencia en localStorage y sincroniza con el DOM
 */

class GestorTema {
  constructor() {
    this.claveAlmacenamiento = 'app-tema';
    this.atributoTema = 'data-tema';
    console.log('🚀 GestorTema: Inicializando...');
    this.inicializar();
  }

  /**
   * Inicializa el sistema de temas
   */
  inicializar() {
    try {
      console.log('📋 GestorTema: Cargando tema...');
      this.cargarTema();

      console.log('🔗 GestorTema: Adjuntando eventos...');
      this.adjuntarEventos();

      console.log('👁️ GestorTema: Observando preferencias del sistema...');
      this.observarPreferenciaDelSistema();

      console.log('✅ GestorTema: Inicialización completada');
    } catch (error) {
      console.error('❌ GestorTema: Error durante inicialización:', error);
    }
  }

  /**
   * Carga el tema guardado o usa la preferencia del sistema
   */
  cargarTema() {
    const temaGuardado = localStorage.getItem(this.claveAlmacenamiento);

    if (temaGuardado) {
      this.establecerTema(temaGuardado);
    } else {
      // Usa la preferencia del sistema operativo
      const prefiereDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
      this.establecerTema(prefiereDarkMode ? 'oscuro' : 'claro');
    }
  }

  /**
   * Establece el tema y guarda en localStorage
   * @param {string} tema - 'claro' u 'oscuro'
   */
  establecerTema(tema) {
    if (tema !== 'claro' && tema !== 'oscuro') {
      console.warn(`Tema inválido: ${tema}. Usando 'claro'.`);
      tema = 'claro';
    }

    // Establecer atributo en <html>
    document.documentElement.setAttribute(this.atributoTema, tema);

    // También en <body> como respaldo
    document.body.setAttribute(this.atributoTema, tema);

    localStorage.setItem(this.claveAlmacenamiento, tema);
    this.actualizarBotonToggle(tema);

    console.log(`✅ Tema establecido a: ${tema}`);
  }

  /**
   * Alterna entre tema claro y oscuro
   */
  alternarTema() {
    const temaActual = document.documentElement.getAttribute(this.atributoTema);
    const nuevoTema = temaActual === 'oscuro' ? 'claro' : 'oscuro';
    this.establecerTema(nuevoTema);
  }

  /**
   * Obtiene el tema actual
   * @returns {string} - 'claro' u 'oscuro'
   */
  obtenerTemaActual() {
    return document.documentElement.getAttribute(this.atributoTema);
  }

  /**
   * Actualiza el icono del botón de toggle
   * @param {string} tema - 'claro' u 'oscuro'
   */
  actualizarBotonToggle(tema) {
    const boton = document.getElementById('boton-toggle-tema');
    if (boton) {
      boton.textContent = tema === 'oscuro' ? '☀️' : '🌙';
      boton.setAttribute('aria-label', tema === 'oscuro' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    }
  }

  /**
   * Adjunta listeners a botones de toggle
   */
  adjuntarEventos() {
    const botonesFijo = document.querySelectorAll('[data-alternar-tema]');
    console.log(`🔍 Encontrados ${botonesFijo.length} botones con [data-alternar-tema]`);

    botonesFijo.forEach((boton, index) => {
      boton.addEventListener('click', () => {
        console.log(`🖱️ Click en botón #${index + 1}`);
        this.alternarTema();
      });
      console.log(`✓ Listener adjuntado a botón #${index + 1}`);
    });

    // También el ID directo
    const botonPrincipal = document.getElementById('boton-toggle-tema');
    console.log(`🔍 Botón con ID 'boton-toggle-tema':`, botonPrincipal ? 'ENCONTRADO' : 'NO ENCONTRADO');

    if (botonPrincipal) {
      botonPrincipal.addEventListener('click', () => {
        console.log(`🖱️ Click en boton-toggle-tema`);
        this.alternarTema();
      });
      console.log(`✓ Listener adjuntado a #boton-toggle-tema`);
    }
  }

  /**
   * Observa cambios en la preferencia del sistema operativo
   */
  observarPreferenciaDelSistema() {
    const consultaMedia = window.matchMedia('(prefers-color-scheme: dark)');

    // Usa el método moderno si está disponible
    if (consultaMedia.addEventListener) {
      consultaMedia.addEventListener('change', (e) => {
        // Solo actualiza si el usuario no ha establecido una preferencia manual
        if (!localStorage.getItem(this.claveAlmacenamiento)) {
          this.establecerTema(e.matches ? 'oscuro' : 'claro');
        }
      });
    }
  }
}

// Inicializa inmediatamente si el DOM está listo, o espera si no
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.gestorTema = new GestorTema();
  });
} else {
  // El script se carga al final del body, así que el DOM ya está listo
  window.gestorTema = new GestorTema();
}
