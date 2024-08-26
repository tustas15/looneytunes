<?php
session_start();
include_once('../../configuracion/conexion.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: /looneytunes/public/login.php");
    exit();
}
include('/xampp/htdocs/looneytunes/admin/includespro/header.php');
require('../fpdf/fpdf.php');

try {
    // Obtener las categorías
    $queryCategorias = "SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias";
    $stmtCategorias = $conn->prepare($queryCategorias);
    $stmtCategorias->execute();
    $resultCategorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    die();
}
?>

<style>
    body {
        background-color: #eef2f7;
    }

    .form-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .form-header h2 {
        font-size: 22px;
        color: #343a40;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-select,
    .form-control {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        margin-bottom: 20px;
    }

    .btn-submit,
    .btn-primary {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        background-color: #007bff;
        color: #fff;
        border: none;
    }

    .btn-submit:hover,
    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-clipboard-list"></i> Generar Reporte de Categorías Deportistas</h2>
    </div>
    <form method="POST" action="../descarga/generar_reporte_categoria_deportista.php">
        <label for="categorias" class="form-label"><i class="fas fa-tags"></i> Seleccionar Categoría:</label>
        <select class="form-select" id="categorias" name="categorias">
            <option value="" disabled selected>Seleccione una categoría</option>
            <?php foreach ($resultCategorias as $categoria) { ?>
                <option value="<?= htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                    <?= htmlspecialchars($categoria['CATEGORIA']); ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn-submit"><i class="fas fa-download"></i> Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
