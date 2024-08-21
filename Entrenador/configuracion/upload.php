<?php
session_start();
require_once('../../admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idUsuario = $_SESSION['user_id']; 
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/entrenador/pdfs/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Genera el nombre del archivo
    $fileName = basename($idUsuario . '_cv.pdf'); 
    $uploadFile = $uploadDir . $fileName;

    // Elimina el registro anterior con el mismo id_usuario
    $deleteSql = "DELETE FROM tab_pdfs WHERE id_usuario = :id_usuario";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':id_usuario', $idUsuario);
    $deleteStmt->execute();

    // Subir el archivo
    if (move_uploaded_file($_FILES['cvFile']['tmp_name'], $uploadFile)) {
        $filePath = '/entrenador/pdfs/' . $fileName;

        // Inserta el nuevo registro
        $sql = "INSERT INTO tab_pdfs (id_usuario, file_name, file_path) VALUES (:id_usuario, :file_name, :file_path)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':file_name', $fileName);
        $stmt->bindParam(':file_path', $filePath);

        if ($stmt->execute()) {
            $message = "El archivo se ha subido correctamente.";
        } else {
            $message = "Hubo un error al guardar la información en la base de datos.";
        }
    } else {
        $message = "Hubo un error al subir el archivo.";
    }
    
    // Redirigir a indexentrenador.php con el mensaje
    header("Location: ../entrenador/indexentrenador.php?message=" . urlencode($message));
    exit();
}
?>
