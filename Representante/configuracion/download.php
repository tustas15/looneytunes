<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}
require_once('../../admin/configuracion/conexion.php'); 

// Verificar si se ha proporcionado el id_usuario a través de la URL
$idUsuarioEntrenador = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;

if ($idUsuarioEntrenador) {
    // Consulta para obtener el archivo PDF asociado con el id_usuario del entrenador
    $sql = "SELECT p.file_name, p.file_path 
            FROM tab_pdfs p
            INNER JOIN tab_entrenadores e ON p.id_usuario = e.id_usuario
            WHERE e.id_usuario = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $idUsuarioEntrenador, PDO::PARAM_INT);
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

            //Nombre del entrenador
            $sql = "SELECT e.nombre_entre 
            FROM tab_entrenadores e
            WHERE e.id_usuario = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $idUsuarioEntrenador, PDO::PARAM_INT);
    $stmt->execute();
    $entre_nom = $stmt->fetch(PDO::FETCH_ASSOC);

            $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Descarga pdf del entrenador ".$entre_nom['nombre_entre'];
    $tipo_evento = "descarga_pdf";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);
            exit;
        } else {
            echo "El archivo no se encuentra en el servidor.";
        }
    } else {
        echo "No se encontró un archivo PDF para este usuario del entrenador.";
    }
} else {
    echo "No se proporcionó un ID de usuario válido.";
}
?>
