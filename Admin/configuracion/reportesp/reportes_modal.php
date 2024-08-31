<?php
require_once('../conexion.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

$response = [
    'success' => false,
    'data' => [],
    'error' => null,
    'debug' => []
];

try {
    $response['debug'][] = "Action: $action, Tipo: $tipo";
    
    if ($action === 'getListado') {
        if ($tipo === 'al-dia') {
            $query = "SELECT D.NOMBRE_DEPO AS DEPORTISTA, C.CATEGORIA, P.MONTO, DATE_FORMAT(P.FECHA_PAGO, '%m/%Y') AS FECHA
                          FROM tab_pagos P
                          JOIN tab_deportistas D ON P.ID_DEPORTISTA = D.ID_DEPORTISTA
                          JOIN tab_categoria_deportista CD ON D.ID_DEPORTISTA = CD.ID_DEPORTISTA
                          JOIN tab_categorias C ON CD.ID_CATEGORIA = C.ID_CATEGORIA
                          WHERE P.FECHA_PAGO <= DATE_SUB(CURDATE(), INTERVAL 8 DAY)
                          ORDER BY D.NOMBRE_DEPO";
        } elseif ($tipo === 'atrasados') {
            $query = "SELECT D.NOMBRE_DEPO AS DEPORTISTA, C.CATEGORIA, P.MONTO, DATE_FORMAT(P.FECHA_PAGO, '%m/%Y') AS FECHA
                      FROM tab_pagos P
                      JOIN tab_deportistas D ON P.ID_DEPORTISTA = D.ID_DEPORTISTA
                          JOIN tab_categoria_deportista CD ON D.ID_DEPORTISTA = CD.ID_DEPORTISTA
                          JOIN tab_categorias C ON CD.ID_CATEGORIA = C.ID_CATEGORIA
                      WHERE P.FECHA_PAGO > DATE_SUB(CURDATE(), INTERVAL 8 DAY)
                      ORDER BY D.NOMBRE_DEPO";
        } elseif ($tipo === 'categoria-mayor-atraso') {
            $query = "SELECT C.CATEGORIA, DATE_FORMAT(P.FECHA_PAGO, '%m/%Y') AS FECHA, COUNT(*) AS CANTIDAD_ATRASOS
                      FROM tab_pagos P
                      JOIN tab_deportistas D ON P.ID_DEPORTISTA = D.ID_DEPORTISTA
                          JOIN tab_categoria_deportista CD ON D.ID_DEPORTISTA = CD.ID_DEPORTISTA
                          JOIN tab_categorias C ON CD.ID_CATEGORIA = C.ID_CATEGORIA
                      WHERE P.FECHA_PAGO > DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                      GROUP BY C.CATEGORIA, FECHA
                      ORDER BY CANTIDAD_ATRASOS DESC";
        } elseif ($tipo === 'meses-pagados') {
            $query = "SELECT MONTH(p.FECHA_PAGO) AS MES, YEAR(p.FECHA_PAGO) AS ANIO, 
                      SUM(CASE 
                            WHEN p.FECHA_PAGO <= DATE_SUB(CURDATE(), INTERVAL 8 DAY) THEN p.MONTO
                            ELSE 0
                        END) AS TOTAL
                      FROM tab_pagos p
                      GROUP BY MES, ANIO";
        } else {
            throw new Exception("Tipo de listado no reconocido");
        }

        $response['debug'][] = "Query: $query";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['success'] = true;
        $response['debug'][] = "Row count: " . count($response['data']);
    } else {
        throw new Exception("Acción no reconocida");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    $response['debug'][] = "Exception: " . $e->getMessage();
} finally {
    $conn = null;
}

header('Content-Type: application/json');
echo json_encode($response);
?>