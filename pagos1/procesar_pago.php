<?php
// Incluir archivo de conexión a la base de datos
require_once('../Admin/configuracion/conexion.php');

// Verificar si se recibieron datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir y limpiar los datos del formulario
    $nombre_representante = $_POST['nombre_representante'] ?? '';
    $tipo_pago = $_POST['tipo_pago'] ?? '';
    $nombre_deportista = '';
    $motivo = '';
    $monto = '';
    $fecha = '';

    // Dependiendo del tipo de pago, asignar los valores correspondientes
    if ($tipo_pago === 'efectivo') {
        $nombre_deportista = $_POST['nombre_deportista_efectivo'] ?? '';
        $motivo = $_POST['motivo_efectivo'] ?? '';
        $monto = $_POST['monto_efectivo'] ?? '';
        $fecha = $_POST['fecha_efectivo'] ?? '';
    } elseif ($tipo_pago === 'transferencia') {
        $nombre_deportista = $_POST['nombre_deportista_transferencia'] ?? '';
        $motivo = $_POST['motivo_transferencia'] ?? '';
        $banco = $_POST['banco'] ?? '';
        $monto = $_POST['monto_transferencia'] ?? '';
        $fecha = $_POST['fecha_transferencia'] ?? '';
    }

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Obtener el ID del representante por su nombre
        $sql_representante = "SELECT ID_REPRESENTANTE FROM tab_representantes WHERE nombre_repre = :nombre_representante";
        $stmt_representante = $conn->prepare($sql_representante);
        $stmt_representante->bindParam(':nombre_representante', $nombre_representante);
        $stmt_representante->execute();
        $id_representante = $stmt_representante->fetchColumn();

        // Insertar el pago en la tabla de pagos
        $sql_insert = "
            INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, FECHA, TIPO_PAGO, MONTO, MOTIVO, BANCO)
            VALUES (
                :id_representante,
                (SELECT ID_DEPORTISTA FROM tab_deportistas WHERE nombre_depo = :nombre_deportista),
                :fecha,
                :tipo_pago,
                :monto,
                :motivo,
            
            )
        ";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':id_representante', $id_representante);
        $stmt_insert->bindParam(':nombre_deportista', $nombre_deportista);
        $stmt_insert->bindParam(':fecha', $fecha);
        $stmt_insert->bindParam(':tipo_pago', $tipo_pago);
        $stmt_insert->bindParam(':monto', $monto);
        $stmt_insert->bindParam(':motivo', $motivo);
        $stmt_insert->execute();

        // Confirmar la transacción
        $conn->commit();

        // Llamar a historial_pagos.php para actualizar la tabla de pagos
        include 'historial_pagos.php';
    } catch (PDOException $e) {
        // Si hay algún error, hacer rollback y mostrar el mensaje de error
        $conn->rollback();
        echo 'Error al procesar el pago: ' . $e->getMessage();
    }
} else {
    // Si no se recibieron datos por POST, redirigir o mostrar un mensaje de error
    echo 'Error: No se recibieron datos por POST.';
}
?>
