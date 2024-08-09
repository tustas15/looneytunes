<?php
// Incluir el archivo de configuración de la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');



// Si es necesario, agregar código específico para el perfil de representante
// y cómo debe comportarse el formulario en este contexto
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include './Includes/header.php';?>

</head>
<main>
    <h1>Formulario de Pagos - Representante</h1>
    <!-- Aquí puedes agregar el formulario o contenido específico para el representante -->
    <!-- Puedes copiar el contenido de pagos.php aquí o hacer referencia a él -->
    <?php
    // Si `pagos.php` tiene HTML y necesitas mostrarlo, puedes incluirlo aquí
    include('../Admin/configuracion/pagos/pagos.php');
    ?>
</main>
</html>
