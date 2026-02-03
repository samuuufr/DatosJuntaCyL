document.addEventListener('DOMContentLoaded', () => {
    const botonMenuHamburguesa = document.getElementById('boton-menu-hamburguesa');
    const menuPrincipal = document.getElementById('menu-principal');
    const overlay = document.getElementById('navbar-overlay');

    const cerrarMenu = () => {
        if (menuPrincipal.classList.contains('menu-abierto')) {
            menuPrincipal.classList.remove('menu-abierto');
            botonMenuHamburguesa.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-movil-abierto');
            overlay.classList.remove('activo');
            botonMenuHamburguesa.setAttribute('aria-label', 'Abrir menú de navegación');
        }
    };

    const abrirMenu = () => {
        menuPrincipal.classList.add('menu-abierto');
        botonMenuHamburguesa.setAttribute('aria-expanded', 'true');
        document.body.classList.add('menu-movil-abierto');
        overlay.classList.add('activo');
        botonMenuHamburguesa.setAttribute('aria-label', 'Cerrar menú de navegación');
    }

    const toggleMenu = () => {
        if (menuPrincipal.classList.contains('menu-abierto')) {
            cerrarMenu();
        } else {
            abrirMenu();
        }
    };

    if (botonMenuHamburguesa && menuPrincipal && overlay) {
        botonMenuHamburguesa.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        overlay.addEventListener('click', cerrarMenu);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menuPrincipal.classList.contains('menu-abierto')) {
                cerrarMenu();
            }
        });

        const enlacesMenu = menuPrincipal.querySelectorAll('a');
        enlacesMenu.forEach(enlace => {
            enlace.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    cerrarMenu();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                cerrarMenu();
            }
        });
    }

    // Lógica para el menú desplegable del usuario
    const botonMenuUsuario = document.getElementById('boton-menu-usuario');
    const menuDesplegableUsuario = document.getElementById('menu-desplegable-usuario');

    if (botonMenuUsuario && menuDesplegableUsuario) {
        botonMenuUsuario.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevenir que el listener de window lo cierre inmediatamente
            const isHidden = menuDesplegableUsuario.classList.toggle('hidden');
            botonMenuUsuario.setAttribute('aria-expanded', !isHidden);
        });
    }

    // Cerrar menús si se hace clic fuera de ellos
    window.addEventListener('click', (e) => {
        // Cierre del menú de usuario
        if (menuDesplegableUsuario && !menuDesplegableUsuario.classList.contains('hidden')) {
            if (!botonMenuUsuario.contains(e.target) && !menuDesplegableUsuario.contains(e.target)) {
                menuDesplegableUsuario.classList.add('hidden');
                botonMenuUsuario.setAttribute('aria-expanded', 'false');
            }
        }
    });
});
