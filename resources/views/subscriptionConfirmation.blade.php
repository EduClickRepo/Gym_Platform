@extends('layouts.app')

<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Suscripción</title>
</head>
<body>
<p>Hola {{ $user->name }},</p>
<p>Adjunto encontrarás el comprobante de tu suscripción al débito automático.</p>
<p>Si tienes dudas, contáctanos.</p>
<p>Saludos,<br>{{ config('app.name') }}</p>
</body>
</html>