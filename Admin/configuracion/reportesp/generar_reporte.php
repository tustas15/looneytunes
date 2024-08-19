<?php
require_once('../conexion.php');

$tipo_reporte = $_POST['tipo_reporte'];
$deportista = $_POST['deportista'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

$query = "";

if ($tipo_reporte === 'individual') {
    SELECT d.NOMBRE_DEPO, d.APELLIDO_DEPO, p.FECHA_PAGO, p.MONTO, p.METODO_PAGO
    FROM tab_deportistas d
    LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
    WHERE d.ID_DEPORTISTA = [ID_DEL_DEPORTISTA]
    ORDER BY p.FECHA_PAGO DESC;
} elseif ($tipo_reporte === 'al_dia') {
    $query = "SELECT d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
              MONTH(p.FECHA_PAGO) as mes, YEAR(p.FECHA_PAGO) as anio, 
              p.MONTO, p.FECHA_PAGO, p.MOTIVO
              FROM tab_deportistas d
              INNER JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
              WHERE p.FECHA_PAGO BETWEEN '$fecha_inicio' AND '$fecha_fin'";
} elseif ($tipo_reporte === 'no_al_dia') {
    $query = "SELECT d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
              NULL as mes, NULL as anio, 
              NULL as MONTO, NULL as FECHA_PAGO, 'No ha pagado' as MOTIVO
              FROM tab_deportistas d
              WHERE d.ID_DEPORTISTA NOT IN (
                  SELECT DISTINCT ID_DEPORTISTA 
                  FROM tab_pagos 
                  WHERE FECHA_PAGO BETWEEN '$fecha_inicio' AND '$fecha_fin'
              )";
}

$result = $conn->query($query);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$reporte = array();
while ($row = $result->fetch_assoc()) {
    $reporte[] = $row;
}
error_log("Query: " . $query);
error_log("Resultado: " . print_r($reporte, true));

echo json_encode($reporte);
$conn->close();