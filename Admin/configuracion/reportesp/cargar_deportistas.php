<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Conecta a la base de datos
$mysqli = new mysqli('localhost', 'root', '', 'looneytunes');

// Verifica la conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Consulta para obtener los deportistas al día
$query = "SELECT d.CEDULA_DEPO, d.APELLIDO_DEPO, d.NOMBRE_DEPO
          FROM deportistas d
          LEFT JOIN pagos p ON d.CEDULA_DEPO = p.deportista_id
          WHERE (p.fecha_pago IS NULL OR DAY(p.fecha_pago) <= 5)
          GROUP BY d.CEDULA_DEPO, d.APELLIDO_DEPO, d.NOMBRE_DEPO";

// Ejecuta la consulta
$result = $mysqli->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$mysqli->close();

// Devuelve los datos en formato JSON
echo json_encode($data);
?>
