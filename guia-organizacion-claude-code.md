# Guía de Organización de Archivos para Claude Code
## Cómo Estructurar tu Proyecto DAW

---

## 🎯 RESPUESTA RÁPIDA

**SÍ, coloca los archivos .md en la raíz del proyecto junto a `CLAUDE.md`**

Claude Code lee automáticamente:
1. `CLAUDE.md` (instrucciones principales)
2. Otros archivos `.md` en la raíz si los referencias
3. Archivos en carpetas específicas si se lo indicas

---

## 📁 ESTRUCTURA DE PROYECTO RECOMENDADA

```
proyecto-demografia-cyl/
│
├── CLAUDE.md                          ← Archivo PRINCIPAL (instrucciones generales)
├── DATASET-LINKS.md                   ← Enlaces a APIs y datasets
├── TECHNICAL-SPECS.md                 ← Especificaciones técnicas detalladas
├── README.md                          ← Documentación para humanos
│
├── docs/                              ← Documentación del proyecto
│   ├── especificaciones-completas.md  ← Tu primer documento
│   ├── datasets-demograficos.md       ← Dataset complementarios
│   └── rubrica-evaluacion.md          ← Criterios de calificación
│
├── data/                              ← Datos descargados
│   ├── raw/                           ← Datos originales (CSV, JSON)
│   │   ├── mnp_nacimientos.csv
│   │   ├── poblacion_provincias.csv
│   │   └── municipios.csv
│   └── processed/                     ← Datos procesados
│
├── database/
│   ├── migrations/                    ← Migraciones de BD
│   ├── seeders/                       ← Seeds con datos
│   └── schema.sql                     ← Esquema de BD
│
├── src/ o app/                        ← Código fuente
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── services/
│
├── public/                            ← Assets públicos
│   ├── css/
│   ├── js/
│   └── index.php
│
├── scripts/                           ← Scripts de utilidad
│   ├── import-data.php               ← Importar datos de APIs
│   └── calculate-indicators.php      ← Calcular indicadores
│
├── .env.example
├── .gitignore
├── composer.json                      ← Si usas Laravel/PHP
└── package.json                       ← Si usas Node.js
```

---

## 📝 OPCIÓN 1: TODO EN CLAUDE.MD (Recomendado para proyectos pequeños)

**Ventajas:**
- ✅ Todo en un solo lugar
- ✅ Claude Code lo lee automáticamente
- ✅ Simple y directo

**Cómo hacerlo:**

Crea un `CLAUDE.md` que incluya TODO:

```markdown
# Proyecto Demografía Castilla y León

## Objetivo
[Tu descripción del proyecto]

## Especificaciones Técnicas
[Copia aquí el contenido de especificaciones-completas.md]

## Enlaces a Datasets
[Copia aquí el contenido de enlaces-apis-datasets-jcyl.md]

## Datasets Complementarios
[Copia aquí el contenido de datasets-demograficos.md]

## Criterios de Evaluación
[Copia la rúbrica]

## Instrucciones de Implementación
[Paso a paso]
```

---

## 📚 OPCIÓN 2: ARCHIVOS SEPARADOS (Recomendado para tu proyecto)

**Ventajas:**
- ✅ Organizado y modular
- ✅ Fácil de mantener
- ✅ Puedes actualizar partes sin tocar todo
- ✅ Mejor para proyectos grandes

**Cómo hacerlo:**

### Paso 1: Crea el archivo principal `CLAUDE.md`

