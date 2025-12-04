<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f3f4f6;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #8b5cf6;
            /* Purple-500 */
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px 20px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 20px;
        }

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
            border-left: 4px solid #8b5cf6;
        }

        .image-container {
            margin: 20px 0;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Feliz Cumpleaños! 🎂</h1>
        </div>

        <div class="content">
            <p class="message">{{ $body }}</p>

            <!-- Imagen Inline (Solo para Clientes y Contratistas) -->
            @if(isset($data['image_url']))
                <div class="image-container">
                    <img src="{{ $message->embed($data['image_url']) }}" alt="Tarjeta de Cumpleaños">
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del sistema ERP Elite.</p>
        </div>
    </div>
</body>

</html>