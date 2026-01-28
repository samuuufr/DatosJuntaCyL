# Arquitectura PDO - Proyecto DAW

## Resumen
Laravel Eloquent usa **PDO internamente** para todas las operaciones de BD. Cumple requisito "PDO para acceso a datos".

## Capas
```
Controladores → Eloquent ORM → Query Builder → PDO → MariaDB
```

## Ejemplo: Query Builder con PDO
```php
// DatoMnp::getEstadisticasPorProvincia(2023)
DB::table('datos_mnp as d')
    ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
    ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
    ->where('d.anno', $ano)  // Prepared statement automático
    ->select(['p.nombre', DB::raw('SUM(d.valor) as total')])
    ->groupBy('p.id', 'p.nombre')
    ->get();

// Laravel internamente ejecuta:
// $pdo->prepare($sql)->execute([$ano]);
```

## Métodos PDO en Modelos

### DatoMnp
| Método | Query |
|--------|-------|
| `getEstadisticasPorProvincia($ano)` | JOIN + GROUP BY |
| `getEvolucionMunicipio($id, $tipo)` | WHERE + BETWEEN |
| `getCrecimientoVegetativo($ano)` | CASE WHEN + SUM |
| `getRankingMunicipios($tipo, $limit)` | ORDER BY + LIMIT |

### Municipio
| Método | Query |
|--------|-------|
| `getResumenAno($ano)` | WHERE + IN |
| `conDatosAno($ano)` | LEFT JOIN |
| `buscarPorNombre($nombre)` | LIKE |

## Transacciones
```php
DB::beginTransaction();
try {
    DatoMnp::updateOrCreate([...], [...]);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
}
```

## Seguridad SQL Injection
```php
// ❌ Vulnerable
DB::select("SELECT * FROM datos WHERE anno = $ano");

// ✅ Seguro (PDO prepared statements)
DB::table('datos')->where('anno', $ano)->get();
```

## PDO Directo (casos avanzados)
```php
$pdo = DB::connection()->getPdo();
$stmt = $pdo->prepare("WITH RECURSIVE...");
$stmt->execute(['ano' => 2023]);
```

## Conclusión
✅ **PDO se usa en todas las operaciones** vía Eloquent/Query Builder
✅ Prepared statements automáticos
✅ Prevención SQL injection
✅ Transacciones soportadas
