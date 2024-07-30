<?php

require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
function generarNumeroFactura($apellido, $id_representante)
{
    global $conexion;

    $fecha_actual = date('Ymd');
    $iniciales = strtoupper(substr($apellido, 0, 2));

    // Obtener el último secuencial para este representante en la fecha actual
    $query = "SELECT MAX(secuencial) as ultimo_secuencial FROM tab_facturas 
              WHERE id_representante = ? AND DATE(fecha) = CURDATE()";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_representante);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    $secuencial = ($fila['ultimo_secuencial'] ?? 0) + 1;

    // Generar el número de factura
    $numero_factura = $iniciales . $fecha_actual . str_pad($secuencial, 3, '0', STR_PAD_LEFT);

    // Insertar el nuevo registro de factura
    $query_insert = "INSERT INTO tab_facturas (numero_factura, id_representante, fecha, secuencial) 
                     VALUES (?, ?, CURDATE(), ?)";
    $stmt_insert = $conexion->prepare($query_insert);
    $stmt_insert->bind_param('sii', $numero_factura, $id_representante, $secuencial);
    $stmt_insert->execute();

    return $numero_factura;
}
if (isset($_POST['id_representante']) && isset($_POST['apellido'])) {
    $id_representante = $_POST['id_representante'];
    $apellido = $_POST['apellido'];

    $numero_factura = generarNumeroFactura($apellido, $id_representante);

    echo $numero_factura;
} else {
    echo "Error: Datos incompletos";
}