```markdown
# Proyecto Intermodular DAW - Demografía CyL

## 🎯 Descripción General

Aplicación web para visualizar y analizar datos demográficos de Castilla y León,
enfocada en nacimientos, defunciones y movimiento natural de la población.

## 📋 Documentación Importante

**LEE ESTOS ARCHIVOS ANTES DE EMPEZAR:**

1. **DATASET-LINKS.md** - Enlaces directos a todas las APIs y datasets
2. **TECHNICAL-SPECS.md** - Especificaciones técnicas completas del proyecto
3. **docs/rubrica-evaluacion.md** - Criterios de calificación (importante para priorizar)

## 🚀 Fase de Implementación Actual

**Fase 1: Setup y Obtención de Datos**

Tareas inmediatas:
- [ ] Configurar estructura de proyecto Laravel
- [ ] Crear base de datos con migraciones
- [ ] Implementar script de importación de datos desde APIs
- [ ] Probar descarga de datasets principales

## 🔗 Enlaces Rápidos a Datasets Principales

Ver archivo completo: `DATASET-LINKS.md`

**Descargas directas (CSV):**
- Población por provincias: https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv
- Población por edades: https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/exports/csv
- Municipios: https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv

**API Movimiento Natural de la Población:**
- Manual completo en: docs/Manual_de_consultas_MovimientoNaturalPoblacion.pdf
- Base: http://www.jcyl.es/sie/sas/broker?[parámetros]

## 🛠️ Stack Tecnológico

**Backend:**
- PHP 8.2+
- Laravel 11 (framework recomendado)
- MariaDB 10.6+
- PDO para acceso a datos

**Frontend:**
- HTML5 + CSS3
- JavaScript ES6+ (Vanilla)
- Chart.js para gráficos
- Tailwind CSS para diseño
- Fetch API para AJAX

**Infraestructura:**
- Git + GitHub
- Docker (opcional pero valorado)
- Apache/Nginx

## 📊 Estructura de Base de Datos

Ver `database/schema.sql` para el esquema completo.

Tablas principales:
- `nacimientos` - Datos de nacimientos del MNP
- `defunciones` - Datos de defunciones del MNP
- `poblacion` - Datos del Padrón por año/provincia/edad
- `indicadores` - Indicadores calculados (tasas, índices)
- `municipios` - Catálogo de municipios

## 🎨 Funcionalidades Clave

1. **Dashboard Principal**
   - Mapa interactivo de CyL
   - KPIs: población total, nacimientos, defunciones
   - Gráfico de evolución temporal

2. **Análisis Provincial**
   - Selector de provincia
   - Pirámide de población
   - Indicadores demográficos
   - Gráficos comparativos

3. **Sistema de Usuarios**
   - Registro/Login
   - Sesiones seguras
   - Exportación de datos

4. **Innovación (para puntos extra)**
   - Predictor con ML/IA
   - Generador de informes con Claude API
   - Visualizaciones avanzadas

## ⚠️ Criterios de Evaluación (ver rubrica-evaluacion.md)

- Backend (18%): BD + API + Lógica
- Frontend (18%): Interfaz + AJAX + Validación
- Infraestructura (12%): Git + Deploy
- Innovación (12%): IA + Sostenibilidad
- Asistencia (20%), Memoria (10%), Presentación (10%)

## 📅 Timeline

- 26 enero - 5 febrero: Desarrollo
- 5 febrero: Entrega
- 6 febrero: Presentación

## 💡 Notas Importantes

- Usa commits atómicos y descriptivos
- Documenta código inline
- Prioriza funcionalidad básica antes que extras
- Testea en diferentes navegadores
- Diseño mobile-first

## 🆘 Si Tienes Dudas

1. Revisa TECHNICAL-SPECS.md (tiene ejemplos de código)
2. Revisa DATASET-LINKS.md (tiene ejemplos de APIs)
3. Consulta la documentación oficial de Laravel
4. Los datasets están en `data/raw/` una vez descargados
```

### Paso 2: Crea `DATASET-LINKS.md`

```markdown
# Enlaces a Datasets y APIs

[COPIA AQUÍ TODO EL CONTENIDO DEL ARCHIVO "enlaces-apis-datasets-jcyl.md"]
```

### Paso 3: Crea `TECHNICAL-SPECS.md`

```markdown
# Especificaciones Técnicas Completas

[COPIA AQUÍ TODO EL CONTENIDO DEL ARCHIVO "proyecto-daw-especificaciones.md"]
```

### Paso 4: Organiza en carpeta docs/

```
docs/
├── datasets-complementarios.md    ← Análisis de datasets adicionales
├── rubrica-evaluacion.md         ← Criterios de calificación
└── manual-mnp.pdf                ← Manual de la API (ya lo tienes)
```

---

## 🤖 CÓMO CLAUDE CODE LEE LOS ARCHIVOS

### Lectura Automática
Claude Code **automáticamente** lee:
1. `CLAUDE.md` al iniciar el proyecto
2. `README.md` si existe

### Lectura por Referencia
Si en `CLAUDE.md` dices:
```markdown
Ver detalles en DATASET-LINKS.md
```

Claude Code **NO** lo lee automáticamente, pero tú puedes:
1. Decirle: "Lee el archivo DATASET-LINKS.md"
2. O usar `@DATASET-LINKS.md` en tus mensajes
3. O incluir secciones relevantes en CLAUDE.md

