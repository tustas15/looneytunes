<?php
require_once('../conexion.php');

// Habilitar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si se recibieron todos los parámetros necesarios
if (!isset($_POST['fecha_inicio']) || !isset($_POST['fecha_fin']) || !isset($_POST['tipo_reporte']) || !isset($_POST['opcion_especifica'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Parámetros incompletos']);
    exit;
}

$fechaInicio = $_POST['fecha_inicio'];
$fechaFin = $_POST['fecha_fin'];

try {
    $query = "SELECT p.ID_PAGO, p.MONTO, p.FECHA_PAGO,
           d.NOMBRE_DEPO, d.APELLIDO_DEPO,
           c.CATEGORIA,
           r.NOMBRE_REPRE, r.APELLIDO_REPRE,
           CASE
               WHEN p.FECHA_PAGO IS NOT NULL AND p.MONTO > 0 THEN 'Pagado'
               ELSE 'No Pagado'
           END AS ESTADO_PAGO
    FROM tab_pagos p
    LEFT JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
    LEFT JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
    LEFT JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
    LEFT JOIN tab_usuarios u ON d.ID_USUARIO = u.ID_USUARIO
    LEFT JOIN tab_representantes r ON u.ID_USUARIO = r.ID_USUARIO
    WHERE DATE(p.FECHA_PAGO) BETWEEN :fechaInicio AND :fechaFin";

    $params = [':fechaInicio' => $fechaInicio, ':fechaFin' => $fechaFin];

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        $checkQuery = "SELECT COUNT(*) as count FROM tab_pagos WHERE DATE(FECHA_PAGO) BETWEEN :fechaInicio AND :fechaFin";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute($params);
        $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];

        header('Content-Type: application/json');
        if ($count > 0) {
            echo json_encode(['error' => 'Se encontraron pagos en el rango de fechas, pero no se pudieron recuperar todos los datos. Verifique las relaciones entre tablas.']);
        } else {
            echo json_encode(['error' => 'No se encontraron pagos para el rango de fechas especificado.']);
        }
        exit;
    }

    $montoTotal = array_sum(array_column($results, 'MONTO'));
    $totalPagos = count($results);

    $pagosPagados = count(array_filter($results, function($r) { return $r['ESTADO_PAGO'] == 'Pagado'; }));
$pagosNoPagados = $totalPagos - $pagosPagados;

$estadisticas = [
    'pagados' => $pagosPagados,
    'noPagados' => $pagosNoPagados
];
    

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Reporte generado con éxito',
        'totalPagos' => $totalPagos,
        'montoTotal' => $montoTotal,
        'resultados' => $results,
        'estadisticas' => $estadisticas
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error general: ' . $e->getMessage()]);
}
?>