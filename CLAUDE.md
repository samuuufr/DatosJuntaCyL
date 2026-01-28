# Demografía CyL - Proyecto DAW

## Estado
- **Fase:** Desarrollo
- **Entrega:** 5 febrero 2026 | **Defensa:** 6 febrero 2026

## Archivos Clave
| Archivo | Contenido |
|---------|-----------|
| `DATASET-LINKS.md` | APIs, URLs descarga, ejemplos |
| `TECHNICAL-SPECS.md` | Requisitos técnicos, arquitectura |
| `docs/manual-mnp.pdf` | Manual API MNP oficial |

## Objetivo
App web Laravel que consume datos MNP (nacimientos/defunciones/matrimonios) de Castilla y León, los almacena en BD y los presenta con gráficos interactivos y filtros asíncronos.

## Stack
**Backend:** PHP 8.2+, Laravel 11, MariaDB, PDO  
**Frontend:** JS ES6 (Vanilla), Chart.js, Tailwind CSS, Fetch API  
**Infra:** Git/GitHub, Docker (opcional)

## BD Principal
```sql
-- provincias: id, codigo_ine, nombre
-- municipios: id, provincia_id, codigo_ine, nombre
-- datos_mnp: id, municipio_id, anno, tipo_evento(nacimiento|defuncion|matrimonio), valor
-- usuarios: id, email, password, nombre, rol
-- favoritos: usuario_id, municipio_id
```

## Funcionalidades
1. **Dashboard:** Mapa CyL, KPIs, gráfico evolución temporal
2. **Análisis Provincial:** Selector, pirámide población, indicadores
3. **Análisis Municipal:** Buscador, ficha detallada
4. **Auth:** Registro/login con $_SESSION, password_hash
5. **Export:** CSV (mínimo)
6. **Innovación:** Predictor IA, Claude API insights

## Evaluación (ver rúbrica PDF)
| Criterio | Peso |
|----------|------|
| Asistencia | 20% |
| **Contenido técnico** | **60%** |
| → Backend (BD+API+lógica) | 30% |
| → Frontend (UI+AJAX+validación) | 30% |
| → Infraestructura (Git+deploy) | 20% |
| → Innovación (IA+sostenibilidad) | 20% |
| Memoria | 10% |
| Presentación | 10% |

## Checklist Rápido
- [ ] Migraciones BD
- [ ] Script importación MNP
- [ ] Modelos Eloquent con relaciones
- [ ] API endpoints JSON
- [ ] Dashboard con Chart.js
- [ ] Filtros asíncronos (fetch)
- [ ] Auth (registro/login)
- [ ] Diseño responsive (mobile+desktop)
- [ ] Commits atómicos descriptivos

## Comandos
```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Restricciones
✅ Fetch API (no jQuery) | ✅ Validación client+server | ✅ Responsive | ✅ MVC  
⭐ Laravel/Symfony | ⭐ Docker | ⭐ Deploy producción | ⭐ IA
