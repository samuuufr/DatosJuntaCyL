# Technical Specs - Proyecto DAW

## Evaluación
| Criterio | Peso | Instrumento |
|----------|------|-------------|
| RA1 Asistencia | 20% | Control diario |
| RA2 Técnico | 60% | Repo GitHub + URL |
| RA3 Memoria | 10% | .docx (mín 10 pág) |
| RA4 Presentación | 10% | .pptx + vídeo 5min |

### RA2 Desglose
- Backend (30%): BD + PDO + API + MVC
- Frontend (30%): Fetch + validación + responsive + Chart.js
- Infraestructura (20%): Git + commits atómicos + deploy
- Innovación (20%): IA + sostenibilidad + extras

## Stack Requerido

### Backend
**Básico:** PHP + MariaDB + Apache + PDO + cURL + $_SESSION  
**Avanzado:** Laravel/Symfony + Docker + Deploy producción

### Frontend
- JS ES6 + Fetch API (obligatorio, no jQuery)
- Chart.js / Leaflet.js (opcional)
- HTML5 + CSS3 + Tailwind/Bootstrap
- Responsive: mínimo mobile + desktop

### Infraestructura
- Git + GitHub (obligatorio)
- Commits atómicos descriptivos
- Docker (valorado)

## Estructura Proyecto
```
proyecto/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── DataController.php
│   │   └── AuthController.php
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Provincia.php
│   │   ├── Municipio.php
│   │   ├── DatoMnp.php
│   │   └── Favorito.php
│   └── Services/
│       └── MnpApiService.php
├── database/migrations/
├── resources/views/
├── public/js/
│   ├── app.js
│   ├── charts.js
│   └── filters.js
├── routes/
│   ├── web.php
│   └── api.php
└── docs/
    ├── Memoria.pdf
    ├── Presentacion.pptx
    └── demo.mp4
```

## Modelo de Datos
```sql
CREATE TABLE provincias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_ine VARCHAR(2) UNIQUE,
    nombre VARCHAR(100)
);

CREATE TABLE municipios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provincia_id INT REFERENCES provincias(id),
    codigo_ine VARCHAR(5),
    nombre VARCHAR(100)
);

CREATE TABLE datos_mnp (
    id INT PRIMARY KEY AUTO_INCREMENT,
    municipio_id INT REFERENCES municipios(id),
    anno YEAR,
    tipo_evento ENUM('nacimiento','defuncion','matrimonio'),
    valor INT,
    ultima_actualizacion TIMESTAMP
);

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    nombre VARCHAR(100),
    rol ENUM('usuario','admin') DEFAULT 'usuario'
);

CREATE TABLE favoritos (
    usuario_id INT REFERENCES usuarios(id),
    municipio_id INT REFERENCES municipios(id),
    PRIMARY KEY (usuario_id, municipio_id)
);
```

## API Endpoints
```
GET  /api/provincias
GET  /api/municipios?provincia_id={id}
GET  /api/datos?tipo={nacimiento|defuncion}&anno={year}&provincia={cod}
GET  /api/evolucion?municipio_id={id}&tipo={tipo}
GET  /api/ranking?anno={year}&tipo={tipo}&limit={n}
POST /api/favoritos
DELETE /api/favoritos/{municipio_id}
```

## Frontend - Filtros con Fetch
```javascript
document.getElementById('filtro-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const params = new URLSearchParams({
        provincia: document.getElementById('provincia').value,
        anno: document.getElementById('anno').value,
        tipo: document.getElementById('tipo').value
    });
    
    const res = await fetch(`/api/datos?${params}`);
    const data = await res.json();
    renderTable(data);
    updateChart(data);
});
```

## Frontend - Chart.js
```javascript
const ctx = document.getElementById('chart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: data.map(d => d.anno),
        datasets: [{
            label: 'Nacimientos',
            data: data.map(d => d.valor),
            borderColor: '#3b82f6'
        }]
    },
    options: { responsive: true }
});
```

## Autenticación
```php
// Registro
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
Usuario::create(['email' => $email, 'password' => $password]);

// Login
$user = Usuario::where('email', $email)->first();
if ($user && password_verify($_POST['password'], $user->password)) {
    $_SESSION['user_id'] = $user->id;
}
```

## Indicadores Calculados
```php
// Tasa natalidad = (nacimientos / población) × 1000
// Tasa mortalidad = (defunciones / población) × 1000
// Crecimiento vegetativo = nacimientos - defunciones
// Índice envejecimiento = (pob_>65 / pob_<15) × 100
```

## Entregables
1. **Repo GitHub:**
   - Código completo
   - `docs/Memoria.pdf`
   - `docs/Presentacion.pptx`
   - `docs/demo.mp4` (máx 5min)
   - `database/dump.sql`
   - `README.md` con instrucciones

2. **Memoria (.docx):** mín 10 páginas
   - Portada, índice auto
   - Introducción, análisis, diseño
   - Implementación, pruebas, despliegue
   - Conclusiones, bibliografía

3. **Presentación:** máx 10 diapositivas
   - 10min exposición + 5min preguntas

## Memoria - Secciones
1. Introducción
2. Análisis (dataset, requisitos, casos de uso)
3. Diseño (ER, mockups, paleta colores)
4. Desarrollo (stack, estructura, manual)
5. Pruebas (usabilidad, accesibilidad WAVE)
6. Despliegue (instrucciones, URLs)
7. Sostenibilidad (caché, optimización, green coding)
8. Conclusiones (dificultades, autoevaluación)

## Sostenibilidad (Puntos Extra)
- [ ] Caché de consultas API
- [ ] Minificación CSS/JS
- [ ] Lazy loading
- [ ] Índices BD optimizados
- [ ] Compresión GZIP

## Innovación (Puntos Extra)
- [ ] Predictor ML/IA
- [ ] Claude API insights
- [ ] Mapa interactivo Leaflet
- [ ] PWA
- [ ] Dark mode
- [ ] Comparador provincias

## Checklist Final
### Backend
- [ ] API MNP consumida
- [ ] BD con migraciones
- [ ] Auth funcional
- [ ] Endpoints REST
- [ ] Validación server-side

### Frontend
- [ ] Responsive mobile+desktop
- [ ] Fetch API (no recargas)
- [ ] Validación formularios
- [ ] Gráficos Chart.js
- [ ] Filtros dinámicos
- [ ] Export CSV

### Infraestructura
- [ ] Git historial limpio
- [ ] README completo
- [ ] .env.example
- [ ] Deploy (opcional)
