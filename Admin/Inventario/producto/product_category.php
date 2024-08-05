<?php
// Asegúrate de iniciar la sesión al principio del archivo
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
require_once('../main.php');
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
?>

<main class="container my-5">
    <div class="mb-6">
        <h1 class="title">Productos</h1>
        <h2 class="subtitle">Lista de productos por categoría</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <h2 class="title text-center">Categorías</h2>
            <?php
                $conexion = conexion();
                $categorias = $conexion->query("SELECT * FROM tab_producto_categoria");
                if ($categorias->rowCount() > 0) {
                    $categorias = $categorias->fetchAll();
                    foreach ($categorias as $row) {
                        echo '<a href="index.php?vista=product_category&category_id=' . $row['id_categoria_producto'] . '" class="btn btn-link btn-block mb-2">' . $row['categoria_nombre'] . '</a>';
                    }
                } else {
                    echo '<p class="text-center">No hay categorías registradas</p>';
                }
                $conexion = null;
            ?>
        </div>

        <div class="col-md-8">
            <?php
                $categoria_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;

                // Verificando categoría
                $conexion = conexion();
                $check_categoria = $conexion->query("SELECT * FROM tab_producto_categoria WHERE id_categoria_producto='$categoria_id'");

                if ($check_categoria->rowCount() > 0) {
                    $check_categoria = $check_categoria->fetch();

                    echo '
                        <h2 class="title text-center">' . $check_categoria['categoria_nombre'] . '</h2>
                        <p class="text-center pb-6">' . $check_categoria['categoria_ubicacion'] . '</p>
                    ';

                    // Eliminar producto
                    if (isset($_GET['product_id_del'])) {
                        require_once "./producto_eliminar.php";
                    }

                    $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $pagina = $pagina <= 1 ? 1 : $pagina;

                    $pagina = limpiar_cadena($pagina);
                    $url = "index.php?vista=product_category&category_id=$categoria_id&page="; // URL para la paginación
                    $registros = 15; // Número de registros por página
                    $busqueda = ""; // Búsqueda (por ahora vacío)

                    // Paginador producto
                    require_once "./producto_lista.php";

                } else {
                    echo '<h2 class="text-center title">Seleccione una categoría para empezar</h2>';
                }
                $conexion = null;
            ?>
        </div>
    </div>
</main>

<?php
include '/xampp/htdocs/looneytunes/admin/includespro/footer.php';
?>
