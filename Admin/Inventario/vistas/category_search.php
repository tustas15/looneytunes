<?php
require_once "./php/main.php"; // Incluye dependencias necesarias
ob_start(); // Iniciar el búfer de salida
?>

<div class="container is-fluid mb-6">
    <h1 class="title">Categorías</h1>
    <h2 class="subtitle">Buscar categoría</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    if (isset($_POST['modulo_buscador'])) {
        require_once "./php/buscador.php";
    }

    if (!isset($_SESSION['busqueda_categoria']) || empty($_SESSION['busqueda_categoria'])) {
    ?>
    <div class="columns">
        <div class="column">
            <form action="" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_buscador" value="categoria">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estás buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30">
                    </p>
                    <p class="control">
                        <button class="button is-info" type="submit">Buscar</button>
                    </p>
                </div>
            </form>
        </div>
    </div>
    <?php
    } else {
    ?>
    <div class="columns">
        <div class="column">
            <form class="has-text-centered mt-6 mb-6" action="" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_buscador" value="categoria"> 
                <input type="hidden" name="eliminar_buscador" value="categoria">
                <p>Estás buscando <strong>“<?php echo htmlspecialchars($_SESSION['busqueda_categoria']); ?>”</strong></p>
                <br>
                <button type="submit" class="button is-danger is-rounded">Eliminar búsqueda</button>
            </form>
        </div>
    </div>

    <?php
    # Eliminar categoría #
    if (isset($_GET['category_id_del'])) {
        require_once "./php/categoria_eliminar.php";
    }

    $pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($pagina <= 1) {
        $pagina = 1;
    }

    $pagina = limpiar_cadena($pagina);
    $url = "index.php?vista=category_search&page=";
    $registros = 15;
    $busqueda = isset($_SESSION['busqueda_categoria']) ? $_SESSION['busqueda_categoria'] : '';

    # Paginador categoría #
    require_once "./php/categoria_lista.php";
    }
    ?>
</div>

<?php
ob_end_flush(); // Enviar el contenido del búfer al navegador
?>
