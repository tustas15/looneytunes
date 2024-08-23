<?php
session_start();
require_once('../conexion.php');

// Verificar el tipo de filtro enviado
$filtro = $_POST['filtro'] ?? '';

switch ($filtro) {
    case 'categoria':
        $sql = "SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias ORDER BY CATEGORIA";
        break;
    case 'deportista':
        $sql = "SELECT ID_DEPORTISTA, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) AS nombre FROM tab_deportistas ORDER BY NOMBRE_DEPO";
        break;
    case 'representante':
        $sql = "SELECT ID_REPRESENTANTE, CONCAT(NOMBRE_REPRE, ' ', APELLIDO_REPRE) AS nombre FROM tab_representantes ORDER BY NOMBRE_REPRE";
        break;
    default:
        echo '<option value="">Seleccione una opción</option>';
        exit;
}

$result = $conn->query($sql);

if ($result === false) {
    echo '<option value="">Error en la consulta: ' . $conn->error . '</option>';
    exit;
}

$options = '<option value="">Seleccione una opción</option>';
while ($row = $result->fetch_assoc()) {
    $value = $row['ID_CATEGORIA'] ?? $row['ID_DEPORTISTA'] ?? $row['ID_REPRESENTANTE'];
    $name = $row['CATEGORIA'] ?? $row['nombre'];
    $options .= "<option value='{$value}'>{$name}</option>";
}

echo $options;
?>