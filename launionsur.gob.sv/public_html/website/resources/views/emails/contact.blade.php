<!DOCTYPE html>
<html lang="es">

<body>
    <h1>Nuevo mensaje de contacto</h1>
    <p><strong>Nombre:</strong> {{ $data['name'] }}</p>
    <p><strong>Correo:</strong> {{ $data['email'] }}</p>
    <p><strong>Asunto:</strong>
        @php
            $subjectLabel = match ($data['subject'] ?? 'general') {
                'services' => 'Trámites y Servicios',
                'complaint' => 'Denuncia Ciudadana',
                'info' => 'Información',
                'other' => 'Otro',
                default => 'Consulta General',
            };
        @endphp
        {{ $subjectLabel }}
    </p>
    @if (!empty($data['number'] ?? null))
        <p><strong>Teléfono:</strong> {{ $data['number'] }}</p>
    @endif
    @if (!empty($data['company'] ?? null))
        <p><strong>Empresa:</strong> {{ $data['company'] }}</p>
    @endif
    <p><strong>Mensaje:</strong></p>
    <p>{!! nl2br(e($data['message'])) !!}</p>
</body>

</html>
