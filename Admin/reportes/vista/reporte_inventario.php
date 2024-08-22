<?php
session_start();
include_once('../../configuracion/conexion.php');
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

<div class="container mt-5">
    <h2>Generar Reporte de Inventario</h2>
    <form method="POST" action="../descarga/generar_reporte_inventario.php">
        <div class="form-group">
            <label for="productos">Seleccionar Producto(s):</label>
            <select multiple class="form-control" id="productos" name="productos[]">
                <?php foreach ($resultProductos as $producto) { ?>
                    <option value="<?= htmlspecialchars($producto['id_producto']); ?>">
                        <?= htmlspecialchars($producto['producto_nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mt-4">
            <label for="categorias">Seleccionar Categoría(s):</label>
            <select multiple class="form-control" id="categorias" name="categorias[]">
                <?php foreach ($resultCategorias as $categoria) { ?>
                    <option value="<?= htmlspecialchars($categoria['id_categoria_producto']); ?>">
                        <?= htmlspecialchars($categoria['categoria_nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
