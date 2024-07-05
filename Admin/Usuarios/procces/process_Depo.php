<?php
include '../../configuracion/conexion.php';

try {
    // Verificar que todos los campos están completos
    if (!isset($_POST['nombre_d'], $_POST['apellido_d'], $_POST['nacimiento_d'], $_POST['cedula_d'], $_POST['celular_d'], $_POST['genero'], $_POST['correo_d'], $_POST['categoria_d'], $_POST['representante'])) {
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
    $stmt = $conn->prepare('INSERT INTO tab_deportistas (id_usuario, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO, CORREO, ID_CATEGORIA, ID_REPRESENTANTE) 
    VALUES (:id_usuario, :NOMBRE_DEPO, :APELLIDO_DEPO, :FECHA_NACIMIENTO, :CEDULA_DEPO, :NUMERO_CELULAR, :GENERO, :CORREO, :ID_CATEGORIA, :ID_REPRESENTANTE)');
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':NOMBRE_DEPO', $_POST['nombre_d']);
    $stmt->bindParam(':APELLIDO_DEPO', $_POST['apellido_d']);
    $stmt->bindParam(':FECHA_NACIMIENTO', $_POST['nacimiento_d']);
    $stmt->bindParam(':CEDULA_DEPO', $_POST['cedula_d']);
    $stmt->bindParam(':NUMERO_CELULAR', $_POST['celular_d']);
    $stmt->bindParam(':GENERO', $_POST['genero']);
    $stmt->bindParam(':CORREO', $_POST['correo_d']);
    $stmt->bindParam(':ID_CATEGORIA', $_POST['categoria_d']);
    $stmt->bindParam(':ID_REPRESENTANTE', $_POST['representante']);  // Asegúrate de que el campo id_representante esté en la tabla tab_deportistas

    $stmt->execute();

    // Redirigir a la página de éxito
    header("Location: ../pages/crdeportista.php?message=success");
    exit();
} catch (Exception $e) {
    // Redirigir a la página de error con el mensaje de excepción
    header("Location: ../pages/crdeportista.php?message=" . urlencode($e->getMessage()));
    exit();
}
?>
