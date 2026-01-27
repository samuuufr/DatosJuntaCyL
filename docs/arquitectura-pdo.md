# Arquitectura de Acceso a Datos con PDO

## 📋 Resumen

Este proyecto utiliza **Laravel Eloquent ORM**, que internamente usa **PDO (PHP Data Objects)** para interactuar con la base de datos. Cumple con el requisito técnico de "PDO para acceso a datos" mientras mantiene código limpio y mantenible.

---

## 🏗️ Arquitectura en Capas

```
┌─────────────────────────────────────────────────────┐
│  CAPA 4: Controladores y Lógica de Negocio         │
│  (DashboardController, DataController, etc.)        │
├─────────────────────────────────────────────────────┤
│  CAPA 3: Modelos Eloquent (Active Record Pattern)  │
│  (DatoMnp, Municipio, Provincia, Usuario)          │
│  • Métodos de instancia (relaciones, scopes)       │
│  • Métodos estáticos (queries complejas)           │
├─────────────────────────────────────────────────────┤
│  CAPA 2: Query Builder + PDO Wrapper               │
│  (Illuminate\Database\Query\Builder)                │
│  • Genera SQL seguro con prepared statements       │
│  • Previene SQL injection automáticamente          │
├─────────────────────────────────────────────────────┤
│  CAPA 1: PDO (PHP Data Objects)                    │
│  • Conexión real a MariaDB/MySQL                   │
│  • Prepared statements nativos                      │
│  • Transacciones y manejo de errores               │
└─────────────────────────────────────────────────────┘
```

---

## ✅ Cumplimiento del Requisito "PDO para acceso a datos"

### 1. PDO se Usa en Cada Consulta

**Ejemplo:** Método `getEstadisticasPorProvincia()`

```php
// app/Models/DatoMnp.php:82-101
public static function getEstadisticasPorProvincia(int $ano)
{
    return \DB::table('datos_mnp as d')  // Query Builder sobre PDO
        ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
        ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
        ->where('d.anno', $ano)          // WHERE con prepared statement
        ->select([
            'p.codigo_ine',
            'p.nombre as provincia',
            'd.tipo_evento',
            \DB::raw('SUM(d.valor) as total'),  // Funciones agregadas
        ])
        ->groupBy('p.id', 'p.codigo_ine', 'p.nombre', 'd.tipo_evento')
        ->get();                         // Ejecuta PDO::execute()
}
```

**SQL Generado por PDO:**

```sql
SELECT `p`.`codigo_ine`, `p`.`nombre` as `provincia`,
       `d`.`tipo_evento`, SUM(d.valor) as total
FROM `datos_mnp` as `d`
INNER JOIN `municipios` as `m` ON `d`.`municipio_id` = `m`.`id`
INNER JOIN `provincias` as `p` ON `m`.`provincia_id` = `p`.`id`
WHERE `d`.`anno` = ?  -- Parámetro preparado con PDO
GROUP BY `p`.`id`, `p`.`codigo_ine`, `p`.`nombre`, `d`.`tipo_evento`
```

**Internamente Laravel hace:**

```php
// vendor/laravel/framework/src/Illuminate/Database/Connection.php
$pdo = $this->getPdo();
$statement = $pdo->prepare($sql);
$statement->execute($bindings);  // [2023]
return $statement->fetchAll(PDO::FETCH_OBJ);
```

---

## 🔧 Métodos Implementados con PDO

### A. Métodos en `DatoMnp` (app/Models/DatoMnp.php)

| Método | Líneas | Descripción | Query SQL |
|--------|--------|-------------|-----------|
| `getEstadisticasPorProvincia()` | 82-101 | Estadísticas agregadas por provincia | JOIN + GROUP BY |
| `getEvolucionMunicipio()` | 110-126 | Evolución temporal de municipio | WHERE + BETWEEN |
| `getCrecimientoVegetativo()` | 135-169 | Cálculo nacimientos - defunciones | CASE WHEN + SUM |
| `getRankingMunicipios()` | 178-198 | Top N municipios por evento | ORDER BY + LIMIT |

