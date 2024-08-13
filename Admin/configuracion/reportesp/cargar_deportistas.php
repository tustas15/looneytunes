<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$query = "SELECT cedula_depo, CONCAT(apellido_depo, ' ', nombre_depo) AS nombre_completo FROM tab_deportistas";
$result = $conn->query($query);

$deportistas = array();
while ($row = $result->fetch_assoc()) {
    $deportistas[] = $row;
}

echo json_encode($deportistas);
?>
