<?php
session_start();

// Verifica si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // Incluye la conexión a la base de datos
    require '../Admin/configuracion/conexion.php';

    // Obtiene la dirección IP del usuario
    $ip = $_SERVER['REMOTE_ADDR'];

    // Registra la actividad de cierre de sesión en la base de datos
    $evento = "Cierre de sesión";
    $tipo_evento = "cierre_sesion"; // Agregar el tipo de evento
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);
}

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Redirige a la página de inicio de sesión
header("Location: login.php");
exit();
?>
