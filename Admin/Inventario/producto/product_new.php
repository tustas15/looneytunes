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
        <h2 class="subtitle">Nuevo producto</h2>

        <?php
        require_once "../main.php";
        ?>

        <div class="form-rest mb-6 mt-6"></div>

        <form action="./producto_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="producto_codigo" class="form-label">Código de barra</label>
                    <input type="text" class="form-control" id="producto_codigo" name="producto_codigo" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" required>
                </div>
                <div class="col-md-6">
                    <label for="producto_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}" maxlength="70" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="producto_precio" class="form-label">Precio</label>
                    <input type="text" class="form-control" id="producto_precio" name="producto_precio" pattern="[0-9.]{1,25}" maxlength="25" required>
                </div>
                <div class="col-md-6">
                    <label for="producto_stock" class="form-label">Stock</label>
                    <input type="text" class="form-control" id="producto_stock" name="producto_stock" pattern="[0-9]{1,25}" maxlength="25" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="producto_categoria" class="form-label">Categoría</label>
                    <select class="form-select" id="producto_categoria" name="producto_categoria">
                        <option value="" selected>Seleccione una opción</option>
                        <?php
                        $categorias = conexion();
                        $categorias = $categorias->query("SELECT * FROM tab_producto_categoria");
                        if ($categorias->rowCount() > 0) {
                            $categorias = $categorias->fetchAll();
                            foreach ($categorias as $row) {
                                echo '<option value="' . $row['id_categoria'] . '">' . $row['categoria_nombre'] . '</option>';
                            }
                        }
                        $categorias = null;
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="producto_foto" class="form-label">Foto o imagen del producto</label>
                    <input class="form-control" type="file" id="producto_foto" name="producto_foto" accept=".jpg, .png, .jpeg">
                    <small class="form-text text-muted">JPG, JPEG, PNG. (MAX 3MB)</small>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-block">Guardar</button>
            </div>
        </form>
    </div>
</main>
<?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>