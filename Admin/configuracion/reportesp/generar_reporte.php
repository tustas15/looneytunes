<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$tipo_reporte = $_POST['tipo_reporte'];
$deportista = $_POST['deportista'];

$query = "";

if ($tipo_reporte === 'individual') {
    $query = "SELECT d.nombre_depo, d.apellido_depo, p.mes, p.anio, p.monto, p.fecha_pago, p.motivo
              FROM tab_pagos p
              JOIN tab_deportistas d ON p.id_deportista = d.id
              WHERE d.cedula = '$deportista'";
} elseif ($tipo_reporte === 'al_dia') {
    $query = "SELECT d.nombre, d.apellido, p.mes, p.anio, p.monto, p.fecha_pago, p.motivo
              FROM tab_deportistas d
              LEFT JOIN tab_pagos p ON d.id = p.id_deportista AND p.mes = MONTH(CURDATE()) AND p.anio = YEAR(CURDATE())
              WHERE p.id IS NOT NULL";
} elseif ($tipo_reporte === 'no_al_dia') {
    $query = "SELECT d.nombre, d.apellido, p.mes, p.anio, p.monto, p.fecha_pago, p.motivo
              FROM tab_deportistas d
              LEFT JOIN tab_pagos p ON d.id = p.id_deportista AND p.mes = MONTH(CURDATE()) AND p.anio = YEAR(CURDATE())
              WHERE p.id IS NULL";
}

$result = $conn->query($query);

$reporte = array();
while ($row = $result->fetch_assoc()) {
    $reporte[] = $row;
}

echo json_encode($reporte);
