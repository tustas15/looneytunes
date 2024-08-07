<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');


    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT p.id_pago, CONCAT(d.APELLIDO_DEPO, ' ', d.NOMBRE_DEPO) AS deportista, 
               CONCAT(r.APELLIDO_REPRE, ' ', r.NOMBRE_REPRE) AS representante,
               p.metodo_pago, p.fecha, p.motivo, p.monto
        FROM tab_pagos p
        JOIN tab_deportistas d ON p.id_deportista = d.ID_DEPORTISTA
        JOIN tab_representantes r ON p.id_representante = r.ID_REPRESENTANTE
        ORDER BY p.fecha DESC";

$result = $conn->query($sql);
$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(array("data" => $data));

$conn->close();

