<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-bottom: 3px solid #10b981;
            /* Emerald-500 */
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }

        .message-details {
            background-color: #ecfdf5;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #10b981;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #059669;
            /* Emerald-600 */
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }

        .image-container {
            margin-top: 20px;
            text-align: center;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0;">{{ $title }}</h2>
    </div>

    <div class="content">
        <p>{{ $body }}</p>

        @if(isset($data['sender_name']))
            <div class="message-details">
                <strong>De:</strong> {{ $data['sender_name'] }}<br>
                @if(isset($data['message_content']))
                    <strong>Mensaje:</strong> {{ Str::limit($data['message_content'], 100) }}
                @endif
            </div>
        @endif

        <!-- Imagen Inline -->
        @if(isset($data['image_url']))
            <div class="image-container">
                <img src="{{ $message->embed($data['image_url']) }}" alt="Nuevo Mensaje">
            </div>
        @endif

        @if($actionUrl)
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="button">Ver Mensaje</a>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Este es un correo automático del sistema ERP Elite.</p>
    </div>
</body>

</html>