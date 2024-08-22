<?php
session_start();
include_once('../../configuracion/conexion.php');
include('/xampp/htdocs/looneytunes/admin/includespro/header.php');
require('../fpdf/fpdf.php');

try {
    // Obtener los administradores
    $queryAdmins = "SELECT ID_ADMINISTRADOR, NOMBRE_ADMIN, APELLIDO_ADMIN FROM tab_administradores";
    $stmtAdmins = $conn->prepare($queryAdmins);
    $stmtAdmins->execute();
    $resultAdmins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los tipos de eventos en los logs
    $queryTiposEventos = "SHOW COLUMNS FROM tab_logs LIKE 'TIPO_EVENTO'";
    $stmtTiposEventos = $conn->prepare($queryTiposEventos);
    $stmtTiposEventos->execute();
    $row = $stmtTiposEventos->fetch(PDO::FETCH_ASSOC);
    
    $tiposEventos = [];
    if ($row) {
        $enumList = explode(",", str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type']) - 6))));
        $tiposEventos = $enumList;
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    die();
}
?>

<div class="container mt-5">
    <h2>Generar Reporte de Administradores</h2>
    <form method="POST" action="../descarga/generar_reporte_admin.php">
        <div class="form-group">
            <label for="administradores">Seleccionar Administrador(es):</label>
            <select multiple class="form-control" id="administradores" name="administradores[]">
                <?php foreach ($resultAdmins as $admin) { ?>
                    <option value="<?= htmlspecialchars($admin['ID_ADMINISTRADOR']); ?>">
                        <?= htmlspecialchars($admin['NOMBRE_ADMIN'] . " " . $admin['APELLIDO_ADMIN']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mt-4">
            <label for="eventos">Seleccionar Eventos a Incluir:</label>
            <?php foreach ($tiposEventos as $evento) { ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="eventos[]" value="<?= htmlspecialchars($evento); ?>">
                    <label class="form-check-label"><?= htmlspecialchars($evento); ?></label>
                </div>
            <?php } ?>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
