<?php
require_once('../conexion.php');

$tipo = $_POST['tipo'];
$options = [];

try {
    switch ($tipo) {
        case 'categoria':
            $query = "SELECT DISTINCT c.ID_CATEGORIA as id, cat.CATEGORIA as nombre 
                      FROM tab_categoria_deportista c 
                      JOIN tab_categorias cat ON c.ID_CATEGORIA = cat.ID_CATEGORIA 
                      ORDER BY cat.CATEGORIA";
            break;
        case 'deportista':
            $query = "SELECT ID_DEPORTISTA as id, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) as nombre 
                      FROM tab_deportistas 
                      ORDER BY NOMBRE_DEPO";
            break;
        case 'representante':
            $query = "SELECT ID_REPRESENTANTE as id, CONCAT(NOMBRE_REPRE, ' ', APELLIDO_REPRE) as nombre 
                      FROM tab_representantes 
                      ORDER BY NOMBRE_REPRE";
            break;
        default:
            throw new Exception("Tipo de opción no válido");
    }

    $stmt = $conn->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options[] = $row;
    }

    echo json_encode($options);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}