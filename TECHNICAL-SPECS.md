# Especificaciones del Proyecto Intermodular DAW
## Aplicación Web con Datos Abiertos de Castilla y León - Movimiento Natural de la Población

---

## 1. CONTEXTO Y OBJETIVO GENERAL

Desarrollar una **aplicación web completa** que acceda al conjunto de datos abiertos del **Movimiento Natural de la Población de Castilla y León** (nacimientos, matrimonios, defunciones), los integre en un sistema de base de datos y los presente al usuario final de manera **interactiva, asíncrona, estructurada y amigable**.

**Dataset seleccionado:** Movimiento Natural de la Población (MNP) de la Junta de Castilla y León
- **API Base:** http://www.jcyl.es/sie/sas/broker (ver sección 5 para detalles completos)
- **Datos disponibles:** Nacimientos, matrimonios y defunciones con múltiples dimensiones de análisis

---

## 2. CRITERIOS DE EVALUACIÓN Y PESO

La calificación final se distribuye así:

| Criterio | Peso | Descripción |
|----------|------|-------------|
| **RA1: Asistencia** | 20% | Asistencia con aprovechamiento durante el desarrollo |
| **RA2: Contenido Técnico** | 60% | Calidad del desarrollo backend, frontend e infraestructura |
| **RA3: Memoria** | 10% | Documentación completa en formato Word |
| **RA4: Presentación** | 10% | Exposición oral y defensa ante tribunal |

### Desglose RA2 - Contenido Técnico (60% total):

| Componente | Peso dentro de RA2 | Peso total |
|------------|-------------------|------------|
| Backend: lógica de negocio y BD | 30% | 18% |
| Frontend: cliente e interfaz | 30% | 18% |
| Infraestructura: Git, CI/CD, deploy | 20% | 12% |
| Innovación: IA, sostenibilidad, extras | 20% | 12% |

---

## 3. STACK TECNOLÓGICO REQUERIDO

### 3.1 Backend (Modalidad Básica → Avanzada)

**BÁSICO (funcionalidad mínima):**
- PHP (vanilla o con patrón MVC básico)
- MariaDB / MySQL
- Apache
- PDO para acceso a datos
- cURL para consumo de API externa
- Sistema de sesiones ($_SESSION)
- Registro/login con:
  - Contraseñas encriptadas (password_hash)
  - Opcional pero valorado: CAPTCHA

**AVANZADO (mayor puntuación):**
- Framework PHP: **Laravel** o Symfony
- Despliegue en máquina virtual con servicios configurados
- **Docker** (contenedorización de servicios)

### 3.2 Frontend

**JavaScript:**
- Vanilla JavaScript (ES6+) obligatorio
- `fetch()` o equivalente para comunicación asíncrona
- Validación de formularios en cliente
- Frameworks/librerías opcionales pero valorados:
  - React, Next.js, Alpine.js
  - Chart.js (para gráficos)
  - Leaflet.js (para mapas si aplica)

**HTML5 y CSS:**
- Diseño **responsivo** (mínimo 2 puntos de corte: mobile y desktop)
- **Accesible y usable**
- Frameworks CSS opcionales:
  - Tailwind CSS, Bootstrap, daisyUI
- Motores de plantillas (ej. Blade si se usa Laravel)

### 3.3 Infraestructura

**Obligatorio:**
- **Git** con repositorio en **GitHub**
- Estrategia de ramificación clara (ej. Git Flow)
- Commits atómicos y descriptivos
- Configuración de entorno local

**Valorado positivamente:**
- Despliegue en producción (hosting real)
- Integración continua (CI/CD)
- Pruebas automatizadas
- Scripts de empaquetado

---

## 4. REQUISITOS FUNCIONALES OBLIGATORIOS

### 4.1 Gestión de Datos

1. **Descarga e integración:**
   - Consumir datos desde la API del MNP (ver sección 5)
   - Almacenar en base de datos relacional/no relacional
   - Posibilidad de actualización periódica

2. **Consultas y filtros:**
   - Usuario debe poder filtrar datos por:
     - Provincia (9 provincias de CyL)
     - Año (rango de años disponibles)
     - Tipo de indicador (nacimientos, matrimonios, defunciones)
     - Otros criterios según el indicador (edad, sexo, etc.)
   
