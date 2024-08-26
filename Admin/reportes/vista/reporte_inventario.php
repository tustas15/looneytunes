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
    // Obtener productos
    $queryProductos = "SELECT id_producto, producto_nombre FROM tab_productos";
    $stmtProductos = $conn->prepare($queryProductos);
    $stmtProductos->execute();
    $resultProductos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener categorías de productos
    $queryCategorias = "SELECT id_categoria_producto, categoria_nombre FROM tab_producto_categoria";
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
        max-width: 500px;
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

    .form-select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        margin-bottom: 20px;
    }

    .btn-submit {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        background-color: #007bff;
        color: #fff;
        border: none;
    }

    .btn-submit:hover {
        background-color: #0056b3;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-box"></i> Generar Reporte de Inventario</h2>
    </div>
    <form method="POST" action="../descarga/generar_reporte_inventario.php">
        <label for="productos" class="form-label"><i class="fas fa-cube"></i> Seleccionar Producto(s):</label>
        <select multiple class="form-select" id="productos" name="productos[]">
            <?php foreach ($resultProductos as $producto) { ?>
                <option value="<?= htmlspecialchars($producto['id_producto']); ?>">
                    <?= htmlspecialchars($producto['producto_nombre']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="categorias" class="form-label"><i class="fas fa-tags"></i> Seleccionar Categoría(s):</label>
        <select multiple class="form-select" id="categorias" name="categorias[]">
            <?php foreach ($resultCategorias as $categoria) { ?>
                <option value="<?= htmlspecialchars($categoria['id_categoria_producto']); ?>">
                    <?= htmlspecialchars($categoria['categoria_nombre']); ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn-submit"><i class="fas fa-download"></i> Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
