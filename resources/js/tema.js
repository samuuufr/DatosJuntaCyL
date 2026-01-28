/**
 * Gestor de temas oscuro/claro
 * Almacena la preferencia en localStorage y sincroniza con el DOM
 */

class GestorTema {
  constructor() {
    this.claveAlmacenamiento = 'app-tema';
    this.atributoTema = 'data-tema';
    this.inicializar();
  }

  /**
   * Inicializa el sistema de temas
   */
  inicializar() {
    this.cargarTema();
    this.adjuntarEventos();
    this.observarPreferenciaDelSistema();
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

    document.documentElement.setAttribute(this.atributoTema, tema);
    localStorage.setItem(this.claveAlmacenamiento, tema);
    this.actualizarBotonToggle(tema);
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
    botonesFijo.forEach(boton => {
      boton.addEventListener('click', () => this.alternarTema());
    });

    // También el ID directo
    const botonPrincipal = document.getElementById('boton-toggle-tema');
    if (botonPrincipal) {
      botonPrincipal.addEventListener('click', () => this.alternarTema());
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

// Inicializa al cargar el documento
document.addEventListener('DOMContentLoaded', () => {
  window.gestorTema = new GestorTema();
});
