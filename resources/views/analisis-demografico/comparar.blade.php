@extends('diseños.panel')

@section('titulo_pagina', 'Comparar Provincias')
@section('descripcion_pagina', 'Compara datos demográficos entre dos provincias')

@section('contenido')

<!-- SECCIÓN: SELECTOR DE PROVINCIAS -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div>
            <h2 class="card-title"> Comparativa de Provincias</h2>
            <p class="card-subtitle">Selecciona dos provincias para compararlas</p>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('analisis-demografico.comparar') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: flex-end;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Provincia A
                </label>
                <select name="provincia_a" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem; background-color: var(--bg-primary); color: var(--text-primary);">
                    <option value="">-- Seleccionar --</option>
                    @foreach($provincias as $prov)
                        <option value="{{ $prov->id }}" @if(request('provincia_a') == $prov->id) selected @endif>
                            {{ $prov->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Provincia B
                </label>
                <select name="provincia_b" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem; background-color: var(--bg-primary); color: var(--text-primary);">
                    <option value="">-- Seleccionar --</option>
                    @foreach($provincias as $prov)
                        <option value="{{ $prov->id }}" @if(request('provincia_b') == $prov->id) selected @endif>
                            {{ $prov->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Comparar →</button>
        </form>
    </div>
</div>

<!-- MOSTRAR COMPARATIVA SI HAY DATOS -->
@if($comparativa)
    <div class="grid grid-2" style="margin-bottom: 2rem;">
        <!-- PROVINCIA A -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">{{ $comparativa['provincia_a']['nombre'] }}</h2>
                    <p class="card-subtitle">Datos demográficos</p>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="stat-box">
                        <div class="stat-box-value">{{ $comparativa['provincia_a']['municipios'] }}</div>
                        <div class="stat-box-label">Municipios</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #10b981;">
                            {{ number_format($comparativa['provincia_a']['nacimientos'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Nacimientos</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #f59e0b;">
                            {{ number_format($comparativa['provincia_a']['matrimonios'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Matrimonios</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #ef4444;">
                            {{ number_format($comparativa['provincia_a']['defunciones'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Defunciones</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROVINCIA B -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">{{ $comparativa['provincia_b']['nombre'] }}</h2>
                    <p class="card-subtitle">Datos demográficos</p>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="stat-box">
                        <div class="stat-box-value">{{ $comparativa['provincia_b']['municipios'] }}</div>
                        <div class="stat-box-label">Municipios</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #10b981;">
                            {{ number_format($comparativa['provincia_b']['nacimientos'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Nacimientos</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #f59e0b;">
                            {{ number_format($comparativa['provincia_b']['matrimonios'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Matrimonios</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value" style="color: #ef4444;">
                            {{ number_format($comparativa['provincia_b']['defunciones'], 0, ',', '.') }}
                        </div>
                        <div class="stat-box-label">Defunciones</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICO COMPARATIVO -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <div>
                <h2 class="card-title">Gráfico Comparativo</h2>
                <p class="card-subtitle">Visualización de datos demográficos</p>
            </div>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 400px;">
                <canvas id="graficoComparativa"></canvas>
            </div>
        </div>
    </div>

    <!-- TABLA COMPARATIVA DETALLADA -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title"> Comparativa Detallada</h2>
                <p class="card-subtitle">Análisis lado a lado</p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Métrica</th>
                            <th style="text-align: center;">{{ $comparativa['provincia_a']['nombre'] }}</th>
                            <th style="text-align: center;">{{ $comparativa['provincia_b']['nombre'] }}</th>
                            <th style="text-align: center;">Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Municipios</strong></td>
                            <td style="text-align: center;">{{ $comparativa['provincia_a']['municipios'] }}</td>
                            <td style="text-align: center;">{{ $comparativa['provincia_b']['municipios'] }}</td>
                            <td style="text-align: center;">
                                @php
                                    $diff = $comparativa['provincia_a']['municipios'] - $comparativa['provincia_b']['municipios'];
                                    $class = $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : '');
                                @endphp
                                <span class="stat-box-change {{ $class }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nacimientos</strong></td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_a']['nacimientos'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_b']['nacimientos'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                @php
                                    $diff = $comparativa['provincia_a']['nacimientos'] - $comparativa['provincia_b']['nacimientos'];
                                    $class = $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : '');
                                @endphp
                                <span class="stat-box-change {{ $class }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Matrimonios</strong></td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_a']['matrimonios'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_b']['matrimonios'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                @php
                                    $diff = $comparativa['provincia_a']['matrimonios'] - $comparativa['provincia_b']['matrimonios'];
                                    $class = $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : '');
                                @endphp
                                <span class="stat-box-change {{ $class }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Defunciones</strong></td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_a']['defunciones'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($comparativa['provincia_b']['defunciones'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                @php
                                    $diff = $comparativa['provincia_a']['defunciones'] - $comparativa['provincia_b']['defunciones'];
                                    $class = $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : '');
                                @endphp
                                <span class="stat-box-change {{ $class }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="color: var(--text-secondary); font-size: 1rem;">
             Selecciona dos provincias arriba para ver la comparativa
        </p>
    </div>
@endif

@endsection

@section('js_adicional')
@if($comparativa)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoComparativa').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Nacimientos', 'Matrimonios', 'Defunciones'],
            datasets: [
                {
                    label: '{{ $comparativa["provincia_a"]["nombre"] }}',
                    data: [
                        {{ $comparativa['provincia_a']['nacimientos'] }},
                        {{ $comparativa['provincia_a']['matrimonios'] }},
                        {{ $comparativa['provincia_a']['defunciones'] }}
                    ],
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1
                },
                {
                    label: '{{ $comparativa["provincia_b"]["nombre"] }}',
                    data: [
                        {{ $comparativa['provincia_b']['nacimientos'] }},
                        {{ $comparativa['provincia_b']['matrimonios'] }},
                        {{ $comparativa['provincia_b']['defunciones'] }}
                    ],
                    backgroundColor: 'rgba(236, 72, 153, 0.8)',
                    borderColor: 'rgba(236, 72, 153, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || '#374151',
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString('es-ES');
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || '#6b7280'
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || '#e5e7eb'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || '#6b7280',
                        callback: function(value) {
                            return value.toLocaleString('es-ES');
                        }
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || '#e5e7eb'
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection
