@extends('diseños.panel')

@section('titulo', 'Mis Municipios Favoritos')

@section('contenido')
<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold" style="color: var(--text-primary);">Mis Municipios Favoritos</h1>
        <a
            href="{{ route('perfil.mostrar') }}"
            class="btn btn-secondary"
            aria-label="Volver a mi perfil"
        >
            <span aria-hidden="true">←</span> Volver al Perfil
        </a>
    </div>

    @if ($favoritos->isEmpty())
        <div class="card text-center" role="status">
            <p class="text-lg mb-2" style="color: var(--text-primary);">Aún no tienes municipios favoritos</p>
            <p class="text-sm" style="color: var(--text-secondary);">Explora los municipios y añade tus favoritos haciendo clic en el botón de favorito</p>
            <div class="mt-4">
                <a
                    href="{{ route('analisis-demografico.mapa-calor') }}"
                    class="btn btn-primary"
                    aria-label="Explorar municipios disponibles"
                >
                    Explorar Municipios
                </a>
            </div>
        </div>
    @else
        <div class="card" style="padding: 0;margin-top: 2rem;">
            <div class="table-wrapper" style="border: none;" role="region" aria-label="Lista de municipios favoritos" tabindex="0">
                <table>
                    <caption class="sr-only">Municipios guardados como favoritos</caption>
                    <thead>
                        <tr>
                            <th scope="col">Municipio</th>
                            <th scope="col">Provincia</th>
                            <th scope="col">Población</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($favoritos as $municipio)
                            <tr>
                                <th scope="row">
                                    <div class="font-medium" style="color: var(--text-primary);">
                                        {{ $municipio->nombre }}
                                    </div>
                                    <div class="text-sm" style="color: var(--text-secondary);">
                                        Código INE: {{ $municipio->codigo_ine }}
                                    </div>
                                </th>
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
                                            aria-label="Ver detalles de {{ $municipio->nombre }}"
                                        >
                                            Ver Detalles
                                        </a>
                                        <button
                                            onclick="eliminarFavorito({{ $municipio->id }}, '{{ $municipio->nombre }}')"
                                            class="hover:opacity-80"
                                            style="color: #ef4444; background: none; border: none; cursor: pointer; font-weight: 500;"
                                            aria-label="Eliminar {{ $municipio->nombre }} de favoritos"
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

<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
a:focus-visible, button:focus-visible, [tabindex]:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}
.table-wrapper:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: -3px;
}

/* ========================================
   RESPONSIVE - FAVORITOS
   ======================================== */
@media (max-width: 768px) {
    .max-w-6xl {
        padding: 0 1rem;
    }

    .flex.justify-between {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }

    .text-3xl {
        font-size: 1.75rem !important;
    }

    .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        min-width: 500px;
    }
}

@media (max-width: 576px) {
    .max-w-6xl {
        padding: 0 0.5rem;
    }

    .text-3xl {
        font-size: 1.5rem !important;
    }

    .mt-8 {
        margin-top: 1.5rem !important;
    }

    .mb-8 {
        margin-bottom: 1.5rem !important;
    }

    table {
        font-size: 0.85rem;
    }

    th, td {
        padding: 0.6rem 0.5rem;
    }

    /* Ocultar columna población en móvil */
    table th:nth-child(3),
    table td:nth-child(3) {
        display: none;
    }
}
</style>
@endsection
