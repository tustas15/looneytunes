<?php
session_start();
include_once('../../configuracion/conexion.php');
// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

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
        <h2><i class="fas fa-clipboard-list"></i> Generar Reporte de Administradores</h2>
    </div>
    <form method="POST" action="../descarga/generar_reporte_admin.php">
        <label for="administradores" class="form-label"><i class="fas fa-user-shield"></i> Seleccionar Administrador:</label>
        <select class="form-select" id="administradores" name="administradores[]">
            <option value="" disabled selected>Seleccione un administrador</option>
            <?php foreach ($resultAdmins as $admin) { ?>
                <option value="<?= htmlspecialchars($admin['ID_ADMINISTRADOR']); ?>">
                    <?= htmlspecialchars($admin['NOMBRE_ADMIN'] . " " . $admin['APELLIDO_ADMIN']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="eventos" class="form-label"><i class="fas fa-calendar-check"></i> Seleccionar Evento:</label>
        <select class="form-select" id="eventos" name="eventos[]">
            <option value="" disabled selected>Seleccione un evento</option>
            <?php foreach ($tiposEventos as $evento) {
                // Reemplaza guiones bajos con espacios
                $evento_formateado = str_replace('_', ' ', $evento);
            ?>
                <option value="<?= htmlspecialchars($evento); ?>">
                    <?= htmlspecialchars($evento_formateado); ?>
                </option>
            <?php } ?>
        </select>


        <button type="submit" class="btn-submit"><i class="fas fa-download"></i> Generar Reporte</button>
    </form>
</div>

<?php include('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>