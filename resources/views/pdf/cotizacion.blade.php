<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 15px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cotización N° {{ $cotizacion->numero_cot }}</h1>
            <p>Fecha de Solicitud: {{ $cotizacion->fecha_solicitud }}</p>
            @if($cotizacion->fecha_aprobacion_cot)
                <p>Fecha de Aprobación: {{ $cotizacion->fecha_aprobacion_cot }}</p>
            @endif
        </div>

        <div class="info">
            <p><strong>Descripción:</strong> {{ $cotizacion->descripcion_cot }}</p>
            <p><strong>Lugar:</strong> {{ $cotizacion->lugar }}</p>
            <p><strong>Tipo:</strong> {{ $cotizacion->tipo }}</p>
            <p><strong>Tipo de Mantenimiento:</strong> {{ $cotizacion->tipo_de_mantenimiento }}</p>
            <p><strong>Estado del Cliente:</strong> {{ $cotizacion->estado_cliente }}</p>
            <p><strong>Estado:</strong> {{ $cotizacion->estado }}</p>
            <p><strong>Mes del Pedido:</strong> {{ $cotizacion->mes_pedido }}</p>
            <p><strong>Centro de Costo ID:</strong> {{ $cotizacion->centro_costo_id }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Por A</th>
                    <th>Por I</th>
                    <th>Por U</th>
                    <th>Admi</th>
                    <th>Impr</th>
                    <th>Util</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $cotizacion->por_a }}</td>
                    <td>{{ $cotizacion->por_i }}</td>
                    <td>{{ $cotizacion->por_u }}</td>
                    <td>{{ $cotizacion->admi }}</td>
                    <td>{{ $cotizacion->impr }}</td>
                    <td>{{ $cotizacion->util }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <tbody>
                <tr>
                    <th>Subtotal</th>
                    <td class="total">{{ $cotizacion->subtotal }}</td>
                </tr>
                <tr>
                    <th>IVA</th>
                    <td class="total">{{ $cotizacion->iva }}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td class="total">{{ $cotizacion->total }}</td>
                </tr>
            </tbody>
        </table>

        @if($cotizacion->factura)
            <div class="info">
                <p><strong>Factura N°:</strong> {{ $cotizacion->factura }}</p>
                @if($cotizacion->fecha_factura)
                    <p><strong>Fecha de Factura:</strong> {{ $cotizacion->fecha_factura }}</p>
                @endif
            </div>
        @endif
    </div>
</body>
</html>