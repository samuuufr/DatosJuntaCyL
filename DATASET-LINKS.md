# Dataset Links - Demografía CyL

## API MNP (Movimiento Natural Población)

**Base URL:**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&
```

### Parámetros MNP
| Param | Uso |
|-------|-----|
| `SL=COD_FAMILIA_VARIABLES:X` | Indicador (10=nacim, 30=defunc, 20=matrim) |
| `SL=ANNO:YYYY` | Filtro año |
| `SL=COD_PROVINCIA:XX` | Filtro provincia |
| `D=NOM_MUNICIPIO` | Agrupar por municipio |
| `AC=ANNO` | Años en columnas |

### Códigos Provincia
05=Ávila, 09=Burgos, 24=León, 34=Palencia, 37=Salamanca, 40=Segovia, 42=Soria, 47=Valladolid, 49=Zamora

### Códigos Indicador
| Código | Indicador |
|--------|-----------|
| 10 | Nacimientos |
| 12 | Nacimientos por sexo |
| 16 | Nacimientos por edad madre |
| 20 | Matrimonios |
| 30 | Defunciones |
| 32 | Defunciones por sexo |
| 36 | Defunciones por edad |

### Ejemplos MNP
```bash
# Nacimientos CyL todos los años
[BASE_URL]SL=COD_FAMILIA_VARIABLES:10&AC=ANNO

# Defunciones CyL todos los años
[BASE_URL]SL=COD_FAMILIA_VARIABLES:30&AC=ANNO

# Nacimientos Salamanca 2020-2023
[BASE_URL]SL=COD_FAMILIA_VARIABLES:10&SL=COD_PROVINCIA:37&SL=ANNO:2020&SL=ANNO:2021&SL=ANNO:2022&SL=ANNO:2023&AC=ANNO

# Nacimientos por municipio Zamora 2023
[BASE_URL]SL=COD_FAMILIA_VARIABLES:10&SL=COD_PROVINCIA:49&SL=ANNO:2023&D=NOM_MUNICIPIO
```

---

## API Opendatasoft (JSON/CSV)

**Base:** `https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/`

### Descargas Directas CSV
```bash
# Población por provincias y sexo
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv

# Población por edades y sexo (pirámides)
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/exports/csv

# Municipios CyL
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv
```

### API JSON (limit=100 max)
```bash
# Población Salamanca 2023
[BASE]poblacion-total-por-provincias-y-sexo/records?where=provincia="Salamanca" AND ano=2023

# Top 5 provincias
[BASE]poblacion-total-por-provincias-y-sexo/records?where=ano=2023&order_by=total DESC&limit=5

# Solo campos específicos
[BASE]poblacion-total-por-provincias-y-sexo/records?select=provincia,ano,total&limit=50
```

---

## Scripts Ejemplo

### PHP (cURL)
```php
$url = "https://analisis.datosabiertos.jcyl.es/.../exports/csv";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$csv = curl_exec($ch);
curl_close($ch);
file_put_contents('data.csv', $csv);
```

### JS (fetch)
```javascript
const url = 'https://analisis.datosabiertos.jcyl.es/.../records?limit=100';
fetch(url)
  .then(r => r.json())
  .then(data => console.log(data.results));
```

---

## Notas Técnicas
- **Delimiter CSV:** `;` (punto y coma)
- **Encoding URL:** Acentos requieren `%XX` (usar http://ascii.cl/es/url-encoding.htm)
- **Sin autenticación** requerida
- **Opendatasoft:** máx 100 records/request, usar `offset` para paginar
- **MNP:** CSV completo sin paginación, timeout 30s recomendado

## Datasets Obligatorios
- [ ] MNP Nacimientos + Defunciones
- [ ] Población por provincias
- [ ] Municipios CyL

## Datasets Recomendados
- [ ] Población por edades (pirámides)
- [ ] Nacimientos por edad madre
- [ ] Defunciones por edad
