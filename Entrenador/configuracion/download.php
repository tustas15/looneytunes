<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Asegúrate de que este archivo contiene la conexión a la base de datos con PDO.

$idUsuario = $_SESSION['user_id'];

$sql = "SELECT file_name, file_path FROM tab_pdfs WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $idUsuario);
$stmt->execute();

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if ($file) {
    $fileName = $file['file_name'];
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $file['file_path'];

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "El archivo no se encuentra en el servidor.";
    }
} else {
    echo "No se encontró un archivo PDF para este usuario.";
}
?>
