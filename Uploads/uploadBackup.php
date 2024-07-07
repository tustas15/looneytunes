<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Ajusta la ruta según sea necesario

session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['backupFile']) && $_FILES['backupFile']['error'] == UPLOAD_ERR_OK) {
        $backupFile = $_FILES['backupFile']['tmp_name'];

        // Leer el contenido del archivo SQL
        $sql = file_get_contents($backupFile);

        // Verificar si el archivo se pudo leer correctamente
        if ($sql === false) {
            $_SESSION['message'] = "Error al leer el archivo de respaldo.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Dividir el contenido en múltiples declaraciones SQL
        $sqlCommands = explode(";", $sql);

        try {
            // Deshabilitar las verificaciones de clave externa
            $conn->query('SET foreign_key_checks = 0');

            // Vaciar todas las tablas antes de restaurar
            $tables = $conn->query("SHOW TABLES");
            if ($tables === false) {
                $_SESSION['message'] = "Error al obtener la lista de tablas.";
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            while ($row = $tables->fetch(PDO::FETCH_ASSOC)) {
                $tableName = $row['Tables_in_looneytunes']; // Obtener el nombre de la tabla usando FETCH_ASSOC
                $conn->query('TRUNCATE TABLE `' . $tableName . '`');
            }

            // Ejecutar cada declaración SQL
            foreach ($sqlCommands as $command) {
                if (!empty(trim($command))) {
                    if ($conn->exec($command) === false) {
                        $_SESSION['message'] = "Error ejecutando la consulta: " . $conn->errorInfo()[2];
                        $_SESSION['message_type'] = 'danger';
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                        exit();
                    }
                }
            }

            // Rehabilitar las verificaciones de clave externa
            $conn->query('SET foreign_key_checks = 1');

            $_SESSION['message'] = "Respaldo subido exitosamente";
            $_SESSION['message_type'] = 'success';
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