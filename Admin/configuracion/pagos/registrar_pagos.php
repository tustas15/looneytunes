<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_representante = $_POST['representante'] ?? '';
    $id_deportista = $_POST['deportista'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $fecha_pago = $_POST['fecha'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $nombre_archivo = '';
    $entidad_origen = $_POST['entidad_origen'] ?? '';
    $tipo_evento = 'nuevo_pago_agregado'; // Tipo de evento para log
    $id_usuario = $_SESSION['user_id'] ?? null; // ID del usuario desde la sesión

    // Si el método de pago es efectivo, se asigna el ID del banco "placeholder"
    if ($metodo_pago === 'efectivo') {
        $id_banco = 0; // ID del banco "Efectivo"
    } else {
        $id_banco = $_POST['banco'] ?? '';
        if (isset($_FILES['nombre_archivo'])) {
            $archivo = $_FILES['nombre_archivo'];
            $nombre_archivo = $archivo['name'];
            $ruta_destino = 'C:/xampp/htdocs/looneytunes/Admin/configuracion/pagos/comprobantes/';
            $ruta_completa = $ruta_destino . $nombre_archivo;

            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)){
                $response = [
                    'success' => false,
                    'message' => 'Por favor, ingresa el comprobante'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        } elseif ($metodo_pago === 'efectivo') {
            // No se maneja archivo para efectivo
            $nombre_archivo = null; // No se usa archivo
            $entidad_origen = null;
        }
    }

    try {
        // Insertar en tab_pagos
        $sql_pagos = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, ID_BANCO, METODO_PAGO, MONTO, FECHA_PAGO, MOTIVO, NOMBRE_ARCHIVO, ENTIDAD_ORIGEN) 
                      VALUES (:id_representante, :id_deportista, :id_banco, :metodo_pago, :monto, :fecha_pago, :motivo, :nombre_archivo, :entidad_origen)";
        $stmt_pagos = $conn->prepare($sql_pagos);
        $stmt_pagos->execute([
            ':id_representante' => $id_representante,
            ':id_deportista' => $id_deportista,
            ':id_banco' => $id_banco,
            ':metodo_pago' => $metodo_pago,
            ':monto' => $monto,
            ':fecha_pago' => $fecha_pago,
            ':motivo' => $motivo,
            ':nombre_archivo' => $nombre_archivo,
            ':entidad_origen' => $entidad_origen
        ]);

        // Obtener el ID del último pago registrado
        $last_insert_id = $conn->lastInsertId();

        // Insertar en tab_logs
        $sql_logs = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) 
                     VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)";
        $stmt_logs = $conn->prepare($sql_logs);
        $stmt_logs->execute([
            ':id_usuario' => $id_usuario,
            ':evento' => "Nuevo pago registrado con ID: $last_insert_id",
            ':hora_log' => date('H:i:s'),
            ':dia_log' => date('Y-m-d'),
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':tipo_evento' => $tipo_evento
        ]);

        $response = [
            'success' => true,
            'message' => 'Pago registrado correctamente'
        ];
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'message' => 'Error al registrar el pago: ' . $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
