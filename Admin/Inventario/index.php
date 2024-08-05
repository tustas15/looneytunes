<?php 

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';


$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
?>
<?php require "./inc/session_start.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <?php include "./inc/head.php"; ?>
</head>

<body>
    <?php

    if (!isset($_GET['vista']) || $_GET['vista'] == "") {
        $_GET['vista'] = "login";
    }


    if (is_file("./vistas/" . $_GET['vista'] . ".php") && $_GET['vista'] != "login" && $_GET['vista'] != "404") {

        /*== Cerrar sesion ==*/
        if ((!isset($_SESSION['id']) || $_SESSION['id'] == "") || (!isset($_SESSION['usuario']) || $_SESSION['usuario'] == "")) {
            include "./vistas/logout.php";
            exit();
        }

        include "./inc/navbarcopy.php";

        include "./vistas/" . $_GET['vista'] . ".php";

        include "./inc/script.php";

        include "./inc/footer.php";
    } else {
        if ($_GET['vista'] == "login") {
            include "/xampp/htdocs/looneytunes/public/login.php";
        } else {
            include "./vistas/404.php";
        }
    }
    ?>
</body>

</html>