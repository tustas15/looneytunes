<?php 
session_start();

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
?>
<!DOCTYPE html>
<html>

<head>
    <?php include "./inc/head.php"; ?>
</head>

<body>
    <?php
    // Si la sesión está iniciada, redirigir a la página principal
    if (isset($_SESSION['id']) && isset($_SESSION['usuario'])) {
        header("Location: index.php?vista=home");
        exit();
    }

    // Si la sesión no está iniciada, permitir mostrar la vista solicitada o 404
    if (!isset($_GET['vista']) || $_GET['vista'] == "") {
        $_GET['vista'] = "login";
    }

    // Verificar si la vista solicitada es válida
    if (is_file("./vistas/" . $_GET['vista'] . ".php")) {
        include "./inc/navbarcopy.html";
        include "./vistas/" . $_GET['vista'] . ".php";
        include "./inc/script.php";
        include "./inc/footer.php";
    } else {
        include "./vistas/404.php";
    }
    ?>
</body>

</html>
