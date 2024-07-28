<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$query = "SELECT cedula, CONCAT(apellido, ' ', nombre) AS nombre_completo FROM tab_deportistas";
$result = $conexion->query($query);

$deportistas = array();
while ($row = $result->fetch_assoc()) {
    $deportistas[] = $row;
}

echo json_encode($deportistas);
?>
