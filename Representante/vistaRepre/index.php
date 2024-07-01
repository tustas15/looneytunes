<!DOCTYPE html>
<html lang="es">

<head>
<?php
include 'header.php';
?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php
        include 'sidebar.php';
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <?php
                // ... (código anterior)
                $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
                include 'navigation.php';
                ?>
                  
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">


    <!-- Scroll to Top Button-->
   <?php include 'scripts.php';?>

    
</body>
 <!-- Footer -->
 <?php include 'footer.php';
            ?>
            <!-- End of Footer -->

</html>