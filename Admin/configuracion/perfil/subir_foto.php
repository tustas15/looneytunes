<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $foto = $_FILES['foto_perfil'];

    if ($foto['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($foto['name']);
        $ruta_archivo = 'uploads/' . $nombre_archivo;

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($foto['tmp_name'], $ruta_archivo)) {
            // Guardar la información en la base de datos
            $pdo = new PDO('mysql:host=localhost;dbname=looneytunes', 'usuario', 'contraseña');
            $sql = "INSERT INTO tab_fotos_perfil (ID_USUARIO, NOMBRE_ARCHIVO, RUTA_ARCHIVO) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario, $nombre_archivo, $ruta_archivo]);

            echo "Foto subida exitosamente.";
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "Error al subir el archivo.";
    }
}
?>
