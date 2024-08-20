<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre'], $_POST['apellido'], $_POST['experiencia'], $_POST['celular'], $_POST['correo'], $_POST['direccion'], $_POST['cedula'], $_POST['categoria'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Iniciar la transacción
    $conn->beginTransaction();

    // Generar el nombre de usuario a partir del nombre y apellido
    $nombre_usuario = strtolower($_POST['nombre'] . '.' . $_POST['apellido']);
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
            $nombre_usuario = strtolower($_POST['nombre'] . '.' . $_POST['apellido'] . $suffix);
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
    $hashed_password = password_hash($_POST['cedula'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $nombre_usuario);
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

    // Obtener el ID del entrenador recién insertado
    $id_entrenador = $conn->lastInsertId();

    // Insertar en tab_entrenador_categoria para asociar al entrenador con la categoría
    $stmt = $conn->prepare('INSERT INTO tab_entrenador_categoria (ID_ENTRENADOR, id_categoria) VALUES (:id_entrenador, :id_categoria)');
    $stmt->bindParam(':id_entrenador', $id_entrenador);
    $stmt->bindParam(':id_categoria', $_POST['categoria']);
    $stmt->execute();

    // Registrar el evento
    // Registrar la actividad en el log usando el ID del usuario que lo creó
    $creador_id = $_SESSION['user_id']; // Obtener el ID del usuario que creó al nuevo deportista
    $evento = "Nuevo entrenador registrado: " . $_POST['nombre'] . " " . $_POST['apellido'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$creador_id, $evento, $ip, $tipo_evento]);

    // Confirmar la transacción
    $conn->commit();

    // Redirigir con mensaje de éxito
    header("Location: ../crear_usuarios/crentrenador.php?message=success");
    exit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    // Redirigir con mensaje de error
    header("Location: ../crear_usuarios/crentrenador.php?message=" . urlencode($e->getMessage()));
    exit();
}

// Cerrar la conexión
$conn = null;
?>
