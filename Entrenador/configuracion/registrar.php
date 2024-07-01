<?php
session_start();
include '../Admin/configuracion/conexion.php';

if (isset($_POST['cedula_r'])) {
    $cedula_r = $_POST['cedula_r'];
    $id_usuario = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($id_usuario && isset($conn)) {
        try {
            // Verificar si el deportista ya está en la tabla principal
            $sql = "SELECT * FROM TAB_DEPORTISTAS WHERE CEDULA_DEPO = :cedula";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cedula', $cedula_r, PDO::PARAM_STR);
            $stmt->execute();
            $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($studentData) {
                // Agregar a la tabla temporal
                $sql_insert = "INSERT INTO TAB_TEMP_DEPORTISTAS (ID_USUARIO, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO)
                               VALUES (:id_usuario, :nombre, :apellido, :fecha_nacimiento, :cedula, :numero_celular, :genero)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt_insert->bindParam(':nombre', $studentData['NOMBRE_DEPO'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':apellido', $studentData['APELLIDO_DEPO'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':fecha_nacimiento', $studentData['FECHA_NACIMIENTO'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':cedula', $studentData['CEDULA_DEPO'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':numero_celular', $studentData['NUMERO_CELULAR'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':genero', $studentData['GENERO'], PDO::PARAM_STR);
                $stmt_insert->execute();

                // Registro del evento en tab_logs
                $evento = "Registro de deportista con cédula: $cedula_r";
                $ip = $_SERVER['REMOTE_ADDR'];
                $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), ?)";
                $stmt_log = $conn->prepare($query);
                $stmt_log->execute([$id_usuario, $evento, $ip]);

                // Actualizar la variable de sesión 'deportistas'
                $_SESSION['deportistas'][] = [
                    'NOMBRE_DEPO' => $studentData['NOMBRE_DEPO'],
                    'APELLIDO_DEPO' => $studentData['APELLIDO_DEPO'],
                    'FECHA_NACIMIENTO' => $studentData['FECHA_NACIMIENTO'],
                    'CEDULA_DEPO' => $studentData['CEDULA_DEPO'],
                    'NUMERO_CELULAR' => $studentData['NUMERO_CELULAR'],
                    'GENERO' => $studentData['GENERO']
                ];

                $_SESSION['success'] = "Deportista registrado correctamente.";
            } else {
                $_SESSION['error'] = "Deportista no encontrado.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "ID de usuario no encontrado o conexión a la base de datos no establecida.";
    }
}

header('Location: ./indexEntrenador.php');
exit;
?>
