/**
 * Sistema de gestión de temas oscuro/claro
 * Almacena la preferencia en localStorage y sincroniza con el DOM
 */

class ThemeManager {
  constructor() {
    this.storageKey = 'app-theme';
    this.themeAttribute = 'data-theme';
    this.init();
  }

  /**
   * Inicializa el sistema de temas
   */
  init() {
    this.loadTheme();
    this.attachEventListeners();
    this.observeSystemPreference();
  }

  /**
   * Carga el tema guardado o usa la preferencia del sistema
   */
  loadTheme() {
    const savedTheme = localStorage.getItem(this.storageKey);

    if (savedTheme) {
      this.setTheme(savedTheme);
    } else {
      // Usa la preferencia del sistema operativo
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      this.setTheme(prefersDark ? 'dark' : 'light');
    }
  }

  /**
   * Establece el tema y guarda en localStorage
   * @param {string} theme - 'light' o 'dark'
   */
  setTheme(theme) {
    if (theme !== 'light' && theme !== 'dark') {
      console.warn(`Tema inválido: ${theme}. Usando 'light'.`);
      theme = 'light';
    }

    document.documentElement.setAttribute(this.themeAttribute, theme);
    localStorage.setItem(this.storageKey, theme);
    this.updateToggleButton(theme);
  }

  /**
   * Alterna entre tema claro y oscuro
   */
  toggleTheme() {
    const currentTheme = document.documentElement.getAttribute(this.themeAttribute);
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    this.setTheme(newTheme);
  }

  /**
   * Obtiene el tema actual
   * @returns {string} - 'light' o 'dark'
   */
  getCurrentTheme() {
    return document.documentElement.getAttribute(this.themeAttribute);
  }

  /**
   * Actualiza el icono del botón de toggle
   * @param {string} theme - 'light' o 'dark'
   */
  updateToggleButton(theme) {
    const button = document.getElementById('theme-toggle-btn');
    if (button) {
      button.textContent = theme === 'dark' ? '☀️' : '🌙';
      button.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    }
  }

  /**
   * Adjunta listeners a botones de toggle
   */
  attachEventListeners() {
    const toggleButtons = document.querySelectorAll('[data-toggle-theme]');
    toggleButtons.forEach(button => {
      button.addEventListener('click', () => this.toggleTheme());
    });

    // También el ID directo
    const primaryButton = document.getElementById('theme-toggle-btn');
    if (primaryButton) {
      primaryButton.addEventListener('click', () => this.toggleTheme());
    }
  }

  /**
   * Observa cambios en la preferencia del sistema operativo
   */
  observeSystemPreference() {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

    // Usa el método moderno si está disponible
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener('change', (e) => {
        // Solo actualiza si el usuario no ha establecido una preferencia manual
        if (!localStorage.getItem(this.storageKey)) {
          this.setTheme(e.matches ? 'dark' : 'light');
        }
      });
    }
  }
}

// Inicializa al cargar el documento
document.addEventListener('DOMContentLoaded', () => {
  window.themeManager = new ThemeManager();
});
