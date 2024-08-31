<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id_pago = $_POST['id'];

    try {
        // Verificar si el registro existe
        $sql_check = "SELECT COUNT(*) FROM tab_pagos WHERE ID_PAGO = :id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':id' => $id_pago]);
        $exists = $stmt_check->fetchColumn();

        if ($exists) {
            // Eliminar los registros dependientes en tab_estado_pagos
            $sql_delete_estado = "DELETE FROM tab_estado_pagos WHERE ID_PAGO = :id";
            $stmt_delete_estado = $conn->prepare($sql_delete_estado);
            $stmt_delete_estado->execute([':id' => $id_pago]);

            // Luego eliminar el registro en tab_pagos
            $sql_delete_pago = "DELETE FROM tab_pagos WHERE ID_PAGO = :id";
            $stmt_delete_pago = $conn->prepare($sql_delete_pago);
            $stmt_delete_pago->execute([':id' => $id_pago]);

            if ($stmt_delete_pago->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Pago y registros relacionados eliminados correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el pago']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El pago no existe']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la operación: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
?>
