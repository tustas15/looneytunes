<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Asegúrate de que la sesión tiene el tipo de usuario definido
$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
if (!$tipo_usuario) {
    $response = ['success' => false, 'message' => 'Tipo de usuario no definido.'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si la variable de sesión ID_DEPORTISTA está definida
    $id_deportista = $_SESSION['user_id'] ?? '';
    if (empty($id_deportista)) {
        $response = ['success' => false, 'message' => 'ID de deportista no definido en la sesión.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
$tipo_usuario = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'DEPO';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_representante = $_POST['representante'] ?? '';
    $id_deportista = $_SESSION['user_id'] ?? ''; // Corrección aquí
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $fecha_pago = $_POST['fecha'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $nombre_archivo = '';
    $entidad_origen = $_POST['entidad_origen'] ?? '';
    $tipo_evento = 'nuevo_pago_agregado'; // Tipo de evento para log

    error_log("Datos recibidos: " . print_r($_POST, true));
    error_log("Archivos recibidos: " . print_r($_FILES, true));

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

            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
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
        $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, ID_BANCO, METODO_PAGO, MONTO, FECHA_PAGO, MOTIVO, NOMBRE_ARCHIVO, ENTIDAD_ORIGEN, REGISTRADO_POR) 
                VALUES (:id_representante, :id_deportista, :id_banco, :metodo_pago, :monto, :fecha_pago, :motivo, :nombre_archivo, :entidad_origen, :tipo_usuario)";
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
            ':entidad_origen' => $entidad_origen,
            ':tipo_usuario' => $tipo_usuario
        ]);

        $id_pago = $conn->lastInsertId(); // Obtener el ID del pago recién insertado

        // Obtener el ID_CATEGORIA basándose en el ID_DEPORTISTA
        $stmt_categoria = $conn->prepare("SELECT ID_CATEGORIA FROM tab_categoria_deportista WHERE ID_DEPORTISTA = :id_deportista");
        $stmt_categoria->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt_categoria->execute();
        $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);
        $id_categoria = $categoria['ID_CATEGORIA'] ?? null;

        if (!$id_categoria) {
            throw new Exception('No se encontró la categoría para el deportista');
        }

        // Determinar el estado del pago basado en la fecha de pago
        $fecha_pago_datetime = new DateTime($fecha_pago);
        $estado = ($fecha_pago_datetime->format('d') <= 8) ? 'Pagado' : 'Pago Atrasado';

        // Insertar en tab_estado_pagos
        $sql_estado = "INSERT INTO tab_estado_pagos (ID_DEPORTISTA, ID_CATEGORIA, ID_PAGO, FECHA, ESTADO)
                       VALUES (:id_deportista, :id_categoria, :id_pago, :fecha, :estado)";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->execute([
            ':id_deportista' => $id_deportista,
            ':id_categoria' => $id_categoria,
            ':id_pago' => $id_pago,
            ':fecha' => $fecha_pago,
            ':estado' => $estado
        ]);

        // Registrar el evento en log
        $stmt = $conn->prepare("SELECT NOMBRE_DEPO FROM tab_deportistas WHERE ID_DEPORTISTA = :id_deportista");
        $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt->execute();
        $nom_repre = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nom_repre) {
            throw new Exception('No se encontró el deportista para registrar el evento');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $evento = "Pago registrado para " . $nom_repre['NOMBRE_DEPO'];
        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);

        $response = [
            'success' => true,
            'message' => 'Pago registrado correctamente'
        ];
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());

        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage());

        $response = [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}}