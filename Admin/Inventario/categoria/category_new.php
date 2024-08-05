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
        <h1 class="title">Categorías</h1>
        <h2 class="subtitle">Nueva categoría</h2>

        <div class="form-rest mb-6 mt-6"></div>

        <form action="./categoria_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="categoria_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="categoria_nombre" name="categoria_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="50" required>
                </div>
                <div class="col-md-6">
                    <label for="categoria_ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="categoria_ubicacion" name="categoria_ubicacion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}" maxlength="150">
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-block">Guardar</button>
            </div>
        </form>
    </div>
</main>
    <?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>

