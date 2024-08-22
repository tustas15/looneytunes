<?php
session_start();
include_once('../../configuracion/conexion.php');
include('/xampp/htdocs/looneytunes/admin/includespro/header.php');
require('../fpdf/fpdf.php');

try {
    // Obtener los entrenadores
    $queryEntrenadores = "SELECT ID_ENTRENADOR, NOMBRE_ENTRE, APELLIDO_ENTRE FROM tab_entrenadores";
    $stmtEntrenadores = $conn->prepare($queryEntrenadores);
    $stmtEntrenadores->execute();
    $resultEntrenadores = $stmtEntrenadores->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los tipos de eventos en los logs
    $queryTiposEventos = "SHOW COLUMNS FROM tab_logs LIKE 'TIPO_EVENTO'";
    $stmtTiposEventos = $conn->prepare($queryTiposEventos);
    $stmtTiposEventos->execute();
    $row = $stmtTiposEventos->fetch(PDO::FETCH_ASSOC);
    
    $tiposEventos = [];
    if ($row) {
        $enumList = explode(",", str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type']) - 6))));
        $tiposEventos = array_filter($enumList, function($evento) {
            $eventosPermitidos = [
                'inicio_sesion',
                'cierre_sesion',
                'actualizacion_perfil',
                'nuevo_observacion_enviada',
                'nuevo_observacion_eliminada',
                'dato_eliminado',
                'subida_pdf',
                'descarga_pdf',
                'cambio_contraseña'
            ];
            return in_array($evento, $eventosPermitidos);
        });
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    die();
}
?>

<div class="container mt-5">
    <h2>Generar Reporte de Entrenadores</h2>
    <form method="POST" action="../descarga/generar_reporte_entrenadores.php">
        <div class="form-group">
            <label for="entrenadores">Seleccionar Entrenador(es):</label>
            <select multiple class="form-control" id="entrenadores" name="entrenadores[]">
                <?php foreach ($resultEntrenadores as $entrenador) { ?>
                    <option value="<?= htmlspecialchars($entrenador['ID_ENTRENADOR']); ?>">
                        <?= htmlspecialchars($entrenador['NOMBRE_ENTRE'] . " " . $entrenador['APELLIDO_ENTRE']); ?>
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
