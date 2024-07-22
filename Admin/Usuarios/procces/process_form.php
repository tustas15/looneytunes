<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre'], $_POST['apellido'], $_POST['experiencia'], $_POST['celular'], $_POST['correo'], $_POST['direccion'], $_POST['cedula'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Iniciar la transacción
    $conn->beginTransaction();

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    // Encriptar la contraseña
    $hashed_password = password_hash($_POST['cedula'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $_POST['nombre']);
    $stmt->bindParam(':pass', $hashed_password);
    
    $stmt->execute();

    $id_usuario = $conn->lastInsertId();

    $tipo = '2';

    // Insertar en tab_usu_tipo
    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam(':id_tipo', $tipo);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos en tab_entrenadores
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

    // Confirmar la transacción
    $conn->commit();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo entrenador: " . $_POST['nombre'] . " " . $_POST['apellido'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    // Redirigir con mensaje de éxito
    header("Location: ../crear_usuarios/crentrenador.php?message=success");
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    // Redirigir con mensaje de error
    header("Location: ../crear_usuarios/crentrenador.php?message=" . urlencode($e->getMessage()));
}

// Cerrar la conexión
$conn = null;
?>
