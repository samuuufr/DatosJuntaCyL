@extends('diseños.panel')

@section('titulo', 'Mis Municipios Favoritos')

@section('contenido')
<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mis Municipios Favoritos</h1>
        <a
            href="{{ route('perfil.mostrar') }}"
            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
        >
            Volver al Perfil
        </a>
    </div>

    @if ($favoritos->isEmpty())
        <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-300 px-6 py-4 rounded-lg text-center">
            <p class="text-lg mb-2">Aún no tienes municipios favoritos</p>
            <p class="text-sm">Explora los municipios y añade tus favoritos haciendo clic en el botón de favorito</p>
            <div class="mt-4">
                <a
                    href="{{ route('municipios.index') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
                >
                    Explorar Municipios
                </a>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Municipio
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Provincia
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Población
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($favoritos as $municipio)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $municipio->nombre }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Código INE: {{ $municipio->codigo_ine }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $municipio->provincia->nombre }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ number_format($municipio->poblacion ?? 0, 0, ',', '.') }} hab.
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a
                                        href="{{ route('analisis-demografico.municipio-detalle', $municipio->id) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        Ver Detalles
                                    </a>
                                    <button
                                        onclick="eliminarFavorito({{ $municipio->id }}, '{{ $municipio->nombre }}')"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Total de favoritos: <span class="font-semibold">{{ $favoritos->count() }}</span>
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
