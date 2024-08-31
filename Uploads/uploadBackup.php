<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Ajusta la ruta según sea necesario

session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['backupFile']) && $_FILES['backupFile']['error'] == UPLOAD_ERR_OK) {
        $backupFile = $_FILES['backupFile']['tmp_name'];
        $ip = $_SERVER['REMOTE_ADDR']; // Obtener la IP del cliente
        $userId = $_SESSION['user_id']; // Asegúrate de tener el ID del usuario en la sesión

        try {
            // Deshabilitar las verificaciones de clave externa
            $conn->query('SET foreign_key_checks = 0');

            // Eliminar todas las tablas antes de restaurar
            $tables = $conn->query("SHOW TABLES");
            if ($tables === false) {
                $_SESSION['message'] = "Error al obtener la lista de tablas.";
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            while ($row = $tables->fetch(PDO::FETCH_ASSOC)) {
                $tableName = $row['Tables_in_looneytunes']; // Ajusta según el nombre de tu base de datos
                $conn->query('DROP TABLE IF EXISTS `' . $tableName . '`');
            }

            // Leer y ejecutar el archivo SQL completo
            $sql = file_get_contents($backupFile);
            if ($conn->exec($sql) === false) {
                $_SESSION['message'] = "Error ejecutando la restauración: " . $conn->errorInfo()[2];
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // Rehabilitar las verificaciones de clave externa
            $conn->query('SET foreign_key_checks = 1');

            $_SESSION['message'] = "Respaldo subido exitosamente";
            $_SESSION['message_type'] = 'success';

            // Registrar la actividad de restauración en el log
            $evento = "Respaldo subido y restaurado exitosamente";
            $tipo_evento = 'subida_base_datos'; // Define el tipo de evento

            $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->execute([$userId, $evento, $ip, $tipo_evento]);

        } catch (PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }

        // Cerrar la conexión
        $conn = null;

        // Redirigir de vuelta a la página anterior
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $_SESSION['message'] = "Error al subir el archivo.";
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