3. **Presentación estructurada:**
   - Tablas dinámicas
   - Gráficos visuales (recomendado: Chart.js)
   - Exportación de datos (CSV, PDF)

### 4.2 Sistema de Usuarios

- **Registro:** Nombre, email, contraseña (validados)
- **Login/Logout:** Con sesiones seguras
- **Roles (opcional pero valorado):**
  - Usuario normal: consulta datos
  - Admin: gestión de datos/usuarios

### 4.3 Interactividad Asíncrona

- **Sin recargas de página** para:
  - Aplicar filtros
  - Actualizar gráficos
  - Cargar nuevas consultas
- Uso de AJAX/Fetch API

### 4.4 Interfaz de Usuario

**Mínimo obligatorio:**
- **Al menos 1 control de interfaz** para preferencias del usuario:
  - Selectores (provincia, año, tipo de dato)
  - Botones de acción (aplicar filtro, exportar)
  - Inputs de búsqueda
  
**Ejemplo:** 
> Usuario selecciona "Provincia: Salamanca" + "Año: 2020" + "Indicador: Nacimientos por sexo" 
> → La aplicación muestra solo esos datos sin recargar

---

## 5. API DEL MOVIMIENTO NATURAL DE LA POBLACIÓN

### 5.1 Estructura de la URI

**URI Base:**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&
```

### 5.2 Parámetros Disponibles

Formato: `[nombre]=[valor]&[nombre]=[valor]&...`

#### Parámetro `SL` (Indicadores)

Valores principales:
- `COD_FAMILIA_VARIABLES:10` → Nacimientos
- `COD_FAMILIA_VARIABLES:12` → Nacimientos por sexo
- `COD_FAMILIA_VARIABLES:14` → Nacimientos según multiplicidad del parto
- `COD_FAMILIA_VARIABLES:16` → Nacimientos según edad de la madre
- `COD_FAMILIA_VARIABLES:18` → Nacimientos por número de hijos
- `COD_FAMILIA_VARIABLES:20` → Matrimonios de distinto sexo
- `COD_FAMILIA_VARIABLES:21` → Matrimonios según tipo de celebración
- `COD_FAMILIA_VARIABLES:23` → Matrimonios según estado civil del varón
- `COD_FAMILIA_VARIABLES:24` → Matrimonios según estado civil de la mujer
- `COD_FAMILIA_VARIABLES:27` → Matrimonios por edad del varón
- `COD_FAMILIA_VARIABLES:28` → Matrimonios por edad de la mujer
- `COD_FAMILIA_VARIABLES:29` → Matrimonios del mismo sexo
- `COD_FAMILIA_VARIABLES:30` → Defunciones
- `COD_FAMILIA_VARIABLES:32` → Defunciones por sexo
- `COD_FAMILIA_VARIABLES:34` → Defunciones según estado civil
- `COD_FAMILIA_VARIABLES:36` → Defunciones por edad

#### Parámetro `AC` (Variables en columnas)
Ver listado de variables abajo.

#### Parámetro `ABC` (Totales por columnas)
- `1` = Sí
- `0` = No

#### Parámetro `D` (Variables en filas)
Ver listado de variables abajo.

#### Parámetro `DC` (Totales por filas)
- `1` = Sí
- `0` = No

#### Parámetro `_SAVEAS` (Nombre del archivo)
- Formato: `"Nombre.csv"`

### 5.3 Variables Disponibles

| Variable | Descripción | Valores/Formato |
|----------|-------------|-----------------|
| `ANNO` | Año de los datos | AAAA (ej. 2020) |
| `COD_PROVINCIA` | Código INE provincia | 05, 09, 24, 34, 37, 40, 42, 47, 49 |
| `NOM_PROVINCIA` | Nombre provincia | Ávila, Burgos, León, Palencia, Salamanca, Segovia, Soria, Valladolid, Zamora |
| `COD_MUNICIPIO` | Código INE municipio | Ver Nomenclátor INE |
| `NOM_MUNICIPIO` | Nombre municipio | Ver Nomenclátor INE |

**Códigos de provincias:**
- 05: Ávila
- 09: Burgos
- 24: León
- 34: Palencia
- 37: Salamanca
- 40: Segovia
- 42: Soria
- 47: Valladolid
- 49: Zamora

### 5.4 Ejemplos de Consulta

#### Ejemplo 1: Nacimientos en Zamora 2009
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&SL=COD_FAMILIA_VARIABLES:10&SL=ANNO:2009&SL=COD_PROVINCIA:49&D=NOM_MUNICIPIO
```

