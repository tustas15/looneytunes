<?php
session_start();
include_once('../../configuracion/conexion.php');
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

<div class="container mt-5">
    <h2>Generar Reporte de Categorías</h2>
    <form method="POST" action="../descarga/generar_reporte_categoria_deportista.php">
        <div class="form-group">
            <label for="categorias">Seleccionar Categoría(s):</label>
            <select multiple class="form-control" id="categorias" name="categorias[]">
                <?php foreach ($resultCategorias as $categoria) { ?>
                    <option value="<?= htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                        <?= htmlspecialchars($categoria['CATEGORIA']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
