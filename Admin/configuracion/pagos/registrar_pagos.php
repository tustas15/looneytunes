<?php
// Conexión a la base de datos utilizando PDO
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    // Configurar PDO para manejar excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos del formulario
    $id_representante = isset($_POST['representante']) ? $_POST['representante'] : null;
    $id_deportista = isset($_POST['deportista']) ? $_POST['deportista'] : null;
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : null;
    $monto = isset($_POST['monto']) ? $_POST['monto'] : null;
    $fecha_pago = isset($_POST['fecha']) ? $_POST['fecha'] : null;
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : null;

    // Inicializar variable para el nombre del archivo
    $nombre_archivo = null;

    // Manejar archivo de comprobante si el método de pago es transferencia
    if ($metodo_pago == 'transferencia' && isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
        $target_dir = "uploads/";
        $nombre_archivo = basename($_FILES['comprobante']['name']);
        $target_file = $target_dir . $nombre_archivo;
        if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $target_file)) {
            throw new Exception("Error al cargar el archivo de comprobante.");
        }
    }

    // Insertar el pago en la base de datos
    $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, METODO_PAGO, MONTO, FECHA_PAGO, MOTIVO, NOMBRE_ARCHIVO) 
            VALUES (:ID_REPRESENTANTE, :ID_DEPORTISTA, :METODO_PAGO, :MONTO, :FECHA_PAGO, :MOTIVO, :NOMBRE_ARCHIVO)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ID_REPRESENTANTE', $id_representante);
    $stmt->bindParam(':ID_DEPORTISTA', $id_deportista);
    $stmt->bindParam(':METODO_PAGO', $metodo_pago);
    $stmt->bindParam(':MONTO', $monto);
    $stmt->bindParam(':FECHA_PAGO', $fecha_pago);
    $stmt->bindParam(':MOTIVO', $motivo);
    $stmt->bindParam(':NOMBRE_ARCHIVO', $nombre_archivo);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
    } else {
        throw new Exception("Error al registrar el pago.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn = null;
?>
