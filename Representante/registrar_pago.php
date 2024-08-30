<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_representante = $_SESSION['tipo_usuario'] ?? null;
    $id_deportista = $_POST['deportista'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $fecha_pago = $_POST['fecha'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $nombre_archivo = '';
    $entidad_origen = $_POST['entidad_origen'] ?? '';

    if ($metodo_pago === 'efectivo') {
        $id_banco = 0; // ID del banco "Efectivo"
    } else {
        $id_banco = $_POST['banco'] ?? '';
        if (isset($_FILES['nombre_archivo'])) {
            $archivo = $_FILES['nombre_archivo'];
            $nombre_archivo = $archivo['name'];
            $ruta_destino = 'C:/xampp/htdocs/looneytunes/Admin/configuracion/pagos/comprobantes/';
            $ruta_completa = $ruta_destino . $nombre_archivo;

            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al subir el comprobante'
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Falta el comprobante para el pago por transferencia'
            ]);
            exit;
        }
    }

    try {
        $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, ID_BANCO, METODO_PAGO, MONTO, FECHA_PAGO, MOTIVO, NOMBRE_ARCHIVO, ENTIDAD_ORIGEN) 
                VALUES (:id_representante, :id_deportista, :id_banco, :metodo_pago, :monto, :fecha_pago, :motivo, :nombre_archivo, :entidad_origen)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
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
        $stmt = $conn->prepare("SELECT NOMBRE_REPRE from tab_representantes where ID_REPRESENTANTE = :id_representante");
        $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
        $stmt->execute();
        $nom_repre = $stmt->fetch(PDO::FETCH_ASSOC);

        $ip = $_SERVER['REMOTE_ADDR'];
        $evento = "Pago registrado " . $nom_repre['NOMBRE_REPRE'];
        $tipo_evento = "nuevo_pago_agregado";
        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP,TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?,?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);

        echo json_encode([
            'success' => true,
            'message' => 'Pago registrado correctamente'



        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar el pago: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
