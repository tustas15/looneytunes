<?php
require('../../reportes/fpdf/fpdf.php');
include('../conexion.php');

if (!isset($_POST['fecha_inicio']) || !isset($_POST['fecha_fin']) || !isset($_POST['tipo_reporte']) || !isset($_POST['opcion_especifica'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit();
}

$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$tipo_reporte = $_POST['tipo_reporte'];
$opcion_especifica = $_POST['opcion_especifica'];

switch ($tipo_reporte) {
    case 'categoria':
        $sql = "
        SELECT 
            c.CATEGORIA AS categoria, 
            CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS deportista, 
            DATE_FORMAT(ep.FECHA, '%M %Y') AS fecha, 
            IFNULL(SUM(p.MONTO), 0) AS monto,
            ep.ESTADO AS estado
        FROM 
            tab_estado_pagos ep
        JOIN 
            tab_deportistas d ON ep.ID_DEPORTISTA = d.ID_DEPORTISTA
        JOIN 
            tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
        JOIN 
            tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
        LEFT JOIN 
            tab_pagos p ON p.ID_DEPORTISTA = d.ID_DEPORTISTA AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin
        WHERE 
            c.ID_CATEGORIA = :opcion_especifica
            AND ep.FECHA BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY 
            c.CATEGORIA, d.ID_DEPORTISTA, ep.FECHA, ep.ESTADO
        ORDER BY 
            categoria, deportista, fecha;
        ";
        break;

    case 'deportista':
        $sql = "
        SELECT 
            CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS deportista, 
            DATE_FORMAT(ep.FECHA, '%M %Y') AS fecha, 
            IFNULL(SUM(p.MONTO), 0) AS monto,
            ep.ESTADO AS estado
        FROM 
            tab_estado_pagos ep
        JOIN 
            tab_deportistas d ON ep.ID_DEPORTISTA = d.ID_DEPORTISTA
        LEFT JOIN 
            tab_pagos p ON p.ID_DEPORTISTA = d.ID_DEPORTISTA AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin
        WHERE 
            d.ID_DEPORTISTA = :opcion_especifica
            AND ep.FECHA BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY 
            d.ID_DEPORTISTA, ep.FECHA, ep.ESTADO
        ORDER BY 
            deportista, fecha;
        ";
        break;

    case 'representante':
        $sql = "
        SELECT 
            CONCAT(r.NOMBRE_REPRE, ' ', r.APELLIDO_REPRE) AS representante, 
            DATE_FORMAT(ep.FECHA, '%M %Y') AS fecha, 
            IFNULL(SUM(p.MONTO), 0) AS monto,
            ep.ESTADO AS estado
        FROM 
            tab_estado_pagos ep
        JOIN 
            tab_deportistas d ON ep.ID_DEPORTISTA = d.ID_DEPORTISTA
        JOIN 
            tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
        JOIN 
            tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        LEFT JOIN 
            tab_pagos p ON p.ID_DEPORTISTA = d.ID_DEPORTISTA AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin
        WHERE 
            r.ID_REPRESENTANTE = :opcion_especifica
            AND ep.FECHA BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY 
            r.ID_REPRESENTANTE, ep.FECHA, ep.ESTADO
        ORDER BY 
            representante, fecha;
        ";
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Tipo de reporte no válido']);
        exit();
}

$stmt = $conn->prepare($sql);
if (!$stmt->execute([
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin,
    'opcion_especifica' => $opcion_especifica[0]
])) {
    error_log("Error en la ejecución de la consulta: " . print_r($stmt->errorInfo(), true));
    echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
    exit();
}

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Datos obtenidos con éxito',
    'data' => $resultados
]);
exit();
