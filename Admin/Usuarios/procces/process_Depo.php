<?php
include '../conexion/conexion.php';

try {
    // Preparar la consulta SQL para insertar los datos en usuario
    $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
    // Encriptar la contraseña
    $hashed_password = password_hash($_POST['cedula_d'], PASSWORD_DEFAULT);

    $stmt->bindParam(':usuario', $_POST['nombre_d']);
    $stmt->bindParam(':pass', $hashed_password);

    $stmt->execute();

    $id_usuario = $conn->lastInsertId();

    $tipo = '4';

    $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
    $stmt->bindParam('id_tipo', $tipo);
    $stmt->bindParam('id_usuario', $id_usuario);
    $stmt->execute();

    // Preparar la consulta SQL para insertar los datos en tab_deportistas
    $stmt = $conn->prepare('INSERT INTO tab_deportistas (id_usuario, NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO) 
    VALUES (:id_usuario, :NOMBRE_DEPO, :APELLIDO_DEPO, :FECHA_NACIMIENTO, :CEDULA_DEPO, :NUMERO_CELULAR, :GENERO)');
    // Bind de parámetros

    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':NOMBRE_DEPO', $_POST['nombre_d']);
    $stmt->bindParam(':APELLIDO_DEPO', $_POST['apellido_d']);
    $stmt->bindParam(':FECHA_NACIMIENTO', $_POST['nacimiendo_d']);
    $stmt->bindParam(':CEDULA_DEPO', $_POST['cedula_d']);
    $stmt->bindParam(':NUMERO_CELULAR', $_POST['celular_d']);
    $stmt->bindParam(':GENERO', $_POST['genero']);

    // Ejecutar la consulta
    $stmt->execute();

    // Asignar el representante al deportista en la tabla tab_representantes_deportistas
    $id_representante = $_POST['representante'];
    $stmt = $conn->prepare('INSERT INTO tab_representantes_deportistas (ID_REPRESENTANTE, ID_DEPORTISTA) VALUES (:ID_REPRESENTANTE, :ID_DEPORTISTA)');
    $stmt->bindParam(':ID_REPRESENTANTE', $id_representante);
    $stmt->bindParam(':ID_DEPORTISTA', $id_usuario);
    $stmt->execute();

    // Registrar el evento en la tabla tab_logs
    $evento = "Registro de nuevo deportista: " . $_POST['nombre_d'] . " " . $_POST['apellido_d'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario, $evento, $ip]);

    echo '<div style="margin: 20px; padding: 20px; border: 1px solid #4CAF50; background-color: #DFF2BF; color: #4CAF50; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
            Registro exitoso
          </div>';
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
