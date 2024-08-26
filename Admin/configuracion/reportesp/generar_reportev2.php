<?php
session_start();
require_once('../conexion.php');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener parámetros del formulario
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$tipos_reporte = $_POST['tipo_reporte'] ?? [];
$opciones_especificas = $_POST['opcion_especifica'] ?? [];

// Validar fechas
if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['success' => false, 'error' => 'Las fechas son obligatorias']);
    exit;
}

try {
    // Construir la consulta SQL base
    $sql = "SELECT p.id, p.monto, p.fecha_pago, c.nombre AS categoria, d.nombre AS deportista, r.nombre AS representante
            FROM tab_pagos p
            JOIN tab_categorias c ON p.id_categoria = c.id
            JOIN tab_deportistas d ON p.id_deportista = d.id
            JOIN tab_representantes r ON d.id_representante = r.id
            WHERE p.fecha_pago BETWEEN :fecha_inicio AND :fecha_fin";

    // Agregar filtros adicionales según el tipo de reporte seleccionado
    $params = [':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin];
    
    if (in_array('categoria', $tipos_reporte) && !empty($opciones_especificas)) {
        $categorias = array_filter($opciones_especificas, function($op) { return strpos($op, 'categoria_') === 0; });
        if (!empty($categorias)) {
            $categoriasIds = array_map(function($cat) { return substr($cat, 10); }, $categorias);
            $sql .= " AND c.id IN (" . implode(',', $categoriasIds) . ")";
        }
    }

    // Aquí puedes agregar más condiciones para deportista y representante si es necesario

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los resultados
    $totalPagos = 0;
    $montoTotal = 0;
    $deportistasAlDia = 0;
    $deportistasAtrasados = 0;
    $resultados = [];

    foreach ($pagos as $pago) {
        $fechaPago = new DateTime($pago['fecha_pago']);
        $fechaLimite = new DateTime($pago['fecha_pago']);
        $fechaLimite->setDate($fechaLimite->format('Y'), $fechaLimite->format('m'), 8);
        
        $estado = $fechaPago <= $fechaLimite ? 'Al día' : 'Atrasado';
        
        $totalPagos++;
        $montoTotal += $pago['monto'];
        
        if ($estado == 'Al día') {
            $deportistasAlDia++;
        } else {
            $deportistasAtrasados++;
        }
        
        $resultados[] = [
            'categoria' => $pago['categoria'],
            'mes' => $fechaPago->format('Y-m'),
            'monto' => $pago['monto'],
            'estado' => $estado,
            'fecha_pago' => $pago['fecha_pago'],
            'deportista' => $pago['deportista'],
            'representante' => $pago['representante']
        ];
    }

    // Preparar la respuesta
    $response = [
        'success' => true,
        'datos' => $resultados,
        'totalPagos' => $totalPagos,
        'montoTotal' => $montoTotal,
        'deportistasAlDia' => $deportistasAlDia,
        'deportistasAtrasados' => $deportistasAtrasados
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}