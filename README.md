# Demografía CyL - Proyecto DAW

Aplicación web Laravel que consume datos del Movimiento Natural de la Población (MNP) de Castilla y León, los almacena en base de datos y los presenta con gráficos interactivos y filtros asíncronos.

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- MySQL 8.0+ o MariaDB 10.3+
- Git

## 🚀 Instalación en Nuevo Ordenador

Sigue estos pasos para configurar el proyecto en un nuevo entorno:

### 1. Clonar el Repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd DatosJuntaCyL
```

### 2. Instalar Dependencias PHP

```bash
composer install
```

### 3. Instalar Dependencias Node

```bash
npm install
```

### 4. Configurar Variables de Entorno

```bash
# Windows (CMD)
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

**Editar el archivo `.env`** y configurar:

```env
APP_NAME="Demografía CyL"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=datosjuntacyl
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 5. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 6. Crear Base de Datos

Crear la base de datos en MySQL/MariaDB:

```sql
CREATE DATABASE datosjuntacyl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Ejecutar Migraciones

```bash
php artisan migrate
```

Esto creará las tablas:
- `provincias`
- `municipios`
- `datos_mnp`
- `usuarios`
- `sessions`
- `cache`
- `jobs`

### 8. Poblar Base de Datos (Seeders)

```bash
php artisan db:seed
```

Esto cargará:
- ✅ 9 provincias de Castilla y León
- ✅ ~2,249 municipios

### 9. Importar Datos MNP desde la API

**⚠️ IMPORTANTE:** La importación debe hacerse **año por año** debido a limitaciones de la API.

```bash
# Importar todos los datos (2020-2023) año por año
for year in 2020 2021 2022 2023; do php artisan mnp:import --ano-inicio=$year --ano-fin=$year; done
```

**Tiempo estimado:** 15-20 minutos para importar todos los datos.

**Resultado esperado:**
- ~3,976 registros de nacimientos
- ~6,763 registros de defunciones
- ~2,723 registros de matrimonios
- **Total: ~13,462 registros**

### 10. Importar Población de Municipios

Actualiza la población de todos los municipios desde la API de datos abiertos de la Junta de Castilla y León:

```bash
php artisan poblacion:importar-api
```

**Características:**
- 🌐 Conecta con la API oficial de OpenDataSoft de la JCyL
- 📊 Importa población de ~2,248 municipios
- ⚡ Procesamiento en lotes con barra de progreso
- 🔄 Actualiza automáticamente el campo `poblacion` en la tabla `municipios`

**Tiempo estimado:** 1-2 minutos

**API utilizada:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/records
```

**Comandos alternativos:**

```bash
# Actualizar población de un municipio específico
php artisan poblacion:actualizar 47186 295000

# Usar el seeder para municipios principales (solo capitales)
php artisan db:seed --class=PoblacionMunicipiosSeeder
```

### 11. Compilar Assets Frontend

```bash
# Para producción (assets compilados y optimizados)
npm run build

# Para desarrollo (servidor con hot-reload)
npm run dev
```

### 12. Iniciar Servidor

```bash
php artisan serve
```

La aplicación estará disponible en: **http://localhost:8000**

## 🔍 Verificación de Datos

Para verificar que los datos se importaron correctamente:

```bash
php artisan tinker --execute="echo 'Total registros: ' . \App\Models\DatoMnp::count();"
```

**Resultados esperados:**
- Nacimientos: 3,976 registros (100% con valores reales)
- Defunciones: 6,763 registros (100% con valores reales)
- Matrimonios: 2,723 registros (100% con valores reales)

## ⚠️ Problemas Comunes

### Error: "Vite manifest not found"

**Solución:**
```bash
npm install
npm run build
```

### Importación devuelve "No se obtuvieron datos"

**Solución:**
Importar año por año en lugar de múltiples años simultáneamente:
```bash
for year in 2020 2021 2022 2023; do php artisan mnp:import --ano-inicio=$year --ano-fin=$year; done
```

### Datos importados con valor 0

**Solución:**
Re-importar los datos año por año:
```bash
for year in 2020 2021 2022 2023; do php artisan mnp:import --tipo=defunciones --ano-inicio=$year --ano-fin=$year; done
```

## 📊 Stack Tecnológico

**Backend:** PHP 8.2+, Laravel 11, MariaDB/MySQL, PDO  
**Frontend:** JavaScript ES6, Chart.js, Tailwind CSS 4.0, Fetch API  
**Herramientas:** Vite 7.0, Composer, npm

## 📚 Documentación Adicional

- [CLAUDE.md](CLAUDE.md) - Instrucciones para Claude Code
- [DATASET-LINKS.md](DATASET-LINKS.md) - Enlaces a APIs y datasets
- [TECHNICAL-SPECS.md](TECHNICAL-SPECS.md) - Especificaciones técnicas

## 📅 Entrega

- **Fecha entrega:** 5 febrero 2026
- **Fecha defensa:** 6 febrero 2026
