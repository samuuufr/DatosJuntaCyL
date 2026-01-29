/**
 * Gestor de temas oscuro/claro
 * Almacena la preferencia en localStorage y sincroniza con el DOM
 */

class GestorTema {
  constructor() {
    this.claveAlmacenamiento = 'app-tema';
    this.atributoTema = 'data-tema';
    this.handleClick = this.handleClick.bind(this);
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
    // Remover listeners existentes para evitar duplicados
    this.removerListenersExistentes();

    const botonesFijo = document.querySelectorAll('[data-alternar-tema]');
    console.log(`🔍 Encontrados ${botonesFijo.length} botones con [data-alternar-tema]`);

    botonesFijo.forEach((boton, index) => {
      boton.addEventListener('click', this.handleClick);
      console.log(`✓ Listener adjuntado a botón #${index + 1}`);
    });

    // También el ID directo
    const botonPrincipal = document.getElementById('boton-toggle-tema');
    console.log(`🔍 Botón con ID 'boton-toggle-tema':`, botonPrincipal ? 'ENCONTRADO' : 'NO ENCONTRADO');

    if (botonPrincipal) {
      botonPrincipal.addEventListener('click', this.handleClick);
      console.log(`✓ Listener adjuntado a #boton-toggle-tema`);
    }
  }

  /**
   * Remueve listeners existentes para evitar duplicados
   */
  removerListenersExistentes() {
    const botones = document.querySelectorAll('[data-alternar-tema], #boton-toggle-tema');
    botones.forEach(boton => {
      boton.removeEventListener('click', this.handleClick);
    });
  }

  /**
   * Manejador de click con el contexto correcto
   */
  handleClick = () => {
    console.log(`🖱️ Click en botón de tema`);
    this.alternarTema();
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

// Función de inicialización segura
function inicializarGestorTema() {
  // Evitar inicialización múltiple
  if (window.gestorTema) {
    console.log('🔄 GestorTema ya inicializado, reutilizando instancia');
    return;
  }
  
  console.log('🚀 Inicializando GestorTema...');
  window.gestorTema = new GestorTema();
}

// Inicializa inmediatamente si el DOM está listo, o espera si no
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarGestorTema);
} else {
  // El script se carga después de que el DOM está listo
  inicializarGestorTema();
}

// También inicializa cuando Turbolinks/SPA lo necesite
document.addEventListener('turbo:load', inicializarGestorTema);
document.addEventListener('page:load', inicializarGestorTema);

// Exportar para uso en otros módulos
export default GestorTema;

/**
 * Gestor de menú desplegable de usuario
 */
function inicializarMenuUsuario() {
  const botonMenu = document.getElementById('boton-menu-usuario');
  const menuDesplegable = document.getElementById('menu-desplegable-usuario');

  if (!botonMenu || !menuDesplegable) {
    // El menú no existe (usuario no autenticado)
    return;
  }

  // Toggle del menú al hacer click en el botón
  botonMenu.addEventListener('click', (e) => {
    e.stopPropagation();
    menuDesplegable.classList.toggle('hidden');
  });

  // Cerrar el menú al hacer click fuera de él
  document.addEventListener('click', (e) => {
    if (!botonMenu.contains(e.target) && !menuDesplegable.contains(e.target)) {
      menuDesplegable.classList.add('hidden');
    }
  });

  // Cerrar el menú al presionar Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      menuDesplegable.classList.add('hidden');
    }
  });
}

// Inicializar menú de usuario cuando el DOM esté listo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarMenuUsuario);
} else {
  inicializarMenuUsuario();
}

// También inicializar cuando Turbolinks/SPA lo necesite
document.addEventListener('turbo:load', inicializarMenuUsuario);
document.addEventListener('page:load', inicializarMenuUsuario);