Parámetros añadidos:
- `SL=COD_FAMILIA_VARIABLES:10` (Nacimientos)
- `SL=COD_PROVINCIA:49` (Zamora)
- `SL=ANNO:2009`
- `D=NOM_MUNICIPIO` (Mostrar por municipio)

#### Ejemplo 2: Nacimientos, Matrimonios y Defunciones CyL (2005-2009)
```
Parámetros:
SL=COD_FAMILIA_VARIABLES:10&
SL=COD_FAMILIA_VARIABLES:20&
SL=COD_FAMILIA_VARIABLES:30&
SL=ANNO:2005&SL=ANNO:2006&SL=ANNO:2007&SL=ANNO:2008&SL=ANNO:2009&
AC=ANNO
```

#### Ejemplo 3: Matrimonios por edad en Valladolid capital
```
Parámetros:
SL=COD_FAMILIA_VARIABLES:27&
SL=COD_FAMILIA_VARIABLES:28&
SL=COD_MUNICIPIO:47186&
AC=ANNO
```

### 5.5 Consideraciones Técnicas

**Caracteres especiales en URLs:**
- Acentos, espacios y caracteres especiales deben codificarse
- Usar `%` seguido del código ASCII hexadecimal
- Herramienta recomendada: http://ascii.cl/es/url-encoding.htm

**Formato de respuesta:**
- CSV por defecto
- Parsear en backend y almacenar en BD

---

## 6. ARQUITECTURA SUGERIDA

### 6.1 Estructura de Carpetas (Ejemplo con Laravel)

```
proyecto-mnp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ApiController.php       # Consumo API MNP
│   │   │   ├── DashboardController.php # Vista principal
│   │   │   ├── AuthController.php      # Login/Registro
│   │   │   └── DataController.php      # Consultas a BD
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Nacimiento.php
│   │   ├── Matrimonio.php
│   │   └── Defuncion.php
│   └── Services/
│       └── MnpApiService.php           # Lógica de API
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   ├── js/
│   │   ├── app.js                      # Lógica frontend principal
│   │   ├── charts.js                   # Gráficos con Chart.js
│   │   └── filters.js                  # Sistema de filtros
│   └── index.php
├── resources/
│   └── views/
│       ├── layouts/
│       ├── dashboard.blade.php
│       ├── login.blade.php
│       └── register.blade.php
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
├── tests/
├── docker-compose.yml                  # Docker (opcional)
├── .env
├── composer.json
└── README.md
```

### 6.2 Flujo de Datos

```
[API MNP] ---> [Backend PHP] ---> [Base de Datos]
                     ↑                    ↓
                     |                    |
              [Auth/Session]        [Consultas]
                     |                    |
                     ↓                    ↓
              [Frontend JS] <--- [JSON Response]
                     ↓
            [Chart.js / Tablas]
                     ↓
              [Usuario Final]
```

### 6.3 Base de Datos (Ejemplo de Tablas)

**Tabla: `users`**
```sql
id, name, email, password, role, created_at, updated_at
```

**Tabla: `nacimientos`**
```sql
id, anno, cod_provincia, nom_provincia, cod_municipio, nom_municipio, 
sexo, valor, familia_variable, created_at, updated_at
```

**Tabla: `matrimonios`**
```sql
id, anno, cod_provincia, nom_provincia, tipo_celebracion, 
edad_varon, edad_mujer, valor, familia_variable, created_at, updated_at
```

**Tabla: `defunciones`**
```sql
id, anno, cod_provincia, nom_provincia, sexo, edad, estado_civil, 
valor, familia_variable, created_at, updated_at
```

---

## 7. FUNCIONALIDADES CLAVE A IMPLEMENTAR

### 7.1 Sistema de Importación de Datos

