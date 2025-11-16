<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de Traducción de Notificaciones (para MailMessage)
    |--------------------------------------------------------------------------
    |
    | Estas líneas traducen el texto predeterminado utilizado por la clase
    | Illuminate\Notifications\Messages\MailMessage, incluyendo el correo
    | de restablecimiento de contraseña.
    |
    */

    // 1. Línea: subject(Lang::get('Reset Password Notification'))
    'Reset Password Notification' => 'Notificación de Restablecimiento de Contraseña',

    // 2. Línea: line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
    'You are receiving this email because we received a password reset request for your account.' => 'Estás recibiendo este correo electrónico porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.',

    // 3. Línea: action(Lang::get('Reset Password'), $url)
    'Reset Password' => 'Restablecer Contraseña',

    // 4. Línea: line(Lang::get('This password reset link will expire in :count minutes.', [...] ))
    'This password reset link will expire in :count minutes.' => 'Este enlace para restablecer la contraseña caducará en :count minutos.',

    // 5. Línea: line(Lang::get('If you did not request a password reset, no further action is required.'));
    'If you did not request a password reset, no further action is required.' => 'Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción.',

    // Líneas adicionales comunes que podrías necesitar (Opcional, pero recomendado):
    'Hello!' => '¡Hola!',
    'Regards' => 'Saludos',
];