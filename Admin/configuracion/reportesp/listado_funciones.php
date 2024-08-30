<?php
include '../conexion.php';

function getEstadoPago($fechaPago) {
    if (!$fechaPago) return "Mes no pagado";
    $fechaLimite = date('Y-m-08');
    return $fechaPago <= $fechaLimite ? "Pagado" : "Pago atrasado";
}

function getListado($conn, $tipo, $categoria = null) {
    $mesActual = date('Y-m');
    $fechaLimite = $mesActual . '-08';
    
    $where = [];
    switch ($tipo) {
        case 'al-dia':
            $where[] = "fecha_pago <= '$fechaLimite'";
            break;
        case 'atrasados':
            $where[] = "fecha_pago > '$fechaLimite'";
            break;
        case 'no-pagados':
            $where[] = "fecha_pago IS NULL";
            break;
        case 'menos-pagada':
            if ($categoria) {
                $where[] = "categoria = '" . $conn->real_escape_string($categoria) . "'";
            }
            break;
    }

    $where[] = "YEAR(fecha_pago) = YEAR(CURDATE()) AND MONTH(fecha_pago) = MONTH(CURDATE())";
    $whereClause = implode(" AND ", $where);

    $sql = "SELECT nombre, categoria, fecha_pago, monto
            FROM tab_pagos
            WHERE $whereClause
            ORDER BY nombre";

    $listado = [];
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = getEstadoPago($row['fecha_pago']);
            $listado[] = $row;
        }
        $result->free();
    } else {
        error_log("Error en la consulta SQL: " . $conn->error);
    }

    return $listado;
}

// Manejar solicitudes AJAX para listados
if (isset($_GET['action']) && $_GET['action'] == 'getListado') {
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $listado = getListado($conn, $tipo, $categoria);
    echo json_encode($listado);
    exit;
}

// No cierres la conexión aquí si necesitas usarla más adelante en el script
// $conn->close();
?>