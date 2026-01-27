<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Datos de la Base de Datos</title>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif; margin: 2rem; background-color: #f9fafb; color: #1f2937; }
        h1, h2 { border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
        .municipio-container { background-color: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 1.5rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        thead th { background-color: #f3f4f6; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        p { color: #6b7280; }
    </style>
</head>
<body>

    <h1>📊 Vista de Test de la Base de Datos</h1>

    @if($municipios->isEmpty())
        <p>No se encontraron municipios. Asegúrate de haber ejecutado los seeders: <code>php artisan db:seed</code></p>
    @else
        <h2>Listado de los primeros 10 municipios y 5 de sus datos MNP:</h2>

        @foreach($municipios as $municipio)
            <div class="municipio-container">
                <h3>{{ $municipio->nombre }} (Provincia: {{ $municipio->provincia->nombre ?? 'N/A' }})</h3>

                @if($municipio->datosMnp->isEmpty())
                    <p>No se encontraron datos MNP para este municipio.</p>
                @else
                    <table>
                        <thead>
                            <tr><th>Año</th><th>Tipo Evento</th><th>Valor</th></tr>
                        </thead>
                        <tbody>
                            @foreach($municipio->datosMnp as $dato)
                                <tr><td>{{ $dato->anno }}</td><td>{{ $dato->tipo_evento }}</td><td>{{ $dato->valor }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    @endif

</body>
</html>
