<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (!isset($_SESSION['id_entrenador'])) {
    die("Error: No se ha iniciado sesión como entrenador.");
}

$id_entrenador = $_SESSION['id_entrenador'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['cvFile'])) {
    $file = $_FILES['cvFile'];

    // Verificar el tipo de archivo
    $allowed_types = ['application/pdf'];
    if (!in_array($file['type'], $allowed_types)) {
        die("Error: Solo se permiten archivos PDF.");
    }

    // Generar un nombre único para el archivo
    $file_name = uniqid() . '_' . $file['name'];
    $upload_dir = '../../uploads/cv/';
    
    // Crear el directorio si no existe
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_path = $upload_dir . $file_name;

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Insertar la información en la base de datos
        $sql = "INSERT INTO tab_hojas_vida (ID_ENTRENADOR, NOMBRE_ARCHIVO, RUTA_ARCHIVO) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iss", $id_entrenador, $file['name'], $file_path);
        
        if ($stmt->execute()) {
            echo "Hoja de vida subida con éxito.";
        } else {
            echo "Error al guardar la información en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error al subir el archivo. Código de error: " . $file['error'];
    }
} else {
    echo "Solicitud no válida.";
}