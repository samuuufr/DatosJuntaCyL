# Sistema de Autenticación y Favoritos

## Resumen de Implementación

Se ha implementado un sistema completo de autenticación de usuarios y gestión de municipios favoritos para el proyecto DatosJuntaCyL.

---

## Características Implementadas

### 1. Sistema de Autenticación

#### Registro de Usuarios
- **Ruta:** `/registro`
- **Vista:** `resources/views/auth/registro.blade.php`
- **Campos requeridos:**
  - Nombre completo
  - Email (único)
  - Contraseña (mínimo 6 caracteres)
  - Confirmación de contraseña

#### Inicio de Sesión
- **Ruta:** `/login`
- **Vista:** `resources/views/auth/login.blade.php`
- **Características:**
  - Opción "Recordarme"
  - Validación de credenciales
  - Redirección automática después del login

#### Cierre de Sesión
- **Ruta:** `POST /logout`
- Invalidación segura de sesión

---

### 2. Perfil de Usuario

#### Vista de Perfil
- **Ruta:** `/perfil`
- **Vista:** `resources/views/perfil/index.blade.php`
- **Funcionalidades:**
  - Actualizar información personal (nombre, email)
  - Cambiar contraseña
  - Ver información de cuenta (rol, fecha de registro)

#### Municipios Favoritos
- **Ruta:** `/perfil/favoritos`
- **Vista:** `resources/views/perfil/favoritos.blade.php`
- **Características:**
  - Lista de municipios favoritos con:
    - Nombre del municipio
    - Provincia
    - Población
    - Enlace a ver detalles
    - Botón para eliminar de favoritos
  - Contador total de favoritos
  - Mensaje cuando no hay favoritos con enlace a explorar municipios

---

### 3. Barra de Navegación Actualizada

La barra de navegación ahora muestra diferentes opciones según el estado de autenticación:

#### Usuario NO autenticado:
- Botón "Iniciar Sesión"
- Botón "Registrarse"
- Botón de cambio de tema

#### Usuario autenticado:
- Menú desplegable con el nombre del usuario
- Opciones del menú:
  - ⚙️ Mi Perfil
  - ⭐ Mis Favoritos
  - 🚪 Cerrar Sesión
- Botón de cambio de tema

---

### 4. API de Favoritos

Endpoints protegidos para gestionar favoritos (requieren autenticación):

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/perfil/favoritos/agregar` | POST | Añade un municipio a favoritos |
| `/api/perfil/favoritos/eliminar` | POST | Elimina un municipio de favoritos |
| `/api/perfil/favoritos/{id}/es-favorito` | GET | Verifica si un municipio es favorito |
| `/api/perfil/favoritos/lista` | GET | Obtiene la lista de IDs de favoritos |

---

## Archivos Creados/Modificados

### Controladores
- ✅ `app/Http/Controllers/AuthController.php` - Gestión de autenticación
- ✅ `app/Http/Controllers/PerfilController.php` - Gestión de perfil y favoritos

### Vistas
- ✅ `resources/views/auth/login.blade.php` - Formulario de login
- ✅ `resources/views/auth/registro.blade.php` - Formulario de registro
- ✅ `resources/views/perfil/index.blade.php` - Página de perfil
- ✅ `resources/views/perfil/favoritos.blade.php` - Lista de favoritos

### Rutas
- ✅ `routes/web.php` - Rutas de autenticación y perfil añadidas

### Configuración
- ✅ `config/auth.php` - Ajustado para usar el modelo `Usuario`

### Assets
- ✅ `resources/css/tema.css` - Estilos para menú de usuario y favoritos
- ✅ `resources/js/tema.js` - Lógica del menú desplegable
- ✅ `resources/js/favoritos.js` - Sistema de gestión de favoritos
- ✅ `resources/js/app.js` - Importación de favoritos.js

### Componentes
- ✅ `resources/views/componentes/barra-navegacion.blade.php` - Actualizada con auth
- ✅ `resources/views/diseños/panel.blade.php` - Añadido meta CSRF y mensajes flash

---

## Usuarios de Prueba

Ya existen usuarios de prueba creados por el seeder:

```
Email: admin@datosjcyl.es
Password: admin123
Rol: admin

Email: usuario1@ejemplo.com
Password: password123
Rol: usuario

Email: usuario2@ejemplo.com
Password: password123
Rol: usuario

Email: usuario3@ejemplo.com
Password: password123
Rol: usuario
```

---

## Cómo Usar

### 1. Ejecutar Migraciones y Seeders
```bash
php artisan migrate --seed
```

### 2. Compilar Assets
```bash
npm run dev
# o para producción:
npm run build
```

### 3. Iniciar Servidor
```bash
php artisan serve
```

### 4. Probar el Sistema

1. **Registro:**
   - Ir a `/registro`
   - Crear una nueva cuenta
   - Serás redirigido al panel principal

2. **Login:**
   - Ir a `/login`
   - Usar credenciales de prueba
   - Acceder al sistema

3. **Perfil:**
   - Hacer clic en tu nombre en la barra de navegación
   - Seleccionar "Mi Perfil"
   - Actualizar información o cambiar contraseña

4. **Favoritos:**
   - Navegar a la página de municipios
   - Añadir municipios a favoritos (funcionalidad se puede implementar en vistas de municipios)
   - Ver favoritos en "Mis Favoritos"

---

## Funcionalidades Adicionales Sugeridas

Para integrar completamente el sistema de favoritos, añade lo siguiente a las vistas de municipios:

### En la vista de detalle de municipio:

```blade
@auth
<button
    data-favorito-municipio="{{ $municipio->id }}"
    class="boton-favorito"
>
    ☆ Añadir a favoritos
</button>
@else
<a href="{{ route('login') }}" class="btn btn-secondary">
    Inicia sesión para añadir a favoritos
</a>
@endauth
```

El JavaScript (`favoritos.js`) se encargará automáticamente de:
- Cargar el estado actual de favoritos
- Actualizar el botón según el estado
- Manejar clicks para añadir/eliminar
- Mostrar notificaciones

---

## Seguridad

✅ Contraseñas hasheadas con `bcrypt`
✅ Protección CSRF en todos los formularios
✅ Middleware de autenticación en rutas protegidas
✅ Validación de datos en servidor y cliente
✅ Sesiones seguras con regeneración de tokens
✅ Unique constraint en favoritos (usuario_id, municipio_id)

---

## Próximos Pasos Recomendados

1. ✨ Añadir botones de favorito en las vistas de municipios
2. ✨ Implementar recuperación de contraseña
3. ✨ Añadir perfil de administrador con panel de gestión
4. ✨ Implementar verificación de email
5. ✨ Añadir estadísticas de favoritos al dashboard
6. ✨ Crear sistema de notificaciones para municipios favoritos

---

## Notas Técnicas

- El sistema usa sesiones de Laravel (session-based authentication)
- Los favoritos se almacenan en tabla `favoritos` con relación muchos a muchos
- El menú desplegable se cierra al hacer click fuera o presionar Escape
- Las notificaciones de favoritos se muestran durante 3 segundos
- Todos los estilos respetan el tema oscuro/claro

---

## Soporte

Para cualquier problema o pregunta, revisa:
- Logs de Laravel: `storage/logs/laravel.log`
- Consola del navegador para errores JavaScript
- Network tab para problemas con APIs

**¡Sistema de autenticación completamente funcional!** 🎉
