# Demografía CyL - Proyecto Intermodular DAW

## 📌 Estado Actual del Proyecto

**Fase:** Configuración inicial y obtención de datos  
**Fecha inicio:** 26 enero 2026  
**Fecha entrega:** 5 febrero 2026  
**Fecha presentación:** 6 febrero 2026

---

## 🎯 Objetivo del Proyecto

Desarrollar una **aplicación web completa** que acceda a los datos abiertos del **Movimiento Natural de la Población de Castilla y León** (nacimientos, defunciones, matrimonios), los integre en una base de datos y los presente de manera **interactiva, asíncrona y visualmente atractiva**.

---

## 📚 Documentación del Proyecto

### Archivos Clave (LÉELOS ANTES DE EMPEZAR)

1. **DATASET-LINKS.md** → Todos los enlaces a APIs, datasets y ejemplos de código
2. **TECHNICAL-SPECS.md** → Especificaciones técnicas completas y arquitectura
3. **docs/manual-mnp.pdf** → Manual oficial de la API del Movimiento Natural de Población

### Documentación Adicional

- **docs/datasets-complementarios.md** → Análisis de datasets adicionales útiles
- **docs/rubrica-evaluacion.md** → Criterios de calificación detallados

---

## 🏗️ Estructura del Proyecto

```
proyecto-demografia-cyl/
│
├── CLAUDE.md                          ← Estás aquí (instrucciones principales)
├── DATASET-LINKS.md                   ← Enlaces y APIs
├── TECHNICAL-SPECS.md                 ← Especificaciones técnicas
├── README.md                          ← Documentación para usuarios
│
├── docs/                              ← Documentación
│   ├── manual-mnp.pdf
│   ├── datasets-complementarios.md
│   ├── rubrica-evaluacion.md
│   ├── Memoria_Proyecto.docx         ← Memoria (entregar)
│   ├── Presentacion.pptx             ← Presentación (entregar)
│   └── demo.mp4                      ← Video demostración (entregar)
│
├── data/                              ← Datos descargados
│   ├── raw/                           ← CSVs originales
│   │   ├── mnp_nacimientos_2005-2023.csv
│   │   ├── mnp_defunciones_2005-2023.csv
│   │   ├── poblacion_provincias.csv
│   │   ├── poblacion_edades.csv
│   │   └── municipios.csv
│   └── processed/                     ← Datos procesados
│
├── database/
│   ├── migrations/                    ← Migraciones Laravel
│   │   ├── create_nacimientos_table.php
│   │   ├── create_defunciones_table.php
│   │   ├── create_poblacion_table.php
│   │   └── create_indicadores_table.php
│   ├── seeders/                       ← Seeders
│   └── schema.sql                     ← Esquema SQL de referencia
│
├── app/                               ← Código Laravel
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── DataController.php
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Nacimiento.php
│   │   ├── Defuncion.php
│   │   ├── Poblacion.php
│   │   └── Indicador.php
│   └── Services/
│       ├── MnpApiService.php         ← Consumo API MNP
│       └── IndicadoresService.php    ← Cálculo de indicadores
│
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── provincial.blade.php
│   │   └── auth/
│   ├── css/
│   └── js/
│       ├── app.js
│       ├── charts.js                 ← Chart.js
│       └── filters.js                ← Sistema de filtros
│
├── public/
│   ├── css/
│   ├── js/
│   └── index.php
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── scripts/                           ← Scripts de utilidad
│   ├── import-mnp-data.php           ← Importar MNP
│   ├── import-poblacion.php          ← Importar población
│   └── calculate-indicators.php      ← Calcular indicadores
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── .env.example
├── .gitignore
├── composer.json
├── package.json
└── docker-compose.yml                 ← Docker (opcional)
```

---

## 🛠️ Stack Tecnológico

### Backend
- **PHP 8.2+**
- **Laravel 11** (framework principal)
- **MariaDB 10.6+** / MySQL 8.0+
- **PDO** para acceso a datos
- **cURL** para consumo de APIs externas

### Frontend
- **HTML5 + CSS3**
- **JavaScript ES6+** (Vanilla, sin jQuery)
- **Chart.js** para gráficos interactivos
- **Tailwind CSS** para diseño responsivo
- **Fetch API** para comunicación asíncrona

### Infraestructura
- **Git + GitHub** (control de versiones)
- **Apache/Nginx** (servidor web)
- **Docker** (opcional pero valorado)
- **Composer** (dependencias PHP)
- **NPM** (dependencias JS)

---

## 🔗 Enlaces Rápidos a Datasets

**Ver `DATASET-LINKS.md` para lista completa de enlaces y ejemplos.**

### Descargas Directas (CSV)

**Población:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/exports/csv
```

**Municipios:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv
```

### API Movimiento Natural de la Población

**Base URL:**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&
```

**Parámetros principales:**
- `SL=COD_FAMILIA_VARIABLES:10` → Nacimientos
- `SL=COD_FAMILIA_VARIABLES:30` → Defunciones
- `SL=COD_PROVINCIA:[codigo]` → Filtrar por provincia
- `SL=ANNO:[año]` → Filtrar por año
- `AC=ANNO` → Agrupar por año en columnas

Ver `docs/manual-mnp.pdf` para detalles completos.

---

## 📊 Estructura de Base de Datos

### Tablas Principales

**1. nacimientos**
```sql
id, anno, cod_provincia, nom_provincia, cod_municipio, nom_municipio,
sexo, edad_madre, multiplicidad, valor, familia_variable,
created_at, updated_at
```

**2. defunciones**
```sql
id, anno, cod_provincia, nom_provincia, cod_municipio, nom_municipio,
sexo, edad, estado_civil, valor, familia_variable,
created_at, updated_at
```

**3. poblacion**
```sql
id, anno, cod_provincia, nom_provincia, edad, sexo, valor,
created_at, updated_at
```

**4. indicadores** (calculados)
```sql
id, anno, cod_provincia, nom_provincia, poblacion_total,
total_nacimientos, total_defunciones, tasa_natalidad, tasa_mortalidad,
crecimiento_vegetativo, indice_envejecimiento, esperanza_vida,
created_at, updated_at
```

**5. municipios** (catálogo)
```sql
id, cod_municipio, nom_municipio, cod_provincia, nom_provincia,
superficie, latitud, longitud, created_at, updated_at
```

**6. users**
```sql
id, name, email, password, role, created_at, updated_at
```

---

## 🎨 Funcionalidades Principales

### 1. Dashboard Principal
- Mapa interactivo de Castilla y León
- Selector de provincia y año
- KPIs destacados:
  - Población total CyL
  - Total nacimientos año actual
  - Total defunciones año actual
  - Crecimiento vegetativo
- Gráfico de evolución temporal (2005-2023)

### 2. Análisis Provincial
- Selector de provincia
- Pirámide de población interactiva
- Tabla de indicadores demográficos
- Gráficos específicos:
  - Nacimientos por edad de madre
  - Defunciones por edad
  - Comparativa con otras provincias

### 3. Análisis Municipal
- Buscador de municipios
- Ficha detallada de cada municipio
- Evolución temporal del municipio

### 4. Sistema de Usuarios
- Registro de usuarios
- Login/Logout
- Sesiones seguras ($_SESSION)
- Contraseñas encriptadas (password_hash)
- Opcional: CAPTCHA

### 5. Exportación de Datos
- Exportar tabla actual a CSV
- Generar informe PDF (opcional)

### 6. Funcionalidades Innovadoras (PUNTOS EXTRA)
- Predictor demográfico con ML/IA
- Generador de insights con Claude API
- Calculadora de indicadores personalizada
- Sistema de comparación avanzada

---

## ✅ Criterios de Evaluación

**Ver `docs/rubrica-evaluacion.md` para detalles completos.**

| Criterio | Peso | Puntos |
|----------|------|--------|
| **RA1: Asistencia con aprovechamiento** | 20% | 2.0 |
| **RA2: Contenido Técnico** | 60% | 6.0 |
| - Backend (lógica + BD) | 30% RA2 | 1.8 |
| - Frontend (cliente + UI) | 30% RA2 | 1.8 |
| - Infraestructura (Git + deploy) | 20% RA2 | 1.2 |
| - Innovación (IA + sostenibilidad) | 20% RA2 | 1.2 |
| **RA3: Memoria** | 10% | 1.0 |
| **RA4: Presentación y defensa** | 10% | 1.0 |
| **TOTAL** | 100% | **10.0** |

---

## 📋 Checklist de Implementación

### Fase 1: Setup Inicial (26-27 enero)
- [ ] Crear repositorio Git en GitHub
- [ ] Configurar estructura Laravel
- [ ] Crear `.env` con configuración BD
- [ ] Inicializar base de datos
- [ ] Crear migraciones para tablas principales

### Fase 2: Obtención de Datos (27-28 enero)
- [ ] Script de importación de datos MNP (nacimientos)
- [ ] Script de importación de datos MNP (defunciones)
- [ ] Script de importación de población por provincias
- [ ] Script de importación de población por edades
- [ ] Importar catálogo de municipios
- [ ] Verificar datos en BD (seeders para testing)

### Fase 3: Backend (28-30 enero)
- [ ] Crear modelos Eloquent (Nacimiento, Defuncion, Poblacion, etc.)
- [ ] Implementar servicio MnpApiService
- [ ] Implementar IndicadoresService (cálculo de tasas)
- [ ] Crear controladores (Dashboard, Data, Auth)
- [ ] Definir rutas web y API
- [ ] Sistema de autenticación (registro/login)

### Fase 4: Frontend (30 enero - 2 febrero)
- [ ] Layout base con Tailwind CSS
- [ ] Dashboard principal con mapa y KPIs
- [ ] Sistema de filtros (provincia, año)
- [ ] Gráficos con Chart.js (evolución temporal)
- [ ] Vista de análisis provincial
- [ ] Pirámide de población interactiva
- [ ] Tabla de indicadores
- [ ] Sistema de exportación CSV

### Fase 5: Interactividad (2-3 febrero)
- [ ] Implementar Fetch API para filtros
- [ ] Actualización asíncrona de gráficos
- [ ] Validación de formularios (client + server)
- [ ] Diseño responsivo (mobile + desktop)
- [ ] Testing en diferentes navegadores

### Fase 6: Innovación (3-4 febrero)
- [ ] Predictor demográfico (regresión lineal básica)
- [ ] Integración con Claude API (generación de insights)
- [ ] Generador de informes automáticos
- [ ] Optimizaciones de rendimiento

### Fase 7: Despliegue y Documentación (4-5 febrero)
- [ ] Despliegue en producción (opcional pero valorado)
- [ ] Configurar Docker (opcional)
- [ ] Escribir README completo
- [ ] Completar memoria (mínimo 10 páginas)
- [ ] Preparar presentación PowerPoint
- [ ] Grabar video demostración (máx. 5 min)
- [ ] Revisión final de código
- [ ] Exportar repositorio a .zip

### Fase 8: Entrega y Presentación (5-6 febrero)
- [ ] Subir repositorio .zip a Teams
- [ ] Entregar memoria impresa (doble cara, espiral)
- [ ] Preparar defensa ante tribunal
- [ ] Ensayar presentación (10 min + 5 min preguntas)

---

## 🚀 Comandos Útiles

### Desarrollo

```bash
# Instalar dependencias
composer install
npm install

