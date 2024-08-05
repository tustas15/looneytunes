<?php
// Asegúrate de iniciar la sesión al principio del archivo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
?>

<main>
    <div class="container mb-6 mt-5">
        <h1 class="title">Categorías</h1>
        <h2 class="subtitle">Buscar categoría</h2>
    </div>

    <div class="container pb-6 pt-6">
        <?php
        require_once "../main.php";

        if (isset($_POST['modulo_buscador'])) {
            require_once "../buscador.php";
        }

        if (!isset($_SESSION['busqueda_categoria']) && empty($_SESSION['busqueda_categoria'])) {
        ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="" method="POST" autocomplete="off" class="d-flex">
                        <input type="hidden" name="modulo_buscador" value="categoria">
                        <input type="text" class="form-control me-2" name="txt_buscador" placeholder="¿Qué estás buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30">
                        <button class="btn btn-info" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        <?php } else { ?>
            <div class="row mb-3">
                <div class="col-md-12 text-center">
                    <form action="" method="POST" autocomplete="off" class="d-flex justify-content-center">
                        <input type="hidden" name="modulo_buscador" value="categoria">
                        <input type="hidden" name="eliminar_buscador" value="categoria">
                        <p>Estás buscando <strong>“<?php echo $_SESSION['busqueda_categoria']; ?>”</strong></p>
                        <button type="submit" class="btn btn-danger ms-3">Eliminar búsqueda</button>
                    </form>
                </div>
            </div>

        <?php
            // Eliminar categoría
            if (isset($_GET['category_id_del'])) {
                require_once "./categoria_eliminar.php";
            }

            if (!isset($_GET['page'])) {
                $pagina = 1;
            } else {
                $pagina = (int) $_GET['page'];
                if ($pagina <= 1) {
                    $pagina = 1;
                }
            }

            $pagina = limpiar_cadena($pagina);
            $url = "index.php?vista=category_search&page=";
            $registros = 15;
            $busqueda = $_SESSION['busqueda_categoria'];

            // Paginador categoría
            require_once "./categoria_lista.php";
        }
        ?>
    </div>

</main>
<?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>