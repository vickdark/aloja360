<x-mail::message>
# ¡Hola, {{ $user->name ?? 'Usuario' }}!

Estás recibiendo este correo porque solicitaste un restablecimiento de contraseña para tu cuenta en **{{ setting('empresa_nombre', setting('app_name', config('app.name'))) }}**.

<x-mail::button :url="$resetUrl" color="primary">
Restablecer Contraseña
</x-mail::button>

Este enlace para restablecer la contraseña expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.

Si no realizaste esta solicitud, no es necesario realizar ninguna acción.

Saludos,<br>
{{ setting('empresa_nombre', setting('app_name', config('app.name'))) }}

<x-slot:subcopy>
Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web:
<br>
<span class="break-all">[{{ $resetUrl }}]({{ $resetUrl }})</span>
</x-slot:subcopy>
</x-mail::message>
