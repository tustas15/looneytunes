<?php
include '../conexion/conexion.php';

try {
    // Preparar la consulta SQL para insertar los datos en usuario
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    // Encriptar la contraseña
    $hashed_password = password_hash($_POST['cedula'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $_POST['nombre']);
    $stmt->bindParam(':pass', $hashed_password);
    
    $stmt->execute();

    $id_usuario= $conn->lastInsertId();

    $tipo='2';

    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam(':id_tipo', $tipo);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos
    $stmt = $conn->prepare('INSERT INTO tab_entrenadores (id_usuario, nombre_entre, apellido_entre, experiencia_entre, celular_entre, correo_entre, direccion_entre, cedula_entre) 
    VALUES (:id_usuario, :nombre_entre, :apellido_entre, :experiencia_entre, :celular_entre, :correo_entre, :direccion_entre, :cedula_entre)');
    // Bind de parámetros
    
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':nombre_entre', $_POST['nombre']);
    $stmt->bindParam(':apellido_entre', $_POST['apellido']);
    $stmt->bindParam(':experiencia_entre', $_POST['experiencia']);
    $stmt->bindParam(':celular_entre', $_POST['celular']);
    $stmt->bindParam(':correo_entre', $_POST['correo']);
    $stmt->bindParam(':direccion_entre', $_POST['direccion']);
    $stmt->bindParam(':cedula_entre', $_POST['cedula']);

    // Ejecutar la consulta
    $stmt->execute();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo entrenador: " . $_POST['nombre'] . " " . $_POST['apellido'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip]);

    echo '<div style="margin: 20px; padding: 20px; border: 1px solid #4CAF50; background-color: #DFF2BF; color: #4CAF50; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
            Registro exitoso
          </div>';
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
