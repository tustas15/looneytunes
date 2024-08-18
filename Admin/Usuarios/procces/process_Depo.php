<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_d'], $_POST['apellido_d'], $_POST['nacimiento_d'], $_POST['cedula_d'], $_POST['celular_d'], $_POST['genero'], $_POST['correo_d'], $_POST['categoria_d'], $_POST['representante'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Iniciar la transacción
    $conn->beginTransaction();

    // Generar el nombre de usuario a partir del nombre y apellido
    $nombre_usuario = strtolower($_POST['nombre_d'] . '.' . $_POST['apellido_d']);
    $nombre_usuario = preg_replace('/\s+/', '.', $nombre_usuario); // Reemplazar espacios por puntos

    // Verificar si el nombre de usuario ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tab_usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $nombre_usuario);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Si el nombre de usuario ya existe, agregar un sufijo numérico
        $suffix = 1;
        do {
            $nombre_usuario = strtolower($_POST['nombre_d'] . '.' . $_POST['apellido_d'] . $suffix);
            $nombre_usuario = preg_replace('/\s+/', '.', $nombre_usuario);
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tab_usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $nombre_usuario);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            $suffix++;
        } while ($count > 0);
    }

    // Preparar la consulta SQL para insertar los datos en tab_usuarios
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    // Encriptar la contraseña
    $hashed_password = password_hash($_POST['cedula_d'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $nombre_usuario);
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
    $stmt = $conn->prepare('INSERT INTO tab_deportistas (id_usuario, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO) 
    VALUES (:id_usuario, :NOMBRE_DEPO, :APELLIDO_DEPO, :FECHA_NACIMIENTO, :CEDULA_DEPO, :NUMERO_CELULAR, :GENERO)');
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':NOMBRE_DEPO', $_POST['nombre_d']);
    $stmt->bindParam(':APELLIDO_DEPO', $_POST['apellido_d']);
    $stmt->bindParam(':FECHA_NACIMIENTO', $_POST['nacimiento_d']);
    $stmt->bindParam(':CEDULA_DEPO', $_POST['cedula_d']);
    $stmt->bindParam(':NUMERO_CELULAR', $_POST['celular_d']);
    $stmt->bindParam(':GENERO', $_POST['genero']);
    
    $stmt->execute();

    $id_deportista = $conn->lastInsertId(); // Obtener el id_deportista generado

    // Insertar en la nueva tabla categoria_deportista
    $stmt = $conn->prepare('INSERT INTO tab_categoria_deportista (id_categoria, id_deportista) VALUES (:id_categoria, :id_deportista)');
    $stmt->bindParam(':id_categoria', $_POST['categoria_d']);
    $stmt->bindParam(':id_deportista', $id_deportista);
    $stmt->execute();

    // Insertar en tab_representantes_deportistas
    $stmt = $conn->prepare('INSERT INTO tab_representantes_deportistas (id_deportista, id_representante) VALUES (:id_deportista, :id_representante)');
    $stmt->bindParam(':id_deportista', $id_deportista);
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
