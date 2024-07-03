<?php
include '../../configuracion/conexion.php'; // Asegúrate de que esta ruta es correcta

// Iniciar la transacción
$conn->beginTransaction();

try {
    // Verificar que las contraseñas coincidan
    if (empty($_POST['celular_a'])) {
        throw new Exception('El celular es obligatorio.');
    }

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    $hashed_password = password_hash($_POST['celular_a'], PASSWORD_DEFAULT); // Usando celular como contraseña

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

    /// Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo administrador: " . $nombre . " " . $apellido;
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    header("Location: ../crear_usuarios/cradmin.php?message=Registro exitoso"); // Redirige al formulario con un mensaje de éxito
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    header("Location: ../crear_usuarios/cradmin.php?message=" . urlencode($e->getMessage())); // Redirige al formulario con un mensaje de error
}

// Cerrar la conexión
$conn = null;
?>
