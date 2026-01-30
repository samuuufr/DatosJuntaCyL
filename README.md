# Demografía CyL - Proyecto DAW

> **🚨 ATENCIÓN:** Después de clonar este proyecto, **DEBES ejecutar `npm install && npm run build`** antes de iniciar el servidor. Sin este paso, la aplicación se verá sin estilos CSS.

Aplicación web Laravel que consume datos del Movimiento Natural de la Población (MNP) de Castilla y León, los almacena en base de datos y los presenta con gráficos interactivos y filtros asíncronos.

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- MySQL 8.0+ o MariaDB 10.3+
- Git

## 🚀 Instalación Paso a Paso

### 1. Clonar el Repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd DatosJuntaCyL
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP
composer install

# Dependencias Node
npm install
```

### 3. Configurar Entorno

```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

**Editar `.env`** con tus credenciales de base de datos:

```env
DB_DATABASE=datosjuntacyl
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 5. Crear Base de Datos

```sql
CREATE DATABASE datosjuntacyl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Crear Tablas

```bash
php artisan migrate
```

### 7. Importar Provincias

```bash
php artisan db:seed --class=ProvinciaSeeder
```

**Resultado:** 9 provincias de Castilla y León

### 8. Importar TODOS los Municipios desde API

```bash
php artisan municipios:import-jcyl
```

**Resultado:** ~2,249 municipios | **Tiempo:** 1-2 minutos

> **ℹ️ Nota:** El `MunicipioSeeder` está comentado en `DatabaseSeeder.php` porque solo carga 27 municipios principales. Usamos el comando anterior para importar TODOS los municipios desde la API oficial de la JCyL.

### 9. Importar Usuarios de Prueba

```bash
php artisan db:seed --class=UsuarioSeeder
```

### 10. Importar Datos MNP (2020-2023)

**⚠️ IMPORTANTE:** Ejecutar año por año debido a limitaciones de la API.

**Windows (CMD):**
```cmd
php artisan mnp:import --ano-inicio=2020 --ano-fin=2020
php artisan mnp:import --ano-inicio=2021 --ano-fin=2021
php artisan mnp:import --ano-inicio=2022 --ano-fin=2022
php artisan mnp:import --ano-inicio=2023 --ano-fin=2023
```

**Windows (PowerShell):**
```powershell
2020..2023 | ForEach-Object { php artisan mnp:import --ano-inicio=$_ --ano-fin=$_ }
```

**Linux/Mac:**
```bash
for year in 2020 2021 2022 2023; do php artisan mnp:import --ano-inicio=$year --ano-fin=$year; done
```

**Resultado esperado:**
- ~3,976 registros de nacimientos
- ~6,763 registros de defunciones
- ~2,723 registros de matrimonios
- **Total: ~13,462 registros**
- **Tiempo:** 15-20 minutos

### 11. Importar Población de Municipios (Opcional)

```bash
php artisan poblacion:importar-api
```

**Resultado:** Población actualizada de ~2,248 municipios | **Tiempo:** 1-2 minutos

### 12. Compilar Assets Frontend ⚠️ **CRÍTICO**

```bash
npm run build
```

**⚠️ IMPORTANTE:** Sin este paso, la aplicación se verá sin estilos CSS.

### 13. Iniciar Servidor

```bash
php artisan serve
```

**Aplicación disponible en:** http://localhost:8000

## 🔍 Verificar Importación

```bash
# Ver totales
php artisan tinker --execute="echo 'Provincias: ' . \App\Models\Provincia::count() . PHP_EOL . 'Municipios: ' . \App\Models\Municipio::count() . PHP_EOL . 'Datos MNP: ' . \App\Models\DatoMnp::count();"
```

**Resultados esperados:**
- Provincias: 9
- Municipios: ~2,249
- Datos MNP: ~13,462

## ⚠️ Problemas Comunes

### La aplicación se ve sin estilos CSS

**Solución:**
```bash
npm install
npm run build
```

### Error: "Vite manifest not found"

**Solución:**
```bash
npm run build
```

### Importación devuelve "No se obtuvieron datos"

**Solución:** Importar año por año (ver paso 10)

## 📊 Stack Tecnológico

**Backend:** PHP 8.2+, Laravel 11, MariaDB/MySQL
**Frontend:** JavaScript ES6, Chart.js, Tailwind CSS 4.0
**Build:** Vite 7.0, npm

## 📚 Documentación Adicional

- [CLAUDE.md](CLAUDE.md) - Instrucciones para desarrollo
- [DATASET-LINKS.md](DATASET-LINKS.md) - APIs y datasets utilizados
- [TECHNICAL-SPECS.md](TECHNICAL-SPECS.md) - Especificaciones técnicas detalladas

## 🎯 Comandos Útiles

```bash
# Desarrollo con hot-reload
npm run dev

# Limpiar caché de Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Resetear base de datos completa
php artisan migrate:fresh

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

## 📅 Proyecto

- **Entrega:** 5 febrero 2026
- **Defensa:** 6 febrero 2026
