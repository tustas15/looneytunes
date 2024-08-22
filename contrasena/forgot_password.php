<?php
// Conexión a la base de datos
require '../admin/configuracion/conexion.php';
require 'envio.php'; // Incluye el archivo de envío

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verifica si el correo electrónico pertenece a un representante
    $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO FROM tab_usuarios u 
                           JOIN tab_representantes r ON u.ID_USUARIO = r.ID_USUARIO
                           WHERE r.CORREO_REPRE = :email AND u.status = 'activo'");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Si no es representante, verifica si es un entrenador
    if (!$user) {
        $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO FROM tab_usuarios u 
                               JOIN tab_entrenadores e ON u.ID_USUARIO = e.ID_USUARIO
                               WHERE e.CORREO_ENTRE = :email AND u.status = 'activo'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
    }

    // Si el correo pertenece a un deportista (a través de su representante)
    if (!$user) {
        $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO FROM tab_usuarios u
                               JOIN tab_representantes_deportistas rd ON u.ID_USUARIO = rd.ID_REPRESENTANTE
                               JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
                               WHERE r.CORREO_REPRE = :email AND u.status = 'activo'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
    }

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expDate = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Guardar el token y su fecha de expiración
        $stmt = $conn->prepare("UPDATE tab_usuarios SET reset_token = :token, reset_token_exp = :expDate WHERE ID_USUARIO = :userId");
        $stmt->execute(['token' => $token, 'expDate' => $expDate, 'userId' => $user['ID_USUARIO']]);

        // Preparar el mensaje del correo
        $subject = 'Recuperación de contraseña';
        $body = "Hola " . $user['USUARIO'] . ",<br><br>Hemos recibido una solicitud para restablecer tu contraseña. 
                 Haz clic en el siguiente enlace para restablecerla: <br><br>
                 <a href='http://localhost/looneytunes/contrasena/reset_password.php?token=" . $token . "'>Restablecer Contraseña</a><br><br>
                 Este enlace es válido por una hora.<br><br>Si no solicitaste este cambio, por favor ignora este correo.";
        $altBody = "Hola " . $user['USUARIO'] . ",\n\nHemos recibido una solicitud para restablecer tu contraseña. 
                    Visita el siguiente enlace para restablecerla: \n\n
                    http://localhost/looneytunes/contrasena/reset_password.php?token=" . $token . "\n\n
                    Este enlace es válido por una hora.\n\nSi no solicitaste este cambio, por favor ignora este correo.";

        // Enviar correo usando la función sendEmail
        $result = sendEmail($email, $user['USUARIO'], $subject, $body, $altBody);

        // Redirigir con el estado de éxito
        header("Location: ../public/recuperacion_contrasena.php?status=success");
        exit;
    } else {
        // Redirigir con el estado de error
        header("Location: ../public/recuperacion_contrasena.php?status=error");
        exit;
    }
}
?>
