<?php
include '../../configuracion/conexion.php';
// Iniciar la sesión antes de acceder a cualquier variable de sesión
session_start();
try {
    // Iniciar la transacción
    $conn->beginTransaction();

    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_r'], $_POST['apellido_r'], $_POST['celular_r'], $_POST['correo_r'], $_POST['direccion_r'], $_POST['cedula_r'])) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Generar el nombre de usuario a partir del nombre y apellido
    $nombre_usuario = strtolower($_POST['nombre_r'] . '.' . $_POST['apellido_r']);
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
            $nombre_usuario = strtolower($_POST['nombre_r'] . '.' . $_POST['apellido_r'] . $suffix);
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
    $hashed_password = password_hash($_POST['cedula_r'], PASSWORD_DEFAULT);

    // Bind de parámetros
    $stmt->bindParam(':usuario', $nombre_usuario);
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

    // Registrar la actividad en el log usando el ID del usuario que lo creó
    $creador_id = $_SESSION['user_id']; // Obtener el ID del usuario que creó al nuevo deportista
    $evento = "Nuevo representante: " . $nombre_usuario;
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = 'nuevo_usuario';  // Define el tipo de evento

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$creador_id, $evento, $ip, $tipo_evento]);

    // Redirigir a crentrenador.php con el nombre de usuario y la clave generada
    header("Location: ../crear_usuarios/crrepresentante.php?message=success&usuario=" . urlencode($nombre_usuario) . "&clave=" . urlencode($_POST['cedula_r']));
    exit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo "Fallo: " . $e->getMessage();
}
?>