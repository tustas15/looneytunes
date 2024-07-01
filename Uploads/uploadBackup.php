<?php
require_once('../Admin/configuracion/conexion.php'); // Ajusta la ruta según sea necesario

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['backupFile']) && $_FILES['backupFile']['error'] == UPLOAD_ERR_OK) {
        $backupFile = $_FILES['backupFile']['tmp_name'];

        // Leer el contenido del archivo SQL
        $sql = file_get_contents($backupFile);

        // Verificar si el archivo se pudo leer correctamente
        if ($sql === false) {
            die("Error al leer el archivo de respaldo.");
        }

        // Dividir el contenido en múltiples declaraciones SQL
        $sqlCommands = explode(";", $sql);

        // Deshabilitar las verificaciones de clave externa
        $conn->query('SET foreign_key_checks = 0');

        // Obtener una lista de todas las tablas
        $tables = $conn->query("SHOW TABLES");

        if ($tables === false) {
            die("Error al obtener la lista de tablas: " . $conn->error);
        }

        // Vaciar todas las tablas antes de restaurar
        while ($row = $tables->fetch_row()) {
            $tableName = $row[0];
            $conn->query('TRUNCATE TABLE `' . $tableName . '`');
        }

        // Rehabilitar las verificaciones de clave externa
        $conn->query('SET foreign_key_checks = 1');

        // Ejecutar cada declaración SQL
        foreach ($sqlCommands as $command) {
            if (!empty(trim($command))) {
                if ($conn->query($command) === FALSE) {
                    echo "Error ejecutando la consulta: " . $conn->error . "<br>";
                }
            }
        }

        echo "Respaldo subido exitosamente";
    } else {
        echo "Error al subir el archivo.";
    }

    $conn->close();
}
