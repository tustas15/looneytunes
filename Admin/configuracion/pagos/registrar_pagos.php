<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
$tipo_usuario = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'ADMIN';


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
        $sql_pagos = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, ID_BANCO, METODO_PAGO, MONTO, FECHA_PAGO, MOTIVO, NOMBRE_ARCHIVO, ENTIDAD_ORIGEN,REGISTRADO_POR) 
                      VALUES (:id_representante, :id_deportista, :id_banco, :metodo_pago, :monto, :fecha_pago, :motivo, :nombre_archivo, :entidad_origen,:tipo_usuario)";
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
            ':entidad_origen' => $entidad_origen,
            ':tipo_usuario' => $tipo_usuario

        ]);

        $id_pago = $conn->lastInsertId(); // Obtener el ID del pago recién insertado

        // Determinar el estado del pago
        $dia_pago = 8; // Día límite de pago
        $estado = '';

        $fecha_actual = date('Y-m-d');
        $dia_actual = date('d', strtotime($fecha_actual));

        if ($dia_actual < $dia_pago) {
            $estado = 'pagado';
        } elseif ($dia_actual == $dia_pago) {
            $estado = 'mes no pagado';
        } else {
            $estado = 'pago retrasado';
        }


 // Obtener el ID_CATEGORIA basándose en el ID_DEPORTISTA
 $stmt_categoria = $conn->prepare("SELECT ID_CATEGORIA FROM tab_categoria_deportista WHERE ID_DEPORTISTA = :id_deportista");
 $stmt_categoria->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
 $stmt_categoria->execute();
 $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);
 $id_categoria = $categoria['ID_CATEGORIA'] ?? null;

 // Determinar el estado del pago
 $fecha_pago_datetime = new DateTime($fecha_pago);
 $estado = ($fecha_pago_datetime->format('d') <= 8) ? 'Pagado' : 'Pago Atrasado';


        // Insertar en la tabla tab_estado_pagos
        $sql_estado = "INSERT INTO tab_estado_pagos (ID_DEPORTISTA, ID_CATEGORIA, ID_PAGO, FECHA, ESTADO)
                       VALUES (:id_deportista, :id_categoria, :id_pago, :fecha, :estado)";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->execute([
            ':id_deportista' => $id_deportista,
            ':id_categoria' => $id_categoria, // Asegúrate de tener este valor disponible
            ':id_pago' => $id_pago,
            ':fecha' => $fecha_pago,
            ':estado' => $estado
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

    $stmt = $conn->prepare("SELECT NOMBRE_REPRE from tab_representantes where ID_REPRESENTANTE = :id_representante");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $nom_repre = $stmt->fetch(PDO::FETCH_ASSOC);

    $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Nuevo Pago de " . $nom_repre['NOMBRE_REPRE'];
    $tipo_evento = "nuevo_pago_agregado";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP,TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);


    echo json_encode($response);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
