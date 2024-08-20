<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Asegúrate de que este archivo contiene la conexión a la base de datos con PDO.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idUsuario = $_SESSION['user_id']; // Obtiene el id del usuario desde la sesión.
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/looneytunes/entrenador/pdfs/';

    // Verifica si la carpeta existe, si no, la crea.
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($idUsuario . '_cv.pdf'); // El nombre del archivo.
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['cvFile']['tmp_name'], $uploadFile)) {
        // El archivo se subió correctamente, ahora lo guardamos en la base de datos.
        $filePath = '/looneytunes/entrenador/pdfs/' . $fileName;

        // Inserta en la base de datos
        $sql = "INSERT INTO tab_pdfs (id_usuario, file_name, file_path) VALUES (:id_usuario, :file_name, :file_path)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':file_name', $fileName);
        $stmt->bindParam(':file_path', $filePath);

        if ($stmt->execute()) {
            echo "El archivo se ha subido y guardado en la base de datos correctamente.";
        } else {
            echo "Hubo un error al guardar la información en la base de datos.";
        }
    } else {
        echo "Hubo un error al subir el archivo.";
    }
}
?>