# Configurar .env
cp .env.example .env
php artisan key:generate

# Migraciones
php artisan migrate
php artisan migrate:fresh --seed

# Importar datos
php scripts/import-mnp-data.php
php scripts/import-poblacion.php
php scripts/calculate-indicators.php

# Servidor desarrollo
php artisan serve
npm run dev

# Testing
php artisan test
```

### Git

```bash
# Commits atómicos
git add .
git commit -m "feat(backend): implementar servicio API MNP"

# Push a GitHub
git push origin main

# Crear ramas
git checkout -b feature/dashboard
git checkout -b feature/auth
```

---

## 💡 Decisiones Técnicas Importantes

### ¿Por qué Laravel?
- Framework robusto y maduro
- ORM Eloquent para facilitar acceso a BD
- Sistema de migraciones y seeders
- Blade para templates
- Autenticación integrada
- Comunidad grande y documentación excelente

### ¿Qué datasets usar?
**Mínimo obligatorio:**
- MNP: Nacimientos y Defunciones (2005-2023)
- Población: Total por provincias

**Recomendado:**
- Población por edades (para pirámides)
- Municipios (para filtros)
- Indicadores demográficos (síntesis)

### ¿Cómo calcular indicadores?
```javascript
// Tasa bruta de natalidad
tasa_natalidad = (nacimientos / poblacion) × 1000

