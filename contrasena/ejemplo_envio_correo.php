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
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    }
    // Autentificación con SMTP
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    // Login
    $mail->Host = "sistema.clublooneytunes.com";
    $mail->Port = 587;
    $mail->Username = "clublooneytunes@sistema.clublooneytunes.com";
    $mail->Password = "Aght7JHgt5kmjgaDFqSmp";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->setFrom('clublooneytunes@sistema.clublooneytunes.com', 'Cuarto');
    $mail->addAddress('tustasgamer@gmail.com', 'EICONO');
    //$mail->addAttachment("/home/user/Escritorio/imagendeejemplo.png", "imagendeejemplo.png");
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isHTML(true);
    $mail->Subject = 'Saludo cuarto software';
    $mail->Body = 'Hola a todos los de cuarto software, como estan';
    $mail->AltBody = 'Texto como elemento de texto simple';
    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: ".$e->getMessage();
}