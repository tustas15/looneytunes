<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_d'], $_POST['apellido_d'], $_POST['nacimiendo_d'], $_POST['cedula_d'], $_POST['celular_d'], $_POST['genero'], $_POST['correo_d'], $_POST['representante'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

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
    $stmt = $conn->prepare('INSERT INTO tab_deportistas (id_usuario, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO, CORREO) 
    VALUES (:id_usuario, :NOMBRE_DEPO, :APELLIDO_DEPO, :FECHA_NACIMIENTO, :CEDULA_DEPO, :NUMERO_CELULAR, :GENERO, :CORREO)');
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':NOMBRE_DEPO', $_POST['nombre_d']);
    $stmt->bindParam(':APELLIDO_DEPO', $_POST['apellido_d']);
    $stmt->bindParam(':FECHA_NACIMIENTO', $_POST['nacimiendo_d']);
    $stmt->bindParam(':CEDULA_DEPO', $_POST['cedula_d']);
    $stmt->bindParam(':NUMERO_CELULAR', $_POST['celular_d']);
    $stmt->bindParam(':GENERO', $_POST['genero']);
    $stmt->bindParam(':CORREO', $_POST['correo_d']);

    // Ejecutar la consulta
    $stmt->execute();

    // Asignar el representante al deportista en la tabla tab_representantes_deportistas
    $id_representante = $_POST['representante'];
    $stmt = $conn->prepare('INSERT INTO tab_representantes_deportistas (ID_REPRESENTANTE, ID_DEPORTISTA) VALUES (:ID_REPRESENTANTE, :ID_DEPORTISTA)');
    $stmt->bindParam(':ID_REPRESENTANTE', $id_representante);
    $stmt->bindParam(':ID_DEPORTISTA', $id_usuario);
    $stmt->execute();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo deportista: " . $nombre . " " . $apellido;
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    // Redirigir con mensaje de éxito
    header("Location: ../crdeportista.php?message=success");
} catch (Exception $e) {
    // Redirigir con mensaje de error
    header("Location: ../crdeportista.php?message=" . urlencode($e->getMessage()));
}

// Cerrar la conexión
$conn = null;
?>
