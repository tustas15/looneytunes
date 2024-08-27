<?php
require('../conexion.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_informe = $_POST['tipo_informe'] ?? '';
    $id_especifico = $_POST['id_especifico'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';

    // Validación de datos
    if (empty($tipo_informe) || empty($id_especifico) || empty($fecha_inicio) || empty($fecha_fin)) {
        echo json_encode(["error" => "Todos los campos son obligatorios."]);
        exit;
    }

    if ($tipo_informe === 'categoria') {
        $sql = "SELECT c.CATEGORIA AS NOMBRE, d.NOMBRE_DEPO, 
                       DATE_FORMAT(p.FECHA_PAGO, '%m/%Y') AS MES_ANIO, 
                       p.MONTO, ep.ESTADO
                FROM tab_categoria_deportista cd
                JOIN tab_deportistas d ON cd.ID_DEPORTISTA = d.ID_DEPORTISTA
                JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE c.ID_CATEGORIA = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } elseif ($tipo_informe === 'deportista') {
        $sql = "SELECT d.NOMBRE_DEPO AS NOMBRE, 
                       DATE_FORMAT(p.FECHA_PAGO, '%m/%Y') AS MES_ANIO, 
                       p.MONTO, ep.ESTADO
                FROM tab_deportistas d
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE d.ID_DEPORTISTA = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } elseif ($tipo_informe === 'representante') {
        $sql = "SELECT r.NOMBRE_REPRE AS NOMBRE, d.NOMBRE_DEPO, 
                       DATE_FORMAT(p.FECHA_PAGO, '%m/%Y') AS MES_ANIO, 
                       p.MONTO, ep.ESTADO
                FROM tab_representantes_deportistas rd
                JOIN tab_deportistas d ON rd.ID_DEPORTISTA = d.ID_DEPORTISTA
                JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE r.ID_REPRESENTANTE = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } else {
        echo json_encode(["error" => "Tipo de informe no válido."]);
        exit;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_especifico, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);





        if (empty($data)) {
            echo json_encode(["message" => "No se encontraron datos para los criterios seleccionados."]);
        } else {
            echo json_encode($data);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>