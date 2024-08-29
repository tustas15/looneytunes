<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

function obtenerMesAnio($fecha) {
    return date('Y-m-01', strtotime($fecha));
}

function determinarEstadoPago($fechaPago, $fechaCorrespondiente) {
    $fechaLimite = date('Y-m-08', strtotime($fechaCorrespondiente));
    $fechaPago = new DateTime($fechaPago);
    $fechaLimite = new DateTime($fechaLimite);
    $fechaCorrespondiente = new DateTime($fechaCorrespondiente);

    if ($fechaPago <= $fechaLimite && $fechaPago->format('Y-m') == $fechaCorrespondiente->format('Y-m')) {
        return 'Pagado';
    } elseif ($fechaPago > $fechaLimite || $fechaPago->format('Y-m') > $fechaCorrespondiente->format('Y-m')) {
        return 'Pago Atrasado';
    } else {
        return 'Mes No Pagado';
    }
}

function actualizarEstadoPagos($conn, $idDeportista, $idCategoria, $idPago, $fechaPago, $montoTotal) {
    $fechaPago = new DateTime($fechaPago);
    $mesActual = obtenerMesAnio($fechaPago->format('Y-m-d'));
    $mesesPendientes = [];

    // Obtener meses pendientes de pago
    $sql = "SELECT FECHA FROM tab_estado_pagos 
            WHERE ID_DEPORTISTA = :id_deportista AND ID_CATEGORIA = :id_categoria 
            AND (ESTADO = 'Mes No Pagado' OR ESTADO = 'Pago Atrasado')
            AND FECHA <= :fecha_pago
            ORDER BY FECHA ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id_deportista' => $idDeportista,
        ':id_categoria' => $idCategoria,
        ':fecha_pago' => $mesActual
    ]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mesesPendientes[] = $row['FECHA'];
    }

    // Si no hay meses pendientes, asumimos que es el pago del mes actual
    if (empty($mesesPendientes)) {
        $mesesPendientes[] = $mesActual;
    }

    $montoPorMes = $montoTotal / count($mesesPendientes);

    foreach ($mesesPendientes as $mes) {
        $estado = determinarEstadoPago($fechaPago->format('Y-m-d'), $mes);
        
        $sql = "INSERT INTO tab_estado_pagos (ID_DEPORTISTA, ID_CATEGORIA, ID_PAGO, FECHA, ESTADO, MONTO) 
                VALUES (:id_deportista, :id_categoria, :id_pago, :fecha, :estado, :monto)
                ON DUPLICATE KEY UPDATE ESTADO = :estado, MONTO = :monto, ID_PAGO = :id_pago";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id_deportista' => $idDeportista,
            ':id_categoria' => $idCategoria,
            ':id_pago' => $idPago,
            ':fecha' => $mes,
            ':estado' => $estado,
            ':monto' => $montoPorMes
        ]);
    }
}

// Ejemplo de uso:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idDeportista = $_POST['id_deportista'] ?? '';
    $idCategoria = $_POST['id_categoria'] ?? '';
    $idPago = $_POST['id_pago'] ?? '';
    $fechaPago = $_POST['fecha_pago'] ?? '';
    $montoTotal = $_POST['monto'] ?? '';

    try {
        $conn->beginTransaction();
        actualizarEstadoPagos($conn, $idDeportista, $idCategoria, $idPago, $fechaPago, $montoTotal);
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Estados de pago actualizados correctamente']);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar estados de pago: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>