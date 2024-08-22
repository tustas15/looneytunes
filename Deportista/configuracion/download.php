<?php
session_start();
require_once('../../admin/configuracion/conexion.php'); 

$idUsuario = $_SESSION['user_id'];

$sql = "SELECT p.file_name, p.file_path FROM tab_pdfs p
INNER JOIN tab_usuarios u ON p.id_usuario = u.id_usuario
INNER JOIN tab_entrenadores e ON u.id_usuario = e.id_usuario
INNER JOIN tab_entrenador_categoria ec ON e.id_entrenador = ec.id_entrenador
INNER JOIN tab_categoria_deportista ca ON ec.id_categoria = ca.id_categoria
INNER JOIN tab_deportistas d ON ca.id_deportista = d.id_deportista
WHERE d.id_usuario = :id_usuario";
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