**Comando/Script de importación:**
```php
// Ejemplo: ImportMnpData.php
class ImportMnpData {
    public function import($familia_variable, $anno_inicio, $anno_fin) {
        // 1. Construir URL con parámetros
        // 2. Realizar petición cURL
        // 3. Parsear CSV
        // 4. Insertar en BD (usar transacciones)
        // 5. Log de proceso
    }
}
```

**Consideraciones:**
- Ejecutar manualmente o vía cron job
- Manejar errores de red/timeout
- Validar datos antes de insertar

### 7.2 Dashboard Interactivo

**Componentes:**
1. **Panel de filtros:**
   - Selector de provincia (dropdown)
   - Selector de año (range o multi-select)
   - Selector de indicador (radio buttons o tabs)
   - Botón "Aplicar filtros"

2. **Área de visualización:**
   - Tabla con datos filtrados
   - Gráficos dinámicos:
     - Líneas: Evolución temporal
     - Barras: Comparación entre provincias
     - Pie: Distribución por categorías
   
3. **Exportación:**
   - Botón "Descargar CSV"
   - Botón "Descargar PDF" (opcional, usar TCPDF/Dompdf)

### 7.3 Sistema de Autenticación

**Rutas:**
- `GET /login` → Formulario de login
- `POST /login` → Procesar login
- `GET /register` → Formulario de registro
- `POST /register` → Procesar registro
- `POST /logout` → Cerrar sesión

**Seguridad:**
- Contraseñas con `password_hash()`
- Validación de inputs (server-side)
- Protección CSRF
- Rate limiting (intentos de login)

### 7.4 API REST (Endpoints Frontend)

**Endpoints sugeridos:**

```
GET /api/data/nacimientos?provincia={cod}&anno={year}
GET /api/data/matrimonios?provincia={cod}&anno={year}
GET /api/data/defunciones?provincia={cod}&anno={year}
GET /api/provincias
GET /api/indicadores
POST /api/export/csv
POST /api/export/pdf
```

**Respuesta JSON:**
```json
{
  "success": true,
  "data": [
    {
      "anno": 2020,
      "provincia": "Salamanca",
      "valor": 1234,
      "descripcion": "Nacimientos totales"
    }
  ],
  "meta": {
    "total": 150,
    "filtros": {
      "provincia": "37",
      "anno": "2020"
    }
  }
}
```

---

## 8. FRONTEND: INTERACTIVIDAD CON JAVASCRIPT

### 8.1 Ejemplo: Sistema de Filtros con Fetch

