<?php
include '../../configuracion/conexion.php';

try {
    // Iniciar la transacción
    $conn->beginTransaction();

    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_r'], $_POST['apellido_r'], $_POST['celular_r'], $_POST['correo_r'], $_POST['direccion_r'], $_POST['cedula_r'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

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

    // Preparar la consulta SQL para insertar los datos en tab_representantes
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
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    // Redirigir con mensaje de éxito
    header("Location: ../crear_usuarios/crrepresentante.php?message=success");
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    // Redirigir con mensaje de error
    header("Location: ../crear_usuarios/crrepresentante.php?message=" . urlencode($e->getMessage()));
}

// Cerrar la conexión
$conn = null;
?>