### Mejor Práctica

**En CLAUDE.md incluye:**
- ✅ Contexto general
- ✅ Objetivos y tareas actuales
- ✅ Enlaces rápidos importantes
- ✅ **Referencias** a archivos detallados

**En archivos separados:**
- ✅ Especificaciones técnicas completas
- ✅ Listas exhaustivas de enlaces
- ✅ Ejemplos de código extensos
- ✅ Documentación de referencia

---

## 💬 CÓMO INTERACTUAR CON CLAUDE CODE

### Opción A: Mensajes Iniciales

```
Claude, este es un proyecto de desarrollo web sobre demografía de CyL.

Lee estos archivos en orden:
1. CLAUDE.md (contexto general)
2. DATASET-LINKS.md (APIs y datasets)
3. TECHNICAL-SPECS.md (especificaciones técnicas)

Después, ayúdame a:
1. Configurar la estructura Laravel
2. Crear el esquema de base de datos
3. Implementar el script de importación de datos
```

### Opción B: Usar @ para Referenciar

```
@CLAUDE.md
@DATASET-LINKS.md

Claude, basándote en estos archivos, crea el script PHP para 
importar datos del MNP desde la API.
```

### Opción C: Comandos Específicos

```
Claude, lee DATASET-LINKS.md sección 5 y dame un ejemplo 
de cómo usar la API Opendatasoft para obtener población de Salamanca
```

---

## ✅ CHECKLIST DE SETUP

### Antes de empezar a programar:

- [ ] Crea `CLAUDE.md` con contexto general
- [ ] Crea `DATASET-LINKS.md` con todos los enlaces
- [ ] Crea `TECHNICAL-SPECS.md` con especificaciones
- [ ] Crea carpeta `docs/` con documentación adicional
- [ ] Crea `.gitignore` adecuado
- [ ] Inicializa repositorio Git
- [ ] Crea estructura de carpetas base

### Al empezar cada sesión con Claude Code:

- [ ] Revisa CLAUDE.md y actualiza "Fase actual"
- [ ] Indica qué archivos debe leer Claude
- [ ] Especifica la tarea concreta del día

---

## 🎓 EJEMPLO COMPLETO DE CLAUDE.MD OPTIMIZADO

Aquí un ejemplo real de cómo debería verse tu `CLAUDE.md`:

```markdown
# Demografía CyL - Proyecto Intermodular DAW

## 📌 Estado Actual del Proyecto

**Fase:** Configuración inicial y obtención de datos
**Fecha:** 27 enero 2026
**Próxima entrega:** 5 febrero 2026

## 🎯 Tarea Inmediata

Implementar el sistema de importación de datos desde las APIs de la Junta de CyL.

**Archivos clave a consultar:**
- `DATASET-LINKS.md` - Sección 1 y 2 (MNP y Población)
- `TECHNICAL-SPECS.md` - Sección 6 y 7 (Arquitectura y Funcionalidades)

## 📚 Documentación del Proyecto

### Archivos Principales
- **DATASET-LINKS.md** - Todos los enlaces a APIs, datasets y ejemplos de uso
- **TECHNICAL-SPECS.md** - Especificaciones técnicas completas del proyecto
- **docs/rubrica-evaluacion.md** - Criterios de calificación (60% técnico)
- **docs/manual-mnp.pdf** - Manual de la API del Movimiento Natural

### Estructura del Proyecto
```
├── CLAUDE.md                 ← Estás aquí
├── DATASET-LINKS.md          ← URLs y APIs
├── TECHNICAL-SPECS.md        ← Especificaciones
├── database/
│   ├── migrations/
│   └── schema.sql
├── src/
│   ├── controllers/
│   ├── models/
│   └── services/
│       └── MnpApiService.php  ← Servicio API MNP
└── scripts/
    └── import-data.php       ← Script importación
