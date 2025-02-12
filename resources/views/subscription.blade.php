<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Débito Automático</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; }
        h2 { color: #333; }
        .details { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Confirmación de Débito Automático</h2>
    <p>Estimado/a {{ $user->name }},</p>
    <p>Gracias por suscribirte a nuestro servicio de débito automático. Aquí están los detalles:</p>
    <div class="details">
        <p><strong>Plan:</strong> {{ $plan->name }}</p>
        <p><strong>Monto:</strong> ${{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</p>
        <p><strong>Fecha de inicio:</strong> {{ $subscription->created_at->format('d/m/Y') }}</p>
    </div>
    <p>Si tienes preguntas, contáctanos.</p>
    <p><strong>Atentamente,</strong><br>El equipo de {{ config('app.name') }}</p>
</div>
</body>
</html>