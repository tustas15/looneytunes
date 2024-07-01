<?php
session_start();
include_once "./Admin/configuracion/conexion.php";

// Verifica si el usuario está autenticado
if (isset($_SESSION['user_id']) && isset($_SESSION['tipo_usuario'])) {
    // Si el usuario ya está autenticado, redirige según su tipo de usuario
    switch ($_SESSION['tipo_usuario']) {
        case 1: // Administrador
            header("Location: ./admin/indexAd.php");
            break;
        case 2: // Entrenador
            header("Location: ./entrenador/indexEntrenador.php");
            break;
        case 3: // Representante
            header("Location: ./representante/indexRep.php");
            break;
        case 4: // Deportista
            header("Location: ./deportista/indexDep.php");
            break;
        default:
            // Si el tipo de usuario no está definido o es incorrecto, redirige a la página de inicio de sesión
            header("Location: ./public/login.php");
            break;
    }
    exit(); // Asegúrate de que el código no siga ejecutándose después de la redirección
} else {
    // Si el usuario no está autenticado, redirige a la página de inicio de sesión
    header("Location: ./public/login.php");
    exit();
}
?>
