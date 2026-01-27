# Enlaces Directos a Datasets y APIs - Junta de Castilla y León
## Para Proyecto Demográfico: Nacimientos y Defunciones

---

## 🎯 ÍNDICE DE RECURSOS

1. [Movimiento Natural de la Población (MNP)](#1-movimiento-natural-de-la-población-mnp)
2. [Padrón y Estructura de Población](#2-padrón-y-estructura-de-población)
3. [Indicadores Demográficos](#3-indicadores-demográficos)
4. [Datos Geográficos y Mapas](#4-datos-geográficos-y-mapas)
5. [APIs y Documentación Técnica](#5-apis-y-documentación-técnica)
6. [Datos del INE (Complementarios)](#6-datos-del-ine-complementarios)

---

## 1. MOVIMIENTO NATURAL DE LA POBLACIÓN (MNP)

### 📊 Dataset Principal (YA TIENES EL MANUAL)

**Portal Interactivo (Explorador Web):**
```
https://servicios4.jcyl.es/estadistica/sie/MNP/
```
🔍 **Descripción:** Interfaz web donde puedes explorar y visualizar los datos de nacimientos, matrimonios y defunciones antes de descargarlos.

**API Base (Descargas CSV):**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&
```
🔍 **Descripción:** URI base para construir consultas API y descargar datos en CSV. Añade parámetros según el manual proporcionado.

### 📝 Ejemplos de Consultas Directas

**Nacimientos totales en CyL por año (2005-2023):**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&SL=COD_FAMILIA_VARIABLES:10&AC=ANNO
```

**Defunciones totales en CyL por año (2005-2023):**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&SL=COD_FAMILIA_VARIABLES:30&AC=ANNO
```

**Nacimientos por sexo en Salamanca (2020-2023):**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&SL=COD_FAMILIA_VARIABLES:12&SL=COD_PROVINCIA:37&SL=ANNO:2020&SL=ANNO:2021&SL=ANNO:2022&SL=ANNO:2023&AC=ANNO
```

**Defunciones por edad en todas las provincias (2023):**
```
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&SL=COD_FAMILIA_VARIABLES:36&SL=ANNO:2023&D=NOM_PROVINCIA
```

---

## 2. PADRÓN Y ESTRUCTURA DE POBLACIÓN

### 📊 Portal Principal Estadísticas Demográficas

**Web Principal:**
```
https://estadistica.jcyl.es/web/es/estadisticas-temas/demograficas.html
```
🔍 **Descripción:** Índice de todas las estadísticas demográficas disponibles.

**Padrón Continuo:**
```
https://estadistica.jcyl.es/web/es/estadisticas-temas/padron-continuo.html
```
🔍 **Descripción:** Acceso a datos de población por edad, sexo, municipio desde 2002.

### 📥 Datasets en Portal de Análisis (Opendatasoft)

#### Dataset 1: Población Total por Provincias y Sexo

**Web del Dataset:**
```
https://analisis.datosabiertos.jcyl.es/explore/dataset/poblacion-total-por-provincias-y-sexo/
```

**API JSON (Todos los datos):**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?limit=100
```

**Descarga CSV directa:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv
```

**Descarga JSON directa:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/json
```

**Ejemplo de consulta API con filtros:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?where=provincia="Salamanca" AND ano>=2020&limit=20
```

#### Dataset 2: Población Total por Edades y Sexo

**Web del Dataset:**
```
https://analisis.datosabiertos.jcyl.es/explore/dataset/poblacion-total-por-edades-y-sexo/
```

**API JSON:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/records?limit=100
```

**Descarga CSV:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/exports/csv
```

🔍 **Descripción:** Población de CyL desglosada por edades (año a año) y sexo desde 2002. Ideal para pirámides de población.

#### Dataset 3: Población Total de CyL por Sexo (Serie Histórica)

**Web del Dataset:**
```
https://analisis.datosabiertos.jcyl.es/explore/dataset/poblacion-total-de-castilla-y-leon-por-sexo/
```

**API JSON:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-de-castilla-y-leon-por-sexo/records?limit=100
```

**Descarga CSV:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-de-castilla-y-leon-por-sexo/exports/csv
```

---

## 3. INDICADORES DEMOGRÁFICOS

### 📊 Portal SIE (Sistema de Información Estadística)

**Portal Indicadores Demográficos:**
```
https://estadistica.jcyl.es/web/es/estadisticas-temas/indicadores-demograficos.html
```

**Página de Datos Abiertos - Indicadores:**
```
https://datosabiertos.jcyl.es/web/jcyl/set/es/demografia/indicadores-demograficos/1284801443556
```

🔍 **Descripción:** Dataset que combina población, nacimientos, defunciones, matrimonios en indicadores pre-calculados por municipio y año.

**Formato disponible:** CSV, XLS

---

## 4. DATOS GEOGRÁFICOS Y MAPAS

### 🗺️ Catálogo de Información Cartográfica (IDECyL)

**Portal Principal:**
```
https://idecyl.jcyl.es/geonetwork
```

#### Municipios de CyL (Registro)

**Dataset en Portal de Análisis:**
```
https://analisis.datosabiertos.jcyl.es/explore/dataset/registro-de-municipios-de-castilla-y-leon/
```

**API JSON:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/records?limit=100
```

**Descarga CSV:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv
```

🔍 **Descripción:** Listado completo de municipios con códigos INE, provincia, superficie, coordenadas.

#### Población 2024 con Cartografía

**Metadatos:**
```
https://idecyl.jcyl.es/geonetwork/srv/api/records/spagobcyltempobinehab2024
```

🔍 **Descripción:** Datos de población 2024 con información geográfica para mapas.

#### Entidades de Población (Puntos Geográficos)

**Metadatos:**
```
https://idecyl.jcyl.es/geonetwork/static/api/records/SPAGOBCYLCITDTSSUEPP
```

🔍 **Descripción:** Coordenadas geográficas de núcleos de población y asentamientos. Útil para mapas con Leaflet.js.

---

## 5. APIS Y DOCUMENTACIÓN TÉCNICA

### 📚 Documentación de la API Opendatasoft

**Documentación General:**
```
https://help.opendatasoft.com/apis/ods-explore-api/explore_v2.html
```

**Portal de APIs de la Junta:**
```
https://analisis.datosabiertos.jcyl.es/api/
```

### 🔧 Estructura de URLs de la API

**Patrón general:**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/{dataset_id}/{endpoint}
```

**Endpoints disponibles:**
- `/records` - Obtener registros
- `/exports/csv` - Exportar CSV
- `/exports/json` - Exportar JSON
- `/exports/xlsx` - Exportar Excel

**Parámetros de consulta:**
- `limit` - Número de registros (máx. 100 por petición)
- `offset` - Paginación
- `where` - Filtros SQL-like
- `select` - Campos específicos
- `order_by` - Ordenación

### 📘 Ejemplos de Uso de la API

**Ejemplo 1: Obtener población de Salamanca en 2023**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?where=provincia="Salamanca" AND ano=2023
```

**Ejemplo 2: Top 5 provincias con más población**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?where=ano=2023&order_by=total DESC&limit=5
```

**Ejemplo 3: Población por edades entre 20-30 años**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/records?where=edad>=20 AND edad<=30 AND ano=2023
```

**Ejemplo 4: Solo campos específicos**
```
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?select=provincia,ano,total&limit=50
```

### 🔑 Autenticación

**No se requiere API key** para los endpoints públicos de datos abiertos.

---

## 6. DATOS DEL INE (COMPLEMENTARIOS)

### 📊 Instituto Nacional de Estadística

#### Cifras Oficiales de Población

**Web:**
```
https://www.ine.es/dyngs/INEbase/es/operacion.htm?c=Estadistica_C&cid=1254736177011
```

**Descarga directa (ejemplo Castilla y León 2024):**
```
https://www.ine.es/jaxiT3/Tabla.htm?t=2852&L=0
```

#### Proyecciones de Población

**Web:**
```
https://www.ine.es/dyngs/INEbase/es/operacion.htm?c=Estadistica_C&cid=1254736176953
```

**Datos descargables:**
```
https://www.ine.es/jaxiT3/Tabla.htm?t=1454
```

#### Esperanza de Vida

**Web:**
```
https://www.ine.es/dyngs/INEbase/es/operacion.htm?c=Estadistica_C&cid=1254736177004
```

#### Defunciones por Causa

**Web:**
```
https://www.ine.es/dyngs/INEbase/es/operacion.htm?c=Estadistica_C&cid=1254736176780
```

#### Nomenclátor (Códigos de Municipios)

**Web:**
```
https://www.ine.es/nomen2/index.do
```

**Descarga completa de códigos:**
```
https://www.ine.es/daco/daco42/codmun/codmunmapa.htm
```

---

## 7. RESUMEN DE ENLACES CLAVE PARA EMPEZAR

### 🚀 Para Claude Code - Enlaces Prioritarios

#### 1️⃣ **Movimiento Natural de la Población (TU DATASET PRINCIPAL)**

```bash
# Portal Web Explorador
https://servicios4.jcyl.es/estadistica/sie/MNP/

# API Base (añade parámetros según manual)
http://www.jcyl.es/sie/sas/broker?_PROGRAM=sashelp.webeis.oprpt.scl&_SERVICE=saswebl&CLASS=mddbpgm.jcyl.custom_webeis2.class&METABASE=RPOSWEB&ST=1&FS=SUM&SPDSHT=X&MDDB=MNP.M_MNP&A=VALOR_VARIABLE&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE&
```

#### 2️⃣ **Población por Provincias y Sexo**

```bash
# CSV Directo (descarga inmediata)
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv

# JSON API (programático)
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?limit=100
```

#### 3️⃣ **Población por Edades y Sexo** (para pirámides)

```bash
# CSV Directo
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/exports/csv

# JSON API
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-edades-y-sexo/records?limit=100
```

#### 4️⃣ **Municipios de CyL** (listado completo)

```bash
# CSV Directo
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv

# JSON API
https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/records?limit=100
```

---

## 8. SCRIPTS DE EJEMPLO PARA DESCARGAR DATOS

### 🐍 Python - Ejemplo con requests

```python
import requests
import pandas as pd

# Descargar CSV de población
url = "https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv"
response = requests.get(url)

# Guardar localmente
with open('poblacion_provincias.csv', 'wb') as f:
    f.write(response.content)

# O cargar directamente en Pandas
df = pd.read_csv(url, delimiter=';')
print(df.head())
```

### 🌐 JavaScript/Node.js - Ejemplo con fetch

```javascript
const fetch = require('node-fetch');
const fs = require('fs');

// Descargar JSON de población
const url = 'https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/records?limit=100';

fetch(url)
  .then(res => res.json())
  .then(data => {
    console.log(`Total registros: ${data.total_count}`);
    console.log('Primeros 5 registros:');
    data.results.slice(0, 5).forEach(record => {
      console.log(record);
    });
    
    // Guardar en archivo
    fs.writeFileSync('poblacion.json', JSON.stringify(data, null, 2));
  })
  .catch(err => console.error('Error:', err));
```

### 🐘 PHP - Ejemplo con cURL

```php
<?php
// Descargar CSV de población
$url = "https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/poblacion-total-por-provincias-y-sexo/exports/csv";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$csv_data = curl_exec($ch);
curl_close($ch);

// Guardar en archivo
file_put_contents('poblacion_provincias.csv', $csv_data);

// O parsear directamente
$lines = explode("\n", $csv_data);
$headers = str_getcsv($lines[0], ';');
foreach (array_slice($lines, 1, 10) as $line) {
    $values = str_getcsv($line, ';');
    print_r(array_combine($headers, $values));
}
?>
```

---

## 9. CONSIDERACIONES TÉCNICAS

### ⚠️ Límites y Restricciones

**API Opendatasoft:**
- **Límite por petición:** 100 registros máximo
- **Paginación:** Usar parámetro `offset` para más datos
- **Rate limiting:** No especificado oficialmente, pero recomendado no exceder 100 req/min
- **Sin autenticación** para datasets públicos

**API SIE (Movimiento Natural):**
- Devuelve archivos CSV completos (sin paginación)
- Caracteres especiales requieren URL encoding
- Timeout recomendado: 30 segundos (archivos grandes)

### 🔄 Actualización de Datos

- **Población:** Anual (enero/febrero de cada año)
- **MNP (nacimientos/defunciones):** Anual (mayo/junio del año siguiente)
- **Indicadores:** Anual (tras publicación de MNP)

### 📦 Formatos Disponibles

**Portal Opendatasoft:**
- CSV (delimiter: `;` o `,`)
- JSON
- Excel (XLSX)
- GeoJSON (para datos geográficos)
- Shapefile (para datos geográficos)

**Portal SIE:**
- CSV (delimiter: variable, generalmente `,`)

---

## 10. HERRAMIENTAS RECOMENDADAS

### 🛠️ Testing y Exploración

**Postman/Insomnia:**
- Probar las APIs antes de implementar
- Colección recomendada: Importar ejemplos de arriba

**Excel/LibreOffice:**
- Abrir CSVs descargados para inspección rápida
- Delimiter: `;` (punto y coma)

**Online CSV Viewer:**
```
https://www.convertcsv.com/csv-viewer-editor.htm
```

### 📊 Análisis de Datos

**Python:**
- `pandas` - Manipulación de datos
- `requests` - Descargas HTTP
- `matplotlib/seaborn` - Visualizaciones

**R:**
- `httr` - Peticiones HTTP
- `jsonlite` - Parseo JSON
- `ggplot2` - Visualizaciones

---

## 11. CHECKLIST DE DATASETS PARA TU PROYECTO

### ✅ Datasets Obligatorios

- [ ] **MNP - Nacimientos** (totales por provincia y año)
- [ ] **MNP - Defunciones** (totales por provincia y año)
- [ ] **Población Total por Provincias** (para calcular tasas)
- [ ] **Municipios de CyL** (para selectores y filtros)

### ⭐ Datasets Muy Recomendados

- [ ] **Población por Edades y Sexo** (para pirámides)
- [ ] **MNP - Nacimientos por edad de la madre**
- [ ] **MNP - Defunciones por edad**
- [ ] **Indicadores Demográficos** (síntesis pre-calculada)

### 💡 Datasets Opcionales (Innovación)

- [ ] **MNP - Matrimonios** (análisis de nupcialidad)
- [ ] **Variaciones Residenciales** (migraciones)
- [ ] **Proyecciones INE** (predicción)
- [ ] **Datos geográficos** (mapas interactivos)

---

## 12. SOPORTE Y CONTACTO

### 📧 Contacto Junta de CyL

**Portal de Datos Abiertos:**
```
https://datosabiertos.jcyl.es/web/es/participa/contacto.html
```

**Estadística:**
```
https://estadistica.jcyl.es/web/jcyl/Estadistica/es/Plantilla100/1284382483714/_/_/_
```

### 🆘 Ayuda Técnica

**Documentación Opendatasoft:**
```
https://help.opendatasoft.com/
```

**Guías de Open Data JCyL:**
```
https://datosabiertos.jcyl.es/web/es/iniciativa-datos-abiertos/guias-open-data.html
```

---

## 📌 NOTAS FINALES PARA CLAUDE CODE

1. **Empieza por los CSV directos** - Son más fáciles de procesar que las APIs
2. **Usa la API solo si necesitas filtros complejos** o datos en tiempo real
3. **Combina MNP + Población** para calcular tasas e indicadores
4. **Cachea los datos localmente** - No descargues en cada ejecución
5. **Documenta las fuentes** en tu memoria del proyecto

**¡Con estos enlaces tienes todo lo necesario para desarrollar un proyecto de 10/10!** 🚀

---

**Fecha de creación:** Enero 2026  
**Última actualización:** Enero 2026  
**Autor:** Documentación para Proyecto Intermodular DAW  
**Fuentes:** Junta de Castilla y León, INE
