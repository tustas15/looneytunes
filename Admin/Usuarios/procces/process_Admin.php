<?php
include './Admin/configuracion/conexion.php';

try {
    // Verificar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['confirm_password']) {
        throw new Exception('Las contraseñas no coinciden.');
    }

    // Iniciar la transacción
    $conn->beginTransaction();

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Bind de parámetros
    $stmt->bindParam(':usuario', $_POST['nombre_a']);
    $stmt->bindParam(':pass', $hashed_password);
    $stmt->execute();

    $id_usuario = $conn->lastInsertId();

    $tipo = '1';

    // Preparar la consulta SQL para insertar en tab_usu_tipo
    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam(':id_tipo', $tipo);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos en tab_administradores
    $stmt = $conn->prepare('INSERT INTO tab_administradores (ID_USUARIO, NOMBRE_ADMIN, APELLIDO_ADMIN, CELULAR_ADMIN) 
        VALUES (:ID_USUARIO, :NOMBRE_ADMIN, :APELLIDO_ADMIN, :CELULAR_ADMIN)');

    // Bind de parámetros
    $stmt->bindParam(':ID_USUARIO', $id_usuario);
    $stmt->bindParam(':NOMBRE_ADMIN', $_POST['nombre_a']);
    $stmt->bindParam(':APELLIDO_ADMIN', $_POST['apellido_a']);
    $stmt->bindParam(':CELULAR_ADMIN', $_POST['celular_a']);
    $stmt->execute();

    // Confirmar la transacción
    $conn->commit();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de cuenta de administrador: " . $_POST['nombre_a'] . " " . $_POST['apellido_a'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP_USUARIO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip]);

    header("Location: cradmin.php?message=success"); // Redirige al formulario con un mensaje de éxito
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    header("Location: cradmin.php?message=" . urlencode($e->getMessage())); // Redirige al formulario con un mensaje de error
}

// Cerrar la conexión
$conn = null;