```

## 🔗 Enlaces Críticos (Referencia Rápida)

**API Principal - Movimiento Natural:**
```
Base: http://www.jcyl.es/sie/sas/broker?...
Docs: Ver DATASET-LINKS.md sección 1
```

**Datasets Complementarios:**
```
Población: https://analisis.datosabiertos.jcyl.es/api/.../poblacion-total-por-provincias-y-sexo/exports/csv
Ver DATASET-LINKS.md sección 2 para más
```

## 🛠️ Stack Tecnológico

- Laravel 11 + PHP 8.2
- MariaDB 10.6
- Chart.js + Tailwind CSS
- Git/GitHub

## ✅ Tareas Completadas

- [x] Estructura de carpetas creada
- [x] Repositorio Git inicializado
- [ ] Base de datos configurada
- [ ] Script de importación MNP
- [ ] Script de importación Población
- [ ] Modelos Eloquent creados

## 📋 Próximos Pasos

1. Crear migraciones para tablas: nacimientos, defunciones, poblacion
2. Implementar servicio API para consumir MNP
3. Script de importación que:
   - Descarga CSV de nacimientos (2005-2023)
   - Descarga CSV de defunciones (2005-2023)
   - Parsea y guarda en BD
4. Crear seeders con datos descargados

## 💡 Decisiones Técnicas

- **¿Por qué Laravel?** Framework robusto, ORM, sistema de migraciones (ver TECHNICAL-SPECS.md)
- **¿Qué datasets usar?** MNP + Población por provincias + Población por edades (ver DATASET-LINKS.md sección 7)
- **¿Cómo almacenar?** BD relacional normalizada (ver schema.sql)

## 🎨 Funcionalidades Principales

1. Dashboard con mapa de CyL
2. Gráficos de evolución temporal
3. Análisis provincial con pirámide de población
4. Sistema login/registro
5. Exportación CSV/PDF
6. **EXTRA:** Predictor con IA (Claude API)

## ⚠️ Restricciones y Requisitos

- Mobile-first responsive
- Validación client + server side
- Fetch API (no jQuery)
- Git con commits atómicos
- Sin reproducciones de código sin atribución
- Entrega: repo + memoria.pdf + presentación.pptx + demo.mp4

## 📊 Criterios de Evaluación

**Backend (30% de 60%):** BD + API + Lógica → 18% total
**Frontend (30% de 60%):** UI + AJAX + Validación → 18% total
**Infraestructura (20% de 60%):** Git + Deploy → 12% total
**Innovación (20% de 60%):** IA + Sostenibilidad → 12% total

Ver `docs/rubrica-evaluacion.md` para detalles.

## 🆘 Comandos Útiles

```bash
# Importar datos
php scripts/import-data.php

# Migraciones
php artisan migrate

# Seeders
php artisan db:seed

# Servidor desarrollo
php artisan serve
```

## 📝 Notas

- Los CSVs usan `;` como delimiter
- Caracteres especiales en URLs requieren encoding
- API MNP sin autenticación
- API Opendatasoft: max 100 records/request
- Cachear datos localmente (no descargar cada vez)
```

---

## 🎁 RESUMEN EJECUTIVO

### Para tu proyecto específico, haz esto:

**1. Crea 3 archivos en la raíz:**

```
CLAUDE.md              ← Contexto general + tareas actuales (usa el ejemplo de arriba)
DATASET-LINKS.md       ← Copia del archivo "enlaces-apis-datasets-jcyl.md"
TECHNICAL-SPECS.md     ← Copia del archivo "proyecto-daw-especificaciones.md"
```

**2. Crea carpeta docs/ con:**

```
docs/datasets-complementarios.md    ← Análisis de datasets adicionales
docs/rubrica-evaluacion.md         ← Extracto de la rúbrica
docs/manual-mnp.pdf                ← El manual que ya tienes
```

**3. Al empezar con Claude Code, di:**

```
Hola Claude, lee estos archivos:
- CLAUDE.md
- DATASET-LINKS.md
- TECHNICAL-SPECS.md

Después, ayúdame a configurar la estructura Laravel del proyecto
y crear el script de importación de datos.
```

**4. Durante el desarrollo:**

```
@CLAUDE.md actualiza la sección "Tareas Completadas"

@DATASET-LINKS.md dame el código para descargar población por edades

@TECHNICAL-SPECS.md recuérdame los requisitos del dashboard principal
```

---

## ✨ VENTAJAS DE ESTA ESTRUCTURA

✅ Claude Code tiene todo el contexto necesario
✅ Puedes actualizar fácilmente cada parte
✅ Separación clara: general vs técnico vs datos
✅ Fácil de mantener durante el desarrollo
✅ Puedes versionar todo en Git
✅ Referencias claras entre archivos
✅ Escalable si el proyecto crece

---

**¿Necesitas que te prepare los 3 archivos principales ya listos para copiar y pegar en tu proyecto?**
