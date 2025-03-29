<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Cotización de Prueba</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif; /* Fuente predeterminada de Laravel */
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            color: #2d3748;
            font-size: 2.2em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .body-content {
            color: #4a5568;
            font-size: 1em;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            display: inline-block;
            background-color: #4caf50; /* Un color verde */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .saludos {
            color: #718096;
            font-size: 0.9em;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Notificación de Cotización
        </div>
        <div class="body-content">
            <p>Estimado Carlos Daniel Anaya Barrios,</p>
            <p>Se le informa que se ha generado una cotización de prueba.</p>
            <p>Para revisar los detalles o el estado de las solicitudes de viáticos pendientes, puede hacer clic en el siguiente enlace:</p>
        </div>
        <div class="button-container">
            <a href="https://modnom.ecnomina.com/solicitud_viatico" class="button">
                Ver Solicitudes Pendientes
            </a>
        </div>
        <p class="saludos">Saludos cordiales,</p>
        <p class="saludos">Equipo de Viáticos</p>
    </div>
</body>
</html>