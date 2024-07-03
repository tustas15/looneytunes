<?php
session_start();
include '../Admin/configuracion/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include '../representante/vistaRepre/header.php'; ?>
    <!-- Asegúrate de que el header.php esté correctamente incluido -->
    <meta charset="utf-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="indexRep.css"> <!-- Enlace a la hoja de estilos -->
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include '../representante/vistaRepre/sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"> <?php
                                                                                                        // ... (código anterior)
                                                                                                        $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
                                                                                                        include '../representante/vistaRepre/navigation.php';
                                                                                                        ?>

                    <!-- End of Topbar -->
                    <?php include '../representante/vistaRepre/footer.php'; ?>
            </div>
        </div>
    </div>
    <?php include '../representante/vistaRepre/scripts.php'; ?>
</body>

</html>