// Tasa bruta de mortalidad
tasa_mortalidad = (defunciones / poblacion) × 1000

// Crecimiento vegetativo
crecimiento = nacimientos - defunciones

// Índice de envejecimiento
indice_env = (poblacion_>65 / poblacion_<15) × 100
```

### ¿Cómo estructurar la BD?
- **Normalizada** para evitar redundancia
- **Con índices** en campos de búsqueda frecuente (provincia, año)
- **Timestamps** en todas las tablas
- **Foreign keys** para integridad referencial

---

## ⚠️ Restricciones y Requisitos

### Obligatorio
- ✅ Diseño **responsivo** (mobile + desktop)
- ✅ Validación **client-side** y **server-side**
- ✅ Uso de **Fetch API** (no jQuery)
- ✅ **Git** con commits atómicos y descriptivos
- ✅ **Al menos 1 control de interfaz** para filtros
- ✅ Presentación **asíncrona** de datos (sin recargas)
- ✅ **Patrón MVC** o framework

### Valorado Positivamente
- ⭐ Framework backend (Laravel/Symfony)
- ⭐ Despliegue en producción
- ⭐ Uso de Docker
- ⭐ Integración con IA
- ⭐ Medidas de sostenibilidad/eficiencia
- ⭐ Testing automatizado

### Prohibido
- ❌ Copiar código sin atribución
- ❌ Contenido ofensivo o discriminatorio
- ❌ Uso de APIs de pago sin autorización

---

## 🆘 Recursos de Ayuda

### Documentación Oficial
- **Laravel:** https://laravel.com/docs
- **Chart.js:** https://www.chartjs.org/docs/
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Fetch API:** https://developer.mozilla.org/es/docs/Web/API/Fetch_API

### Datasets y APIs
- **Portal JCyL:** https://datosabiertos.jcyl.es
- **Portal Análisis:** https://analisis.datosabiertos.jcyl.es
- **INE:** https://www.ine.es

### Herramientas
- **URL Encoder:** http://ascii.cl/es/url-encoding.htm
- **Git Flow:** https://danielkummer.github.io/git-flow-cheatsheet/
- **JSON Viewer:** https://jsonviewer.stack.hu/

---

## 📝 Notas Importantes

1. **Prioriza funcionalidad básica** antes que extras
2. **Commits frecuentes** con mensajes descriptivos
3. **Documenta código inline** mientras programas
4. **Testea en Chrome y Firefox** mínimo
5. **Mobile-first** en el diseño CSS
6. **No dejes documentación para el final**
7. **Haz backups diarios** del proyecto
8. **Ensaya la presentación** varias veces

---

## 🎯 Objetivos de Aprendizaje

Al completar este proyecto, habrás demostrado:

✅ Capacidad de consumir y procesar APIs externas  
✅ Diseño e implementación de bases de datos relacionales  
✅ Desarrollo full-stack con separación MVC  
✅ Creación de interfaces web interactivas y responsivas  
✅ Uso de control de versiones profesional  
✅ Documentación técnica completa  
✅ Presentación y defensa de proyectos técnicos  

---

## 📞 Contacto y Soporte

**Equipo docente DAW**  
**Fechas críticas:**
- 26 enero: Inicio desarrollo
- 5 febrero: Entrega (antes de medianoche)
- 6 febrero: Presentación (10 min + 5 min preguntas)

---

**¡Éxito con el proyecto! 🚀**

*Última actualización: 27 enero 2026*