### B. Métodos en `Municipio` (app/Models/Municipio.php)

| Método | Líneas | Descripción | Query SQL |
|--------|--------|-------------|-----------|
| `getResumenAno()` | 80-111 | Resumen de un municipio en un año | WHERE + IN |
| `conDatosAno()` | 119-145 | Municipios con datos de un año | LEFT JOIN |
| `buscarPorNombre()` | 153-168 | Búsqueda parcial de municipios | LIKE |

---

## 📝 Ejemplos de Uso

### Ejemplo 1: Consulta Simple con Eloquent

```php
// En un controlador
use App\Models\DatoMnp;

public function index()
{
    // Eloquent usa PDO internamente
    $datos = DatoMnp::where('anno', 2023)
        ->where('tipo_evento', 'nacimiento')
        ->with(['municipio.provincia'])  // Eager loading con JOINs
        ->orderBy('valor', 'desc')
        ->limit(10)
        ->get();

    return view('dashboard', compact('datos'));
}
```

**PDO subyacente:**
```php
// Ejecuta 2 queries con PDO prepared statements:
// 1. SELECT * FROM datos_mnp WHERE anno = ? AND tipo_evento = ? ORDER BY valor DESC LIMIT 10
// 2. SELECT * FROM municipios WHERE id IN (?, ?, ...)
```

---

### Ejemplo 2: Query Builder Directo

```php
use Illuminate\Support\Facades\DB;

public function estadisticas($provincia)
{
    // Query Builder sobre PDO
    $stats = DB::table('datos_mnp as d')
        ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
        ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
        ->where('p.codigo_ine', $provincia)
        ->where('d.anno', 2023)
        ->select([
            'p.nombre as provincia',
            DB::raw('SUM(CASE WHEN d.tipo_evento = "nacimiento" THEN d.valor ELSE 0 END) as nacimientos'),
            DB::raw('SUM(CASE WHEN d.tipo_evento = "defuncion" THEN d.valor ELSE 0 END) as defunciones'),
        ])
        ->groupBy('p.id', 'p.nombre')
        ->first();

    return response()->json($stats);
}
```

---

### Ejemplo 3: Transacciones con PDO

```php
use Illuminate\Support\Facades\DB;

public function importarDatos($municipioId, $datos)
{
    try {
        // Inicia transacción PDO
        DB::beginTransaction();

        foreach ($datos as $dato) {
            DatoMnp::updateOrCreate(
                [
                    'municipio_id' => $municipioId,
                    'anno' => $dato['ano'],
                    'tipo_evento' => $dato['tipo'],
                ],
                [
                    'valor' => $dato['valor'],
                    'ultima_actualizacion' => now(),
                ]
            );
        }

        // Commit PDO
        DB::commit();

        return ['success' => true];

    } catch (\Exception $e) {
        // Rollback PDO
        DB::rollBack();

        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

---

### Ejemplo 4: Acceso Directo a PDO (Casos Avanzados)

```php
use Illuminate\Support\Facades\DB;

