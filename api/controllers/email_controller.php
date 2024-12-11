<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
try {
    // Configuración de PHPMailer
    $mail = new PHPMailer(true);
    
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com'; // Cambia esto por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'contacto@chollocuenca.site'; // Tu correo
    $mail->Password = 'CcjmpDaw92%';         // Tu contraseña
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465; // Puerto del servidor SMTP (por defecto: 587 para TLS o 465 para SSL)

    // Remitente y destinatario
    $mail->setFrom('contacto@chollocuenca.site', 'chollocuenca'); // Remitente
    $mail->addAddress('amparwen92@gmail.com', 'Jennifer'); // Destinatario

    // Contenido del correo
    $mail->isHTML(true); // Permitir HTML en el correo
    $mail->Subject = 'Prueba de envío automático';
    $mail->Body = '<h1>Hola</h1><p>Este es un correo de prueba enviado automáticamente al acceder a la página.</p>';
    $mail->AltBody = 'Este es un correo de prueba enviado automáticamente al acceder a la página.';

    // Enviar correo
    $mail->send();
    echo 'Correo enviado con éxito';
} catch (Exception $e) {
    echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
}

?>