```javascript
// filters.js
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const provinciaSelect = document.getElementById('provincia');
    const annoSelect = document.getElementById('anno');
    const indicadorSelect = document.getElementById('indicador');
    const resultsContainer = document.getElementById('results');

    filterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const provincia = provinciaSelect.value;
        const anno = annoSelect.value;
        const indicador = indicadorSelect.value;

        // Mostrar loading
        resultsContainer.innerHTML = '<div class="loader">Cargando...</div>';

        try {
            const response = await fetch(`/api/data/${indicador}?provincia=${provincia}&anno=${anno}`);
            const data = await response.json();

            if (data.success) {
                renderResults(data.data);
                renderChart(data.data);
            } else {
                showError(data.message);
            }
        } catch (error) {
            showError('Error al cargar los datos');
            console.error(error);
        }
    });

    function renderResults(data) {
        let html = '<table class="data-table"><thead><tr>';
        html += '<th>Año</th><th>Provincia</th><th>Valor</th></tr></thead><tbody>';
        
        data.forEach(row => {
            html += `<tr>
                <td>${row.anno}</td>
                <td>${row.provincia}</td>
                <td>${row.valor}</td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        resultsContainer.innerHTML = html;
    }

    function renderChart(data) {
        // Ver sección 8.2 para Chart.js
    }
});
```

### 8.2 Ejemplo: Gráficos con Chart.js

```javascript
// charts.js
function renderChart(data) {
    const ctx = document.getElementById('myChart').getContext('2d');
    
    // Preparar datos
    const labels = data.map(row => row.anno);
    const values = data.map(row => row.valor);

    // Destruir gráfico anterior si existe
    if (window.myChart instanceof Chart) {
        window.myChart.destroy();
    }

    // Crear nuevo gráfico
    window.myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Evolución temporal',
                data: values,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Datos del Movimiento Natural de la Población'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
```

### 8.3 Validación de Formularios

```javascript
// validation.js
function validateForm(form) {
    const provincia = form.querySelector('#provincia').value;
    const anno = form.querySelector('#anno').value;

    let errors = [];

    if (!provincia) {
        errors.push('Debes seleccionar una provincia');
    }

    if (!anno || anno < 2000 || anno > 2024) {
        errors.push('Año inválido');
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }

    return true;
}

function showErrors(errors) {
    const errorContainer = document.getElementById('errors');
    errorContainer.innerHTML = errors.map(err => 
        `<div class="alert alert-danger">${err}</div>`
    ).join('');
}
```

---

## 9. DISEÑO RESPONSIVO Y ACCESIBLE

### 9.1 Breakpoints Mínimos

```css
/* Mobile first */
/* Base: < 768px (mobile) */

@media (min-width: 768px) {
    /* Tablet */
}

@media (min-width: 1024px) {
    /* Desktop */
}
```

### 9.2 Ejemplo de Layout Responsivo (con Tailwind CSS)

```html
<div class="container mx-auto px-4">
    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block mb-2">Provincia</label>
            <select class="w-full p-2 border rounded">...</select>
        </div>
        <div>
            <label class="block mb-2">Año</label>
            <select class="w-full p-2 border rounded">...</select>
        </div>
        <div>
            <label class="block mb-2">Indicador</label>
            <select class="w-full p-2 border rounded">...</select>
        </div>
    </div>

    <!-- Resultados -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <!-- Tabla -->
        </div>
        <div class="bg-white p-6 rounded shadow">
            <!-- Gráfico -->
        </div>
    </div>
</div>
```

### 9.3 Accesibilidad (A11Y)

**Checklist básico:**
- [ ] Etiquetas `<label>` asociadas a inputs
- [ ] Atributos ARIA donde sea necesario
- [ ] Contraste de colores suficiente (WCAG AA)
- [ ] Navegación por teclado funcional
- [ ] Textos alternativos en imágenes
- [ ] Estructura semántica HTML5

---

## 10. CONTROL DE VERSIONES CON GIT

### 10.1 Estrategia de Ramas Recomendada

```
main (producción)
├── develop (desarrollo)
│   ├── feature/auth-system
│   ├── feature/dashboard
│   ├── feature/api-integration
│   ├── feature/charts
│   └── feature/export-data
└── hotfix/bug-fix-nombre
```

### 10.2 Convención de Commits

Formato: `tipo(ámbito): descripción`

**Tipos:**
- `feat`: Nueva funcionalidad
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Formato, espacios (sin cambios de código)
- `refactor`: Refactorización de código
- `test`: Añadir o modificar tests
- `chore`: Tareas de mantenimiento

**Ejemplos:**
```bash
git commit -m "feat(auth): implementar registro de usuarios"
git commit -m "fix(api): corregir encoding de parámetros en URL"
git commit -m "docs(readme): añadir instrucciones de instalación"
git commit -m "style(dashboard): mejorar espaciado en filtros"
```

### 10.3 .gitignore Recomendado

```gitignore
# Laravel
/vendor/
/node_modules/
.env
storage/*.key
*.log

# IDE
.vscode/
.idea/

# OS
.DS_Store
Thumbs.db

# Compilados
/public/build/
/public/hot
```

---

## 11. DESPLIEGUE Y PRODUCCIÓN

### 11.1 Opciones de Hosting

**Gratuitas:**
- Railway (PHP + MySQL)
- Render (Docker)
- PythonAnywhere (si se usa Python)
- 000webhost (PHP básico)

**De pago económico:**
- DigitalOcean ($6/mes)
- Linode
- Vultr

### 11.2 Docker Compose (Ejemplo)

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_DATABASE=mnp_db
      - DB_USERNAME=mnp_user
      - DB_PASSWORD=secret

  db:
    image: mariadb:10.6
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: mnp_db
      MYSQL_USER: mnp_user
      MYSQL_PASSWORD: secret
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

volumes:
  db_data:
```

### 11.3 Script de Despliegue Básico

```bash
#!/bin/bash
# deploy.sh

echo "🚀 Iniciando despliegue..."

# Pull cambios
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Migraciones
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos
chmod -R 775 storage bootstrap/cache

echo "✅ Despliegue completado"
```

---

## 12. CRITERIOS DE INNOVACIÓN (20% de RA2)

Para maximizar esta puntuación, considera:

### 12.1 Integración con IA

**Sugerencias:**
- **Predicciones:** Usar ML para predecir tendencias futuras de nacimientos/defunciones
- **Chatbot:** Asistente que responda preguntas sobre los datos
- **Análisis de texto:** Generar resúmenes automáticos de las estadísticas
- **APIs de IA:** OpenAI API, Anthropic Claude API, Google Gemini

**Ejemplo con Claude AI:**
```javascript
async function generateSummary(data) {
    const response = await fetch('/api/ai/summarize', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ data: data })
    });
    return await response.json();
}
```

### 12.2 Sostenibilidad y Eficiencia

**Medidas a documentar:**
- Queries optimizadas (uso de índices)
- Caché de consultas frecuentes (Redis)
- Lazy loading de imágenes/gráficos
- Minificación de CSS/JS
- CDN para assets estáticos
- Compresión GZIP en servidor

### 12.3 Funcionalidades Extras

**Ideas valoradas:**
- Sistema de comparación entre provincias
- Alertas/notificaciones de nuevos datos
- Exportación a múltiples formatos (JSON, XML, Excel)
- Modo oscuro (dark mode)
- Multiidioma (i18n)
- PWA (Progressive Web App)
- Integración con mapas (Leaflet + datos geoespaciales)

---

## 13. ENTREGABLES Y FORMATO

### 13.1 Repositorio GitHub

**Estructura obligatoria:**
```
repo-nombre/
├── docs/
│   ├── Memoria_NombreProyecto_NombreEquipo.pdf
│   ├── Presentacion.pptx
│   └── video-demo.mp4 (máx. 5min)
├── src/ o app/
├── database/
│   └── dump.sql o migrations/
├── public/
├── README.md (instrucciones de instalación)
├── .env.example
└── (resto de código)
```

### 13.2 Memoria (Mínimo 10 páginas)

**Estructura:**
1. **Portada:** Título, nombre del equipo, integrantes, fecha
2. **Índice:** Generado automáticamente
3. **Introducción:** Contexto y objetivos
4. **Estado del arte:** Investigación previa
5. **Análisis de requisitos:** Funcionales y no funcionales
6. **Diseño:**
   - Arquitectura del sistema
   - Modelo de datos (diagramas)
   - Mockups/wireframes
7. **Implementación:**
   - Tecnologías utilizadas
   - Fragmentos de código relevantes
   - Decisiones técnicas
8. **Pruebas:** Casos de prueba y resultados
9. **Despliegue:** Proceso y URL de producción
10. **Conclusiones y trabajo futuro**
11. **Bibliografía:** Formato IEEE o APA

### 13.3 Presentación PowerPoint

**Máximo 10 diapositivas:**
1. Portada
2. Índice/agenda
3. Introducción al dataset (1 diapositiva)
4. Funcionalidades clave (máx. 3 diapositivas)
5. Demo en video (integrado o referencia)
6. Aspectos técnicos destacables (1 diapositiva)
7. Conclusiones y líneas futuras
8. Preguntas

### 13.4 Video Demostración

**Contenido (máx. 5 minutos):**
- Inicio de sesión
- Aplicar filtros y ver resultados
- Visualización de gráficos
- Exportación de datos
- Funcionalidad innovadora (si aplica)

**Herramientas de grabación:**
- OBS Studio
- Loom
- Screen Studio (Mac)

---

## 14. CHECKLIST FINAL

### Backend
- [ ] API configurada para consumir datos MNP
- [ ] Base de datos diseñada e implementada
- [ ] Migraciones y seeders creados
- [ ] Sistema de autenticación funcional
- [ ] Endpoints API REST documentados
- [ ] Manejo de errores robusto
- [ ] Validación de datos server-side

### Frontend
- [ ] Interfaz responsiva (mobile + desktop)
- [ ] Fetch API para comunicación asíncrona
- [ ] Validación de formularios en cliente
- [ ] Gráficos interactivos implementados
- [ ] Sistema de filtros funcional
- [ ] Exportación de datos (CSV mínimo)
- [ ] Diseño atractivo y usable

### Infraestructura
- [ ] Repositorio Git con historial claro
- [ ] README con instrucciones de instalación
- [ ] .env.example configurado
- [ ] Commits atómicos y descriptivos
- [ ] Despliegue en producción (opcional pero valorado)

### Documentación
- [ ] Memoria completa (>10 páginas)
- [ ] Presentación PowerPoint preparada
- [ ] Video demo grabado (<5 min)
- [ ] Diagramas y mockups incluidos
- [ ] Bibliografía citada correctamente

### Innovación
- [ ] Al menos una característica innovadora
- [ ] Medidas de eficiencia documentadas
- [ ] Justificación técnica de decisiones

---

## 15. RECURSOS Y ENLACES ÚTILES

### Documentación Oficial
- **Laravel:** https://laravel.com/docs
- **PHP:** https://www.php.net/manual/es/
- **MySQL:** https://dev.mysql.com/doc/
- **Chart.js:** https://www.chartjs.org/docs/

### Datasets
- **Portal Datos Abiertos CyL:** https://datosabiertos.jcyl.es/
- **Nomenclátor INE:** https://www.ine.es/daco/daco42/codmun/codmunmapa.htm

### APIs y Librerías
- **Tailwind CSS:** https://tailwindcss.com/
- **Alpine.js:** https://alpinejs.dev/
- **Leaflet.js:** https://leafletjs.com/

### Herramientas
- **URL Encoder:** http://ascii.cl/es/url-encoding.htm
- **Git Flow Cheatsheet:** https://danielkummer.github.io/git-flow-cheatsheet/
- **Docker Hub:** https://hub.docker.com/

### Tutoriales
- **Laravel desde cero:** https://laracasts.com/
- **Chart.js ejemplos:** https://www.chartjs.org/samples/

---

## 16. TIPS Y MEJORES PRÁCTICAS

### Para el Desarrollo
1. **Empieza simple:** Implementa primero la funcionalidad básica, luego añade extras
2. **Commits frecuentes:** Guarda tu progreso regularmente
3. **Testing temprano:** No dejes las pruebas para el final
4. **Documentación inline:** Comenta tu código mientras lo escribes
5. **Pair programming:** Trabajad en pareja para resolver problemas complejos

### Para la Presentación
1. **Ensaya:** Practica varias veces el pitch de 10 minutos
2. **Sé conciso:** Enfócate en lo diferencial, no expliques lo obvio
3. **Prepara respuestas:** Anticipa preguntas del tribunal
4. **Demostración sólida:** Asegúrate de que todo funciona antes
5. **Muestra pasión:** Transmite entusiasmo por tu proyecto

### Para la Memoria
1. **No rellenes:** Mejor calidad que cantidad
2. **Diagramas claros:** Una imagen vale más que mil palabras
3. **Revisa ortografía:** Usa correctores automáticos
4. **Citas correctas:** No plagies, cita siempre tus fuentes
5. **Conclusiones honestas:** Reflexiona sobre limitaciones y mejoras

---

## 17. GLOSARIO TÉCNICO

- **API:** Application Programming Interface
- **AJAX:** Asynchronous JavaScript and XML
- **CRUD:** Create, Read, Update, Delete
- **CSV:** Comma-Separated Values
- **DOM:** Document Object Model
- **MVC:** Model-View-Controller
- **ORM:** Object-Relational Mapping
- **PDO:** PHP Data Objects
- **REST:** Representational State Transfer
- **SPA:** Single Page Application
- **URI:** Uniform Resource Identifier

---

## CONCLUSIÓN

Este documento proporciona una guía completa para desarrollar el proyecto intermodular DAW. Recuerda que:

- **La funcionalidad básica es lo primero:** Asegura que los requisitos mínimos estén cubiertos
- **La innovación suma puntos:** Pero no a costa de la estabilidad
- **La documentación es clave:** Un buen proyecto mal documentado pierde valor
- **El trabajo en equipo importa:** Coordinación y comunicación constante

**¡Éxito con el proyecto!** 🚀

---

**Última actualización:** Enero 2026
**Contacto:** Equipo docente DAW