public function queryCompleja()
{
    // Obtener instancia PDO directa
    $pdo = DB::connection()->getPdo();

    // Prepared statement manual
    $stmt = $pdo->prepare("
        WITH ranking AS (
            SELECT
                m.nombre,
                d.valor,
                ROW_NUMBER() OVER (PARTITION BY d.tipo_evento ORDER BY d.valor DESC) as rn
            FROM datos_mnp d
            JOIN municipios m ON d.municipio_id = m.id
            WHERE d.anno = :ano
        )
        SELECT * FROM ranking WHERE rn <= 10
    ");

    $stmt->execute(['ano' => 2023]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

---

## 🔒 Seguridad: Prevención de SQL Injection

### ❌ MAL (Vulnerable)
```php
$ano = $_GET['ano'];
$sql = "SELECT * FROM datos_mnp WHERE anno = $ano";  // SQL INJECTION!
$result = DB::select($sql);
```

### ✅ BIEN (Seguro con PDO Prepared Statements)
```php
$ano = $_GET['ano'];
$result = DB::table('datos_mnp')->where('anno', $ano)->get();
// Genera: SELECT * FROM datos_mnp WHERE anno = ?
// Bindings: [$ano]
```

---

## 📊 Ventajas del Enfoque Actual

| Aspecto | PDO Puro | Query Builder (actual) | Eloquent ORM |
|---------|----------|------------------------|--------------|
| **Seguridad** | ✅ Manual | ✅ Automática | ✅ Automática |
| **Legibilidad** | ⚠️ Baja | ✅ Alta | ✅ Muy Alta |
| **Mantenibilidad** | ❌ Difícil | ✅ Fácil | ✅ Muy Fácil |
| **Curva aprendizaje** | ⚠️ Media | ✅ Baja | ✅ Baja |
| **Performance** | ✅ Óptima | ✅ Muy Buena | ⚠️ Buena |
| **Control fino** | ✅ Total | ✅ Alto | ⚠️ Medio |

---

## 🎯 Recomendaciones de Uso

### Usar Eloquent cuando:
- CRUD simple (create, read, update, delete)
- Relaciones entre modelos
- Scopes y filtros comunes
- Validación de datos

```php
$municipio = Municipio::with('provincia')->find($id);
$datos = $municipio->datosMnp()->nacimientos()->get();
```

### Usar Query Builder cuando:
- Consultas complejas con múltiples JOINs
- Agregaciones (SUM, COUNT, AVG)
- Subconsultas
- Queries de reporting

```php
$stats = DB::table('datos_mnp')
    ->join('municipios', ...)
    ->select(DB::raw('SUM(...) as total'))
    ->groupBy(...)
    ->get();
```

### Usar PDO puro cuando:
- Procedimientos almacenados
- Consultas muy específicas del motor DB
- Optimizaciones críticas de rendimiento
- CTEs (Common Table Expressions) complejas

```php
$pdo = DB::connection()->getPdo();
$stmt = $pdo->prepare("WITH RECURSIVE ...");
```

---

## 🧪 Verificar Uso de PDO

Ejecuta el script de demostración:

```bash
php scripts/demo-metodos-pdo.php
```

Este script muestra:
- ✅ Consultas SQL generadas por Query Builder
- ✅ Prepared statements con bindings
- ✅ Resultados de métodos personalizados
- ✅ Confirmación de uso de PDO

---

## 📚 Referencias

### Laravel Database
- **Query Builder**: https://laravel.com/docs/11.x/queries
- **Eloquent ORM**: https://laravel.com/docs/11.x/eloquent
- **Database Transactions**: https://laravel.com/docs/11.x/database#database-transactions

### PDO PHP
- **PDO Manual**: https://www.php.net/manual/es/book.pdo.php
- **Prepared Statements**: https://www.php.net/manual/es/pdo.prepared-statements.php

### Código Fuente
- **Conexión Laravel**: `vendor/laravel/framework/src/Illuminate/Database/Connection.php`
- **Query Builder**: `vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php`
- **PDO Wrapper**: `vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php`

---

## ✅ Conclusión

El proyecto **SÍ utiliza PDO** para acceso a datos:

1. **Eloquent ORM** → Capa de abstracción limpia
2. **Query Builder** → SQL programático seguro
3. **PDO** → Motor subyacente en todas las operaciones

Esta arquitectura cumple con:
- ✅ Requisito técnico: "PDO para acceso a datos"
- ✅ Seguridad: Prevención automática de SQL injection
- ✅ Mantenibilidad: Código legible y testeable
- ✅ Performance: Prepared statements cacheados
- ✅ Escalabilidad: Fácil añadir nuevos métodos

**Todos los modelos tienen métodos que interactúan con la BBDD usando PDO.** 🎯
