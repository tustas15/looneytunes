<?php
// Asegúrate de iniciar la sesión al principio del archivo
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
?>

<main>
    <div class="container mb-6 mt-5">
        <h1 class="title">Productos</h1>
        <h2 class="subtitle">Lista de productos</h2>
    </div>

    <div class="container pb-6 pt-6">
        <?php
        require_once "../main.php";

        // Eliminar producto
        if (isset($_GET['product_id_del'])) {
            require_once "./producto_eliminar.php";
        }

        // Paginación
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pagina = $pagina <= 1 ? 1 : $pagina;

        $categoria_id = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : 0;

        $pagina = limpiar_cadena($pagina);
        $url = "index.php?vista=product_list&page="; // URL para la paginación
        $registros = 15; // Número de registros por página
        $busqueda = ""; // Búsqueda (por ahora vacío)

        // Paginador producto
        require_once "./producto_lista.php";
        ?>
    </div>
    </main>
    <?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>

