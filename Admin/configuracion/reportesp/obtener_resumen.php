<?php
session_start();
require_once('../conexion.php');

try {
    // Consultas para obtener los datos del resumen
    $queryTotalPagos = "SELECT COUNT(*) as total FROM pagos";
    $queryMontoTotal = "SELECT SUM(monto) as total FROM pagos";
    $queryDeportistasAlDia = "SELECT COUNT(DISTINCT id_deportista) as total FROM pagos WHERE estado = 'Al día'";
    $queryDeportistasAtrasados = "SELECT COUNT(DISTINCT id_deportista) as total FROM pagos WHERE estado = 'Atrasado'";
    $queryCategoriaAtrasada = "SELECT c.nombre_categoria, COUNT(*) as total 
                               FROM pagos p 
                               JOIN deportistas d ON p.id_deportista = d.id_deportista 
                               JOIN categorias c ON d.id_categoria = c.id_categoria 
                               WHERE p.estado = 'Atrasado' 
                               GROUP BY c.id_categoria 
                               ORDER BY total DESC 
                               LIMIT 1";
    $queryTendenciaPagos = "SELECT 
                                CASE 
                                    WHEN actual.total > anterior.total THEN 'Aumento'
                                    WHEN actual.total < anterior.total THEN 'Disminución'
                                    ELSE 'Estable'
                                END as tendencia
                            FROM 
                                (SELECT COUNT(*) as total FROM pagos WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) as actual,
                                (SELECT COUNT(*) as total FROM pagos WHERE fecha_pago BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) as anterior";

    // Ejecutar las consultas y obtener los resultados
    $totalPagos = $conn->query($queryTotalPagos)->fetch(PDO::FETCH_ASSOC)['total'];
    $montoTotal = $conn->query($queryMontoTotal)->fetch(PDO::FETCH_ASSOC)['total'];
    $deportistasAlDia = $conn->query($queryDeportistasAlDia)->fetch(PDO::FETCH_ASSOC)['total'];
    $deportistasAtrasados = $conn->query($queryDeportistasAtrasados)->fetch(PDO::FETCH_ASSOC)['total'];
    $categoriaAtrasada = $conn->query($queryCategoriaAtrasada)->fetch(PDO::FETCH_ASSOC)['nombre_categoria'];
    $tendenciaPagos = $conn->query($queryTendenciaPagos)->fetch(PDO::FETCH_ASSOC)['tendencia'];

    // Calcular el promedio de pago por deportista
    $promedioPago = $totalPagos > 0 ? $montoTotal / $totalPagos : 0;

    // Calcular la tasa de cumplimiento
    $totalDeportistas = $deportistasAlDia + $deportistasAtrasados;
    $tasaCumplimiento = $totalDeportistas > 0 ? ($deportistasAlDia / $totalDeportistas) * 100 : 0;

    // Preparar la respuesta
    $respuesta = [
        'total_pagos' => $totalPagos,
        'monto_total' => $montoTotal,
        'promedio_pago' => $promedioPago,
        'tasa_cumplimiento' => round($tasaCumplimiento, 2),
        'deportistas_al_dia' => $deportistasAlDia,
        'deportistas_atrasados' => $deportistasAtrasados,
        'categoria_atrasada' => $categoriaAtrasada,
        'tendencia_pagos' => $tendenciaPagos
    ];

    // Enviar la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);

} catch (PDOException $e) {
    // Manejar errores
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>