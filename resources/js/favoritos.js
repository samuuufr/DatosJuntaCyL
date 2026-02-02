/**
 * Sistema de gestión de favoritos
 * Permite a los usuarios añadir/eliminar municipios de su lista de favoritos
 */

class GestorFavoritos {
  constructor() {
    this.favoritosIds = new Set();
    this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
    this.inicializar();
  }

  /**
   * Inicializa el gestor de favoritos
   */
  async inicializar() {
    // Cargar la lista de favoritos del usuario si está autenticado
    await this.cargarFavoritos();

    // Inicializar botones de favoritos en la página
    this.inicializarBotones();
  }

  /**
   * Carga la lista de favoritos del usuario desde la API
   */
  async cargarFavoritos() {
    try {
      const response = await fetch(`${this.baseUrl}/api/perfil/favoritos/lista`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
      });

      if (response.ok) {
        const data = await response.json();
        this.favoritosIds = new Set(data.favoritos);
      }
    } catch (error) {
      // Si hay error, probablemente el usuario no está autenticado
      console.log('Usuario no autenticado o error al cargar favoritos');
    }
  }

  /**
   * Inicializa los botones de favoritos en la página
   */
  inicializarBotones() {
    const botones = document.querySelectorAll('[data-favorito-municipio]');

    botones.forEach(boton => {
      const municipioId = parseInt(boton.getAttribute('data-favorito-municipio'));

      // Actualizar el estado visual del botón
      this.actualizarBoton(boton, this.esFavorito(municipioId));

      // Añadir event listener
      boton.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleFavorito(municipioId, boton);
      });
    });
  }

  /**
   * Verifica si un municipio es favorito
   */
  esFavorito(municipioId) {
    return this.favoritosIds.has(municipioId);
  }

  /**
   * Añade o elimina un municipio de favoritos
   */
  async toggleFavorito(municipioId, boton) {
    const esFavorito = this.esFavorito(municipioId);

    try {
      boton.disabled = true;

      if (esFavorito) {
        await this.eliminarFavorito(municipioId);
      } else {
        await this.agregarFavorito(municipioId);
      }

      // Actualizar el estado visual
      this.actualizarBoton(boton, !esFavorito);

    } catch (error) {
      alert(error.message || 'Error al actualizar favorito');
    } finally {
      boton.disabled = false;
    }
  }

  /**
   * Añade un municipio a favoritos
   */
  async agregarFavorito(municipioId) {
    const response = await fetch(`${this.baseUrl}/api/perfil/favoritos/agregar`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ municipio_id: municipioId })
    });

    // Verificar el tipo de contenido de la respuesta
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('El servidor devolvió un error. Por favor, recarga la página.');
    }

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Error al añadir favorito');
    }

    this.favoritosIds.add(municipioId);
    this.mostrarNotificacion(data.message, 'success');

    // Disparar evento para notificar a otros componentes
    document.dispatchEvent(new CustomEvent('favoritosActualizados', {
      detail: { accion: 'agregar', municipioId }
    }));
  }

  /**
   * Elimina un municipio de favoritos
   */
  async eliminarFavorito(municipioId) {
    const response = await fetch(`${this.baseUrl}/api/perfil/favoritos/eliminar`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ municipio_id: municipioId })
    });

    // Verificar el tipo de contenido de la respuesta
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('El servidor devolvió un error. Por favor, recarga la página.');
    }

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Error al eliminar favorito');
    }

    this.favoritosIds.delete(municipioId);
    this.mostrarNotificacion(data.message, 'info');

    // Disparar evento para notificar a otros componentes
    document.dispatchEvent(new CustomEvent('favoritosActualizados', {
      detail: { accion: 'eliminar', municipioId }
    }));
  }

  /**
   * Actualiza el estado visual del botón de favorito
   */
  actualizarBoton(boton, esFavorito) {
    if (esFavorito) {
      boton.classList.add('favorito-activo');
      boton.innerHTML = '⭐ Favorito';
      boton.setAttribute('title', 'Eliminar de favoritos');
    } else {
      boton.classList.remove('favorito-activo');
      boton.innerHTML = '☆ Añadir a favoritos';
      boton.setAttribute('title', 'Añadir a favoritos');
    }
  }

  /**
   * Muestra una notificación temporal
   */
  mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion-favorito notificacion-${tipo}`;
    notificacion.textContent = mensaje;

    // Añadir al DOM
    document.body.appendChild(notificacion);

    // Mostrar con animación
    setTimeout(() => notificacion.classList.add('mostrar'), 100);

    // Ocultar y eliminar después de 3 segundos
    setTimeout(() => {
      notificacion.classList.remove('mostrar');
      setTimeout(() => notificacion.remove(), 300);
    }, 3000);
  }
}

// Inicializar cuando el DOM esté listo
function inicializarGestorFavoritos() {
  if (window.gestorFavoritos) {
    return; // Ya está inicializado
  }

  window.gestorFavoritos = new GestorFavoritos();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarGestorFavoritos);
} else {
  inicializarGestorFavoritos();
}

// También inicializar cuando Turbolinks/SPA lo necesite
document.addEventListener('turbo:load', inicializarGestorFavoritos);
document.addEventListener('page:load', inicializarGestorFavoritos);

export default GestorFavoritos;
