<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//require_once "PHPMailer-6.9.1/vendor/autoload.php";
// Activar o desactivar excepciones mediante variable

require 'PHPMailer-6.9.1/src/Exception.php';
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';


$debug = true;
try {
    // Crear instancia de la clase PHPMailer
    $mail = new PHPMailer($debug);
    if ($debug) {
        // Genera un registro detallado
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    }
    // Autentificación con SMTP
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    // Login
    $mail->Host = "mail.pensamientosocial.com";
    $mail->Port = 587;
    $mail->Username = "cuarto@pensamientosocial.com";
    $mail->Password = "cuarto*itsi1234";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->setFrom('cuarto@pensamientosocial.com', 'Cuarto');
    $mail->addAddress('eicono@hotmail.com', 'EICONO');
    //$mail->addAttachment("/home/user/Escritorio/imagendeejemplo.png", "imagendeejemplo.png");
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isHTML(true);
    $mail->Subject = 'Saludo cuarto software';
    $mail->Body = 'Hola a todos los de cuarto software, como estan';
    $mail->AltBody = 'Texto como elemento de texto simple';
    if(!$mail->send()) {
        echo 'No se envio el correo.';
        echo 'Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Correo enviado correctamente';
    }

} catch (Exception $e) {
    echo "Mailer Error: ".$e->getMessage();
}