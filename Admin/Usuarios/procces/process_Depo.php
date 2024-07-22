<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_d'], $_POST['apellido_d'], $_POST['nacimiento_d'], $_POST['cedula_d'], $_POST['celular_d'], $_POST['genero'], $_POST['correo_d'], $_POST['categoria_d'], $_POST['representante'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Iniciar la transacción
    $conn->beginTransaction();

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    // Encriptar la contraseña
    $hashed_password = password_hash($_POST['cedula_d'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $_POST['nombre_d']);
    $stmt->bindParam(':pass', $hashed_password);

    $stmt->execute();

    $id_usuario = $conn->lastInsertId();

    $tipo = '4';

    // Insertar en tab_usu_tipo
    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam(':id_tipo', $tipo);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos en tab_deportistas
    $stmt = $conn->prepare('INSERT INTO tab_deportistas (id_usuario, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO, ID_CATEGORIA) 
    VALUES (:id_usuario, :NOMBRE_DEPO, :APELLIDO_DEPO, :FECHA_NACIMIENTO, :CEDULA_DEPO, :NUMERO_CELULAR, :GENERO, :ID_CATEGORIA)');
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':NOMBRE_DEPO', $_POST['nombre_d']);
    $stmt->bindParam(':APELLIDO_DEPO', $_POST['apellido_d']);
    $stmt->bindParam(':FECHA_NACIMIENTO', $_POST['nacimiento_d']);
    $stmt->bindParam(':CEDULA_DEPO', $_POST['cedula_d']);
    $stmt->bindParam(':NUMERO_CELULAR', $_POST['celular_d']);
    $stmt->bindParam(':GENERO', $_POST['genero']);
    $stmt->bindParam(':ID_CATEGORIA', $_POST['categoria_d']);
    
    $stmt->execute();

    // Aquí, el id_usuario de tab_deportistas es el mismo que id_usuario de tab_usuarios
    // No necesitamos obtener el último ID insertado, ya que es el mismo id_usuario

    // Insertar en tab_deportistas_representantes
    $stmt = $conn->prepare('INSERT INTO tab_representantes_deportistas (id_deportista, id_representante) VALUES (:id_deportista, :id_representante)');
    $stmt->bindParam(':id_deportista', $id_usuario);
    $stmt->bindParam(':id_representante', $_POST['representante']);
    $stmt->execute();

    // Confirmar la transacción
    $conn->commit();

    // Registrar la actividad en el log
    $evento = "Nuevo deportista registrado: " . $_POST['nombre_d'] . " " . $_POST['apellido_d'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    // Redirigir a la página de éxito
    header("Location: ../crear_usuarios/crdeportista.php?message=success");
    exit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    // Redirigir a la página de error con el mensaje de excepción
    header("Location: ../crear_usuarios/crdeportista.php?message=" . urlencode($e->getMessage()));
    exit();
}
?>
