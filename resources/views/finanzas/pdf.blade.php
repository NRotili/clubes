<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        .subtitulo { font-size: 11px; color: #64748b; margin: 0 0 14px; }
        .meta { font-size: 9px; color: #94a3b8; margin-bottom: 14px; }

        table.resumen { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.resumen td { padding: 6px 10px; border: 1px solid #cbd5e1; }
        table.resumen td.label { color: #64748b; width: 33%; }
        table.resumen td.valor { font-weight: bold; text-align: right; }

        h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.03em; color: #334155; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin: 18px 0 6px; }

        table.detalle { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.detalle th { text-align: left; font-size: 9px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #cbd5e1; padding: 4px 6px; }
        table.detalle td { font-size: 10px; padding: 4px 6px; border-bottom: 1px solid #f1f5f9; }
        table.detalle td.monto, table.detalle th.monto { text-align: right; }
        table.detalle tfoot td { font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: none; }
        .vacio { color: #94a3b8; padding: 8px 6px; font-style: italic; }
        .ingreso { color: #15803d; }
        .egreso { color: #b91c1c; }
    </style>
</head>
<body>

    <h1>{{ \App\Models\ClubConfig::nombre() }}</h1>
    <p class="subtitulo">Movimientos financieros — {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</p>
    <p class="meta">Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</p>

    <table class="resumen">
        <tr>
            <td class="label">Saldo al {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}</td>
            <td class="valor">${{ number_format($saldoInicio, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total ingresos del período</td>
            <td class="valor ingreso">${{ number_format($totalIngresos, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total egresos del período</td>
            <td class="valor egreso">${{ number_format($totalEgresos, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Saldo al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</td>
            <td class="valor">${{ number_format($saldoFin, 2, ',', '.') }}</td>
        </tr>
    </table>

    <h2>Ingresos</h2>
    @if($pagos->isEmpty())
        <p class="vacio">Sin movimientos registrados en este rango.</p>
    @else
        <table class="detalle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Socio</th>
                    <th>Método de pago</th>
                    <th class="monto">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                    <tr>
                        <td>{{ $pago->fecha->format('d/m/Y') }}</td>
                        <td>{{ $pago->socio?->nombreCompleto() ?? '—' }}</td>
                        <td>{{ \App\Models\Pago::etiquetaMetodo($pago->metodo_pago) }}</td>
                        <td class="monto">${{ number_format($pago->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total ingresos</td>
                    <td class="monto">${{ number_format($totalIngresos, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <h2>Egresos</h2>
    @if($egresos->isEmpty())
        <p class="vacio">Sin movimientos registrados en este rango.</p>
    @else
        <table class="detalle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th class="monto">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($egresos as $egreso)
                    <tr>
                        <td>{{ $egreso->fecha->format('d/m/Y') }}</td>
                        <td>{{ $egreso->descripcion }}</td>
                        <td>{{ \App\Models\Egreso::etiquetaCategoria($egreso->categoria) }}</td>
                        <td class="monto">${{ number_format($egreso->monto, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total egresos</td>
                    <td class="monto">${{ number_format($totalEgresos, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

</body>
</html>
