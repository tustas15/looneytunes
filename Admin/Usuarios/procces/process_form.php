<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre'], $_POST['apellido'], $_POST['experiencia'], $_POST['celular'], $_POST['correo'], $_POST['direccion'], $_POST['cedula'], $_POST['categoria'])) {
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

    // Insertar en tab_entre_categoria para asociar al entrenador con la categoría
    $stmt = $conn->prepare('INSERT INTO tab_entre_categoria (id_usuario, id_categoria) VALUES (:id_usuario, :id_categoria)');
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':id_categoria', $_POST['categoria']);
    $stmt->execute();

    // Registrar el evento
    $id_usuario = $_SESSION['id_usuario'];
    $evento = "Creación de cuenta de entrenador";
    $ip = $_SERVER['REMOTE_ADDR'];
    $tipo_evento = "Registro";

    $logQuery = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->execute([$id_usuario, $evento, $ip, $tipo_evento]);

    // Confirmar la transacción
    $conn->commit();

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
