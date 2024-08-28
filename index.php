<?php
session_start();
require_once('./Admin/configuracion/conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge el ID_TIPO del formulario
    $id_tipo = $_POST['id_tipo'];

    // Verifica y procesa el archivo subido
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);

        // Inserta o actualiza la foto en la base de datos
        $sql = "INSERT INTO tab_fotos_usuario (ID_TIPO, FOTO) VALUES (:id_tipo, :foto)
                ON DUPLICATE KEY UPDATE FOTO = :foto";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id_tipo' => $id_tipo, 'foto' => $foto]);

        echo "Foto subida exitosamente.";
    } else {
        echo "Error al subir la foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Foto para Tipo de Usuario</title>
</head>
<body>
    <h1>Subir Foto para Tipo de Usuario</h1>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="id_tipo">Seleccione Tipo de Usuario:</label>
        <select name="id_tipo" id="id_tipo" required>
            <option value="1">Administrador</option>
            <option value="2">Entrenador</option>
            <option value="3">Representante</option>
            <option value="4">Deportista</option>
        </select>
        <br><br>

        <label for="foto">Subir Foto:</label>
        <input type="file" name="foto" id="foto" accept="image/*" required>
        <br><br>

        <input type="submit" value="Subir Foto">
    </form>
</body>
</html>
