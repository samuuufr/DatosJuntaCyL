# Modelos Eloquent - Guía Rápida

## Modelos
- **Usuario** - Auth + favoritos
- **Provincia** - Catálogo 9 provincias CyL
- **Municipio** - ~2200 municipios
- **DatoMnp** - Nacimientos/defunciones/matrimonios
- **Favorito** - Pivote usuario-municipio

---

## Usuario
```php
// Fillable: email, password, nombre, rol, fecha_registro

// Relaciones
$usuario->favoritos;           // Collection<Favorito>
$usuario->municipiosFavoritos; // Collection<Municipio> (N:M)

// Favoritos
$usuario->municipiosFavoritos()->attach($municipioId);
$usuario->municipiosFavoritos()->detach($municipioId);
$esFavorito = $usuario->municipiosFavoritos()->where('municipio_id', $id)->exists();
```

## Provincia
```php
// Fillable: codigo_ine, nombre

// Relaciones
$provincia->municipios;  // Collection<Municipio>
$provincia->datosMnp;    // HasManyThrough → Collection<DatoMnp>

// Ejemplos
$provincia = Provincia::with('municipios')->find(1);
$leon = Provincia::where('codigo_ine', '24')->first();
$provincias = Provincia::withCount('municipios')->get();
```

## Municipio
```php
// Fillable: provincia_id, codigo_ine, nombre

// Relaciones
$municipio->provincia;    // Provincia
$municipio->datosMnp;     // Collection<DatoMnp>
$municipio->nacimientos;  // Filtrado tipo_evento='nacimiento'
$municipio->defunciones;  // Filtrado tipo_evento='defuncion'
$municipio->matrimonios;  // Filtrado tipo_evento='matrimonio'

// Ejemplos
$municipio = Municipio::with('provincia')->find(1);
$resultados = Municipio::where('nombre', 'like', '%León%')->get();
```

## DatoMnp
```php
// Fillable: municipio_id, anno, tipo_evento, valor, ultima_actualizacion
// tipo_evento: ENUM('nacimiento', 'defuncion', 'matrimonio')

// Relaciones
$dato->municipio;  // Municipio

// Scopes
DatoMnp::tipoEvento('nacimiento')->get();
DatoMnp::anno(2023)->get();
DatoMnp::nacimientos()->get();
DatoMnp::defunciones()->get();

// Ejemplos
$total = DatoMnp::nacimientos()->anno(2023)->sum('valor');
$evolucion = DatoMnp::where('municipio_id', 1)
    ->where('tipo_evento', 'nacimiento')
    ->orderBy('anno')
    ->get(['anno', 'valor']);
```

## Favorito
```php
// Fillable: usuario_id, municipio_id

// Relaciones
$favorito->usuario;    // Usuario
$favorito->municipio;  // Municipio

// Ejemplos
Favorito::create(['usuario_id' => 1, 'municipio_id' => 5]);
Favorito::where('usuario_id', 1)->where('municipio_id', 5)->delete();
$existe = Favorito::where('usuario_id', 1)->where('municipio_id', 5)->exists();
```

---

## Consultas Comunes

### Indicadores por Provincia
```php
$nacimientos = $provincia->datosMnp()
    ->where('tipo_evento', 'nacimiento')
    ->where('anno', 2023)
    ->sum('valor');
$defunciones = $provincia->datosMnp()
    ->where('tipo_evento', 'defuncion')
    ->where('anno', 2023)
    ->sum('valor');
$crecimiento = $nacimientos - $defunciones;
```

### Ranking Municipios
```php
$ranking = Municipio::select('municipios.*')
    ->join('datos_mnp', 'municipios.id', '=', 'datos_mnp.municipio_id')
    ->where('datos_mnp.anno', 2023)
    ->where('datos_mnp.tipo_evento', 'nacimiento')
    ->groupBy('municipios.id')
    ->orderByDesc(DB::raw('SUM(datos_mnp.valor)'))
    ->limit(20)
    ->with('provincia')
    ->get();
```

### Dashboard Usuario
```php
$usuario = Usuario::with([
    'municipiosFavoritos.provincia',
    'municipiosFavoritos.datosMnp' => fn($q) => $q->where('anno', 2023)
])->find($usuarioId);
```

---

## Buenas Prácticas

### Eager Loading (evitar N+1)
```php
// ❌ Mal
$municipios = Municipio::all();
foreach ($municipios as $m) echo $m->provincia->nombre; // N queries

// ✅ Bien
$municipios = Municipio::with('provincia')->get(); // 2 queries
```

### Transacciones
```php
DB::beginTransaction();
try {
    $usuario = Usuario::create([...]);
    $usuario->municipiosFavoritos()->attach($municipioId);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
}
```
