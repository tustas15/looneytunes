<?php
session_start();
require_once '../config/conexion.php';

$id_entrenador = $_SESSION['id_entrenador']; // Asegúrate de tener esta variable en la sesión

// Obtener la ruta del archivo más reciente para este entrenador
$sql = "SELECT RUTA_ARCHIVO FROM tab_hojas_vida WHERE ID_ENTRENADOR = ? ORDER BY FECHA_SUBIDA DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_entrenador);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $file_path = $row['RUTA_ARCHIVO'];
    
    if (file_exists($file_path)) {
        // Configurar headers para la descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));

        // Leer y enviar el archivo
        readfile($file_path);
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se encontró una hoja de vida para este entrenador.";
}

$stmt->close();