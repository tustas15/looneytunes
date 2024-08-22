<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.9.1/src/Exception.php';
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';

function sendEmail($toAddress, $toName, $subject, $body, $altBody) {
    $debug = true;
    try {
        $mail = new PHPMailer($debug);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "sistema.clublooneytunes.com";
        $mail->Port = 587;
        $mail->Username = "clublooneytunes@sistema.clublooneytunes.com";
        $mail->Password = "Aght7JHgt5kmjgaDFqSmp";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->setFrom('clublooneytunes@sistema.clublooneytunes.com', 'RECUPERACION CORREO SISTEMA "CLUB LOONEYTUNES"');
        $mail->addAddress($toAddress, $toName);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        
        $mail->send();
        return "Correo enviado exitosamente.";
    } catch (Exception $e) {
        return "Error al enviar el correo. Mailer Error: " . $e->getMessage();
    }
}
?>
