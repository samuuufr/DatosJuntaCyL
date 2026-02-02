@extends('diseños.panel')

@section('titulo', 'Mis Municipios Favoritos')

@section('contenido')
<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold" style="color: var(--text-primary);">Mis Municipios Favoritos</h1>
        <a
            href="{{ route('perfil.mostrar') }}"
            class="btn btn-secondary"
        >
            Volver al Perfil
        </a>
    </div>

    @if ($favoritos->isEmpty())
        <div class="card text-center">
            <p class="text-lg mb-2" style="color: var(--text-primary);">Aún no tienes municipios favoritos</p>
            <p class="text-sm" style="color: var(--text-secondary);">Explora los municipios y añade tus favoritos haciendo clic en el botón de favorito</p>
            <div class="mt-4">
                <a
                    href="{{ route('municipios.index') }}"
                    class="btn btn-primary"
                >
                    Explorar Municipios
                </a>
            </div>
        </div>
    @else
        <div class="card" style="padding: 0;">
            <div class="table-wrapper" style="border: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Municipio</th>
                            <th>Provincia</th>
                            <th>Población</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($favoritos as $municipio)
                            <tr>
                                <td>
                                    <div class="font-medium" style="color: var(--text-primary);">
                                        {{ $municipio->nombre }}
                                    </div>
                                    <div class="text-sm" style="color: var(--text-secondary);">
                                        Código INE: {{ $municipio->codigo_ine }}
                                    </div>
                                </td>
                                <td>
                                    <span style="color: var(--text-primary);">
                                        {{ $municipio->provincia->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: var(--text-primary);">
                                        @if($municipio->poblacion)
                                            {{ number_format($municipio->poblacion, 0, ',', '.') }} hab.
                                        @else
                                            <span style="color: var(--text-secondary); font-style: italic;">Sin datos</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <a
                                            href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}"
                                            class="text-blue-600 hover:text-blue-800"
                                            style="color: var(--primary-color);"
                                        >
                                            Ver Detalles
                                        </a>
                                        <button
                                            onclick="eliminarFavorito({{ $municipio->id }}, '{{ $municipio->nombre }}')"
                                            class="hover:opacity-80"
                                            style="color: #ef4444; background: none; border: none; cursor: pointer; font-weight: 500;"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 1rem 1.5rem; background-color: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <p class="text-sm" style="color: var(--text-secondary);">
                    Total de favoritos: <span class="font-semibold" style="color: var(--text-primary);">{{ $favoritos->count() }}</span>
                </p>
            </div>
        </div>
    @endif
</div>

<script>
async function eliminarFavorito(municipioId, nombreMunicipio) {
    if (!confirm(`¿Estás seguro de eliminar "${nombreMunicipio}" de tus favoritos?`)) {
        return;
    }

    try {
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        const response = await fetch(`${baseUrl}/api/perfil/favoritos/eliminar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                municipio_id: municipioId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Recargar la página para actualizar la lista
            location.reload();
        } else {
            alert(data.message || 'Error al eliminar el favorito');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el favorito');
    }
}
</script>
@endsection
