<?php
include '../conexion/conexion.php';

try {
    // Iniciar la transacción
    $conn->beginTransaction();

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    $hashed_password = password_hash($_POST['cedula_r'], PASSWORD_DEFAULT);

    // Bind de parámetros
    $stmt->bindParam(':usuario', $_POST['nombre_r']);
    $stmt->bindParam(':pass', $hashed_password);
    $stmt->execute();

    $id_usuario = $conn->lastInsertId();

    $tipo = '3';

    // Preparar la consulta SQL para insertar en tab_usu_tipo
    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam(':id_tipo', $tipo);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos en tab_administradores
    $stmt = $conn->prepare('INSERT INTO tab_representantes (ID_USUARIO, NOMBRE_REPRE, APELLIDO_REPRE, CELULAR_REPRE, CORREO_REPRE, DIRECCION_REPRE, CEDULA_REPRE) 
    VALUES (:ID_USUARIO, :NOMBRE_REPRE, :APELLIDO_REPRE, :CELULAR_REPRE, :CORREO_REPRE, :DIRECCION_REPRE, :CEDULA_REPRE)');
    
    // Bind de parámetros
    $stmt->bindParam(':ID_USUARIO', $id_usuario);
    $stmt->bindParam(':NOMBRE_REPRE', $_POST['nombre_r']);
    $stmt->bindParam(':APELLIDO_REPRE', $_POST['apellido_r']);
    $stmt->bindParam(':CELULAR_REPRE', $_POST['celular_r']);
    $stmt->bindParam(':CORREO_REPRE', $_POST['correo_r']);
    $stmt->bindParam(':DIRECCION_REPRE', $_POST['direccion_r']);
    $stmt->bindParam(':CEDULA_REPRE', $_POST['cedula_r']);
    $stmt->execute();

    // Confirmar la transacción
    $conn->commit();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo representante: " . $_POST['nombre_r'] . " " . $_POST['apellido_r'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip]);

    echo '<div style="margin: 20px; padding: 20px; border: 1px solid #4CAF50; background-color: #DFF2BF; color: #4CAF50; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
            Registro exitoso
          </div>';
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo '<div style="margin: 20px; padding: 20px; border: 1px solid #FF0000; background-color: #FFBABA; color: #D8000C; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
            Error: ' . htmlspecialchars($e->getMessage()) . '
          </div>';
}

// Cerrar la conexión
$conn = null;
?>
