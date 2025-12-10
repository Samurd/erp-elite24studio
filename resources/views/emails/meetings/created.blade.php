<!DOCTYPE html>
<html>

<head>
    <title>Nueva Reunión Programada</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #2c3e50;">Se ha programado una nueva reunión</h2>

        <p>Hola,</p>

        <p>Te informamos que se ha creado una nueva reunión en el sistema que podría interesarte.</p>

        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Título:</strong> {{ $meeting->title }}</p>
            <p><strong>Fecha:</strong> {{ $meeting->date }}</p>
            <p><strong>Hora:</strong> {{ $meeting->start_time }} - {{ $meeting->end_time }}</p>
            @if($meeting->team)
                <p><strong>Equipo:</strong> {{ $meeting->team->name }}</p>
            @endif
            @if($meeting->url)
                <p><strong>Link de Reunión:</strong> <a href="{{ $meeting->url }}">{{ $meeting->url }}</a></p>
            @endif
        </div>

        <p>Para ver más detalles, por favor visita el siguiente enlace:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('meetings.show', $meeting->id) }}"
                style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ver
                Reunión</a>
        </div>

        <p>Saludos,<br>El equipo de ERP Elite</p>
    </div>
</body>

</html>