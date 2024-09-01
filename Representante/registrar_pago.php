<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verifica que el ID del usuario esté disponible en la sesión
if (!isset($_SESSION['user_id'])) {
    die("ID de usuario no disponible en la sesión.");
}
$id_usuario = $_SESSION['user_id'];

// Paso 1: Obtener el ID del representante usando el ID del usuario
$sql_deportistas = "SELECT ID_DEPORTISTA 
                      FROM tab_deportistas 
                      WHERE ID_USUARIO = :id_usuario";

try {
    $stmt = $conn->prepare($sql_deportistas);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deportista) {
        die("No se encontró el representante para el usuario actual.");
    }

    $id_deportista = $deportista['ID_DEPORTISTA'];

    // Paso 2: Obtener la lista de representantes asociados al deportista
    $sql_representantes = "SELECT d.ID_REPRESENTANTE, d.NOMBRE_REPRE, d.APELLIDO_REPRE 
                        FROM tab_representantes d
                        INNER JOIN tab_representantes_deportistas rd ON d.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
                        WHERE rd.ID_DEPORTISTA = :id_deportista";

    $stmt = $conn->prepare($sql_representantes);
    $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($representantes)) {
        echo "No se encontraron deportistas para el representante actual.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_deportista = $_POST['deportista'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $fecha_pago = $_POST['fecha'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $nombre_archivo = '';
    $entidad_origen = $_POST['entidad_origen'] ?? '';
    $tipo_usuario = $_SESSION['user_type'] ?? 'REPRE';
    
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

        // Determinar el estado del pago
        $fecha_pago_datetime = new DateTime($fecha_pago);
        $estado = ($fecha_pago_datetime->format('d') <= 8) ? 'Pagado' : 'Pago Atrasado';

        // Obtener el ID_CATEGORIA basándose en el ID_DEPORTISTA
        $stmt_categoria = $conn->prepare("SELECT ID_CATEGORIA FROM tab_categoria_deportista WHERE ID_DEPORTISTA = :id_deportista");
        $stmt_categoria->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt_categoria->execute();
        $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);
        $id_categoria = $categoria['ID_CATEGORIA'] ?? null;

        // Insertar en la tabla tab_estado_pagos
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

        // Registro de log
        $stmt = $conn->prepare("SELECT NOMBRE_REPRE from tab_representantes where ID_REPRESENTANTE = :id_representante");
        $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
        $stmt->execute();
        $nom_repre = $stmt->fetch(PDO::FETCH_ASSOC);

        $ip = $_SERVER['REMOTE_ADDR'];
        $evento = "Pago registrado " . $nom_repre['NOMBRE_REPRE'];
        $tipo_evento = "nuevo_pago_agregado";
        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);

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
