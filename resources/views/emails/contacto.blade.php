<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        
        <h2 style="color: #0056A6; border-bottom: 2px solid #0056A6; padding-bottom: 10px;">
            ¡Nuevo Mensaje de Contacto Web!
        </h2>
        
        <p>Has recibido un nuevo mensaje a través del formulario de contacto:</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 30%;">Nombre:</td>
                <td style="padding: 8px 0;">{{ $nombre }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Email:</td>
                <td style="padding: 8px 0;"><a href="mailto:{{ $email }}" style="color: #0056A6;">{{ $email }}</a></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Fecha:</td>
                <td style="padding: 8px 0;">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <div style="background-color: #f8f8f8; padding: 15px; border-radius: 4px; border-left: 5px solid #0056A6;">
            <strong style="display: block; margin-bottom: 5px;">Mensaje:</strong>
            <p style="white-space: pre-wrap; margin: 0;">{{ $mensaje }}</p>
        </div>

        <p style="margin-top: 20px; font-size: 12px; color: #777;">
            Este es un correo automático.
        </p>
    </div>

</body>
</html>