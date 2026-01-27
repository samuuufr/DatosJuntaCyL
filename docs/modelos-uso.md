# Guía de Uso de Modelos Eloquent

## Modelos Creados

- **Usuario** - Gestión de usuarios con autenticación
- **Provincia** - Catálogo de provincias de Castilla y León
- **Municipio** - Catálogo de municipios por provincia
- **DatoMnp** - Datos del Movimiento Natural de Población
- **Favorito** - Tabla pivote entre usuarios y municipios

---

## Usuario

**Ubicación:** [app/Models/Usuario.php](../app/Models/Usuario.php)

### Propiedades Fillable
```php
'email', 'password', 'nombre', 'rol', 'fecha_registro'
```

### Relaciones

#### Favoritos (1:N)
```php
$usuario = Usuario::find(1);
$favoritos = $usuario->favoritos; // Colección de Favorito
```

#### Municipios Favoritos (N:M)
```php
$usuario = Usuario::find(1);
$municipios = $usuario->municipiosFavoritos; // Colección de Municipio

// Agregar favorito
$usuario->municipiosFavoritos()->attach($municipioId);

// Eliminar favorito
$usuario->municipiosFavoritos()->detach($municipioId);

// Verificar si es favorito
$esFavorito = $usuario->municipiosFavoritos()->where('municipio_id', $municipioId)->exists();
```

### Ejemplos de Uso

```php
// Crear usuario
$usuario = Usuario::create([
    'email' => 'usuario@ejemplo.com',
    'password' => bcrypt('password123'),
    'nombre' => 'Juan Pérez',
    'rol' => 'usuario',
]);

// Autenticación
$usuario = Usuario::where('email', 'usuario@ejemplo.com')->first();
if ($usuario && password_verify('password123', $usuario->password)) {
    // Login exitoso
}

// Obtener todos los favoritos de un usuario con datos
$favoritos = Usuario::find(1)
    ->municipiosFavoritos()
    ->with('provincia', 'datosMnp')
    ->get();
```

---

## Provincia

**Ubicación:** [app/Models/Provincia.php](../app/Models/Provincia.php)

### Propiedades Fillable
```php
'codigo_ine', 'nombre'
```

### Relaciones

#### Municipios (1:N)
```php
$provincia = Provincia::find(1);
$municipios = $provincia->municipios; // Colección de Municipio
```

#### Datos MNP (HasManyThrough)
```php
$provincia = Provincia::find(1);
$datos = $provincia->datosMnp; // Todos los datos MNP de todos los municipios
```

### Ejemplos de Uso

```php
// Crear provincia
Provincia::create([
    'codigo_ine' => '24',
    'nombre' => 'León',
]);

// Obtener provincia con sus municipios
$provincia = Provincia::with('municipios')->find(1);

// Obtener datos agregados de una provincia
$provincia = Provincia::find(1);
$totalNacimientos = $provincia->datosMnp()
    ->where('tipo_evento', 'nacimiento')
    ->where('anno', 2023)
    ->sum('valor');

// Buscar provincia por código INE
$leon = Provincia::where('codigo_ine', '24')->first();

// Listar todas las provincias con conteo de municipios
$provincias = Provincia::withCount('municipios')->get();
```

---

## Municipio

**Ubicación:** [app/Models/Municipio.php](../app/Models/Municipio.php)

### Propiedades Fillable
```php
'provincia_id', 'codigo_ine', 'nombre'
```

### Relaciones

#### Provincia (N:1)
```php
$municipio = Municipio::find(1);
$provincia = $municipio->provincia; // Instancia de Provincia
```

#### Datos MNP (1:N)
```php
$municipio = Municipio::find(1);
$datos = $municipio->datosMnp; // Colección de DatoMnp
$nacimientos = $municipio->nacimientos; // Solo nacimientos
$defunciones = $municipio->defunciones; // Solo defunciones
$matrimonios = $municipio->matrimonios; // Solo matrimonios
```

#### Favoritos (1:N)
```php
$municipio = Municipio::find(1);
$favoritos = $municipio->favoritos; // Cuántos usuarios lo tienen como favorito
```

### Ejemplos de Uso

```php
// Crear municipio
Municipio::create([
    'provincia_id' => 1,
    'codigo_ine' => '24089',
    'nombre' => 'León',
]);

// Obtener municipio con provincia
$municipio = Municipio::with('provincia')->find(1);
echo $municipio->nombre . ' - ' . $municipio->provincia->nombre;

// Obtener datos demográficos de un municipio para un año
$municipio = Municipio::find(1);
$datos2023 = $municipio->datosMnp()
    ->where('anno', 2023)
    ->get();

// Nacimientos por año
$nacimientos = $municipio->nacimientos()
    ->where('anno', 2023)
    ->sum('valor');

// Municipios de una provincia ordenados por nombre
$municipios = Municipio::where('provincia_id', 1)
    ->orderBy('nombre')
    ->get();

// Buscar municipio por nombre (like)
$resultados = Municipio::where('nombre', 'like', '%León%')
    ->with('provincia')
    ->get();

// Top 10 municipios con más nacimientos en 2023
$top = Municipio::with('provincia')
    ->withSum(['nacimientos as total_nacimientos' => function($query) {
        $query->where('anno', 2023);
    }], 'valor')
    ->orderByDesc('total_nacimientos')
    ->limit(10)
    ->get();
```

---

## DatoMnp

**Ubicación:** [app/Models/DatoMnp.php](../app/Models/DatoMnp.php)

### Propiedades Fillable
```php
'municipio_id', 'anno', 'tipo_evento', 'valor', 'ultima_actualizacion'
```

### Tipos de Evento (ENUM)
- `nacimiento`
- `defuncion`
- `matrimonio`

### Relaciones

#### Municipio (N:1)
```php
$dato = DatoMnp::find(1);
$municipio = $dato->municipio; // Instancia de Municipio
```

### Scopes Disponibles

```php
// Filtrar por tipo de evento
DatoMnp::tipoEvento('nacimiento')->get();

// Filtrar por año
DatoMnp::anno(2023)->get();

// Scopes específicos
DatoMnp::nacimientos()->get();
DatoMnp::defunciones()->get();
DatoMnp::matrimonios()->get();
```

### Ejemplos de Uso

```php
// Crear dato MNP
DatoMnp::create([
    'municipio_id' => 1,
    'anno' => 2023,
    'tipo_evento' => 'nacimiento',
    'valor' => 150,
]);

// Obtener nacimientos de todos los municipios en 2023
$nacimientos = DatoMnp::nacimientos()
    ->anno(2023)
    ->with('municipio.provincia')
    ->get();

// Calcular total de nacimientos en CyL en 2023
$total = DatoMnp::nacimientos()
    ->anno(2023)
    ->sum('valor');

// Evolución temporal de un municipio
$evolucion = DatoMnp::where('municipio_id', 1)
    ->where('tipo_evento', 'nacimiento')
    ->orderBy('anno')
    ->get(['anno', 'valor']);

// Comparar nacimientos vs defunciones en 2023
$crecimiento = DatoMnp::where('municipio_id', 1)
    ->where('anno', 2023)
    ->selectRaw('tipo_evento, SUM(valor) as total')
    ->groupBy('tipo_evento')
    ->get();

// Datos agregados por provincia y año
$agregado = DatoMnp::join('municipios', 'datos_mnp.municipio_id', '=', 'municipios.id')
    ->join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
    ->where('datos_mnp.anno', 2023)
    ->where('datos_mnp.tipo_evento', 'nacimiento')
    ->selectRaw('provincias.nombre, SUM(datos_mnp.valor) as total')
    ->groupBy('provincias.id', 'provincias.nombre')
    ->get();
```

---

## Favorito

**Ubicación:** [app/Models/Favorito.php](../app/Models/Favorito.php)

### Propiedades Fillable
```php
'usuario_id', 'municipio_id'
```

### Relaciones

#### Usuario (N:1)
```php
$favorito = Favorito::find(1);
$usuario = $favorito->usuario;
```

#### Municipio (N:1)
```php
$favorito = Favorito::find(1);
$municipio = $favorito->municipio;
```

### Ejemplos de Uso

```php
// Agregar favorito
Favorito::create([
    'usuario_id' => 1,
    'municipio_id' => 5,
]);

// Eliminar favorito
Favorito::where('usuario_id', 1)
    ->where('municipio_id', 5)
    ->delete();

// Listar favoritos de un usuario con información completa
$favoritos = Favorito::where('usuario_id', 1)
    ->with('municipio.provincia')
    ->get();

// Verificar si existe favorito
$existe = Favorito::where('usuario_id', 1)
    ->where('municipio_id', 5)
    ->exists();

// Contar cuántos usuarios tienen un municipio como favorito
$popularidad = Favorito::where('municipio_id', 5)->count();
```

---

## Consultas Avanzadas

### Indicadores Demográficos por Provincia

```php
use Illuminate\Support\Facades\DB;

$indicadores = Provincia::with('municipios')
    ->get()
    ->map(function($provincia) {
        $anno = 2023;

        $nacimientos = $provincia->datosMnp()
            ->where('tipo_evento', 'nacimiento')
            ->where('anno', $anno)
            ->sum('valor');

        $defunciones = $provincia->datosMnp()
            ->where('tipo_evento', 'defuncion')
            ->where('anno', $anno)
            ->sum('valor');

        return [
            'provincia' => $provincia->nombre,
            'nacimientos' => $nacimientos,
            'defunciones' => $defunciones,
            'crecimiento_vegetativo' => $nacimientos - $defunciones,
        ];
    });
```

### Dashboard de Usuario

```php
$usuario = Usuario::with([
    'municipiosFavoritos.provincia',
    'municipiosFavoritos.datosMnp' => function($query) {
        $query->where('anno', 2023);
    }
])->find($usuarioId);

foreach ($usuario->municipiosFavoritos as $municipio) {
    echo $municipio->nombre . ' (' . $municipio->provincia->nombre . ')';

    $nacimientos = $municipio->datosMnp->where('tipo_evento', 'nacimiento')->sum('valor');
    $defunciones = $municipio->datosMnp->where('tipo_evento', 'defuncion')->sum('valor');

    echo "Nacimientos: $nacimientos, Defunciones: $defunciones";
}
```

### Ranking de Municipios

```php
// Top municipios por nacimientos en 2023
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

---

## Buenas Prácticas

### Eager Loading

Evita el problema N+1 cargando relaciones de antemano:

```php
// Mal - N+1 queries
$municipios = Municipio::all();
foreach ($municipios as $municipio) {
    echo $municipio->provincia->nombre; // Query por cada municipio
}

// Bien - 2 queries
$municipios = Municipio::with('provincia')->get();
foreach ($municipios as $municipio) {
    echo $municipio->provincia->nombre;
}
```

### Validación antes de Insert

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'email' => 'required|email|unique:usuarios,email',
    'password' => 'required|min:8',
    'nombre' => 'required|string|max:255',
]);

if ($validator->fails()) {
    return response()->json($validator->errors(), 422);
}

$usuario = Usuario::create([
    'email' => $request->email,
    'password' => bcrypt($request->password),
    'nombre' => $request->nombre,
]);
```

### Transacciones

```php
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    $usuario = Usuario::create([...]);
    $usuario->municipiosFavoritos()->attach($municipioId);

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Manejar error
}
```

---

## Testing

```php
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;

class UsuarioTest extends TestCase
{
    public function test_usuario_puede_agregar_favorito()
    {
        $usuario = Usuario::factory()->create();
        $municipio = Municipio::factory()->create();

        $usuario->municipiosFavoritos()->attach($municipio->id);

        $this->assertTrue(
            $usuario->municipiosFavoritos()->where('municipio_id', $municipio->id)->exists()
        );
    }
}
```

---

**Fecha:** 27 enero 2026
**Proyecto:** Demografía CyL - DAW
