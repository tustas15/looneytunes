<?php
session_start();
require_once('../../admin/configuracion/conexion.php'); 

$idUsuario = $_SESSION['user_id'];

$sql = "SELECT p.file_name, p.file_path, e.id_entrenador 
FROM tab_pdfs p
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
    $idEntrenador = $file['id_entrenador']; // Obtén el id_entrenador

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        
        // Obtener el nombre del entrenador usando el id_entrenador
        $sql = "SELECT e.nombre_entre 
                FROM tab_entrenadores e
                WHERE e.id_entrenador = :id_entrenador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_entrenador', $idEntrenador, PDO::PARAM_INT);
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
    echo "No se encontró un archivo PDF para este usuario.";
}
?>
