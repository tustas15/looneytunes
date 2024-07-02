<?php
session_start();
require_once('/xampp/htdocs/tutorial/conexion/conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del usuario logueado
$id_usuario = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['tipo_usuario']; // 'admin', 'entrenador', 'representante'

// Inicializar las variables
$nombre = $apellido = $telefono = $experiencia = $correo = $direccion = $cedula = $nombre_depo = $apellido_depo = $fecha_nacimiento_depo = $cedula_depo = $numero_celular_depo = $genero_depo = '';
$deportistas = [];

// Obtener el ID del usuario cuyo perfil se debe mostrar (si está presente en la URL)
$id_perfil = isset($_GET['ID_USUARIO']) ? intval($_GET['ID_USUARIO']) : $id_usuario;

try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$server;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($tipo_usuario === 1) {
        // Consultar los datos del administrador
        $stmt = $conn->prepare("SELECT * FROM tab_administradores WHERE ID_USUARIO = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_perfil, PDO::PARAM_INT);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $nombre = htmlspecialchars($admin['NOMBRE_ADMIN']);
            $apellido = htmlspecialchars($admin['APELLIDO_ADMIN']);
            $telefono = htmlspecialchars($admin['CELULAR_ADMIN']);
        }
    } elseif ($tipo_usuario === 2) {
        // Consultar los datos del entrenador
        $stmt = $conn->prepare("SELECT * FROM tab_entrenadores WHERE ID_USUARIO = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_perfil, PDO::PARAM_INT);
        $stmt->execute();
        $entrenador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($entrenador) {
            $nombre = htmlspecialchars($entrenador['NOMBRE_ENTRE']);
            $apellido = htmlspecialchars($entrenador['APELLIDO_ENTRE']);
            $telefono = htmlspecialchars($entrenador['CELULAR_ENTRE']);
            $experiencia = htmlspecialchars($entrenador['EXPERIENCIA_ENTRE']);
            $correo = htmlspecialchars($entrenador['CORREO_ENTRE']);
            $direccion = htmlspecialchars($entrenador['DIRECCION_ENTRE']);
        }
    } elseif ($tipo_usuario === 3) {
        // Consultar los datos del representante
        $stmt = $conn->prepare("SELECT * FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_perfil, PDO::PARAM_INT);
        $stmt->execute();
        $representante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($representante) {
            $nombre = htmlspecialchars($representante['NOMBRE_REPRE']);
            $apellido = htmlspecialchars($representante['APELLIDO_REPRE']);
            $telefono = htmlspecialchars($representante['CELULAR_REPRE']);
            $correo = htmlspecialchars($representante['CORREO_REPRE']);
            $direccion = htmlspecialchars($representante['DIRECCION_REPRE']);
            $cedula = htmlspecialchars($representante['CEDULA_REPRE']);

            // Obtener los deportistas asociados al representante
            $deportistas_stmt = $conn->prepare("
                SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO
                FROM tab_representantes_deportistas rd
                INNER JOIN tab_deportistas d ON rd.ID_DEPORTISTA = d.ID_DEPORTISTA
                WHERE rd.ID_REPRESENTANTE = :id_representante
            ");
            $deportistas_stmt->bindParam(':id_representante', $id_perfil, PDO::PARAM_INT);
            $deportistas_stmt->execute();
            $deportistas = $deportistas_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // Manejar la actualización del perfil
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_perfil === $id_usuario) {
        $nuevo_nombre = htmlspecialchars($_POST['nombre']);
        $nuevo_apellido = htmlspecialchars($_POST['apellido']);
        $nuevo_telefono = htmlspecialchars($_POST['telefono']);
        $nuevo_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $nuevo_password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

        // Actualizar datos en la base de datos
        if ($tipo_usuario === 1) {
            $update_stmt = $conn->prepare("
                UPDATE tab_administradores
                SET NOMBRE_ADMIN = :nombre, APELLIDO_ADMIN = :apellido, CELULAR_ADMIN = :telefono
                WHERE ID_USUARIO = :id_usuario
            ");
        } elseif ($tipo_usuario === 2) {
            $update_stmt = $conn->prepare("
                UPDATE tab_entrenadores
                SET NOMBRE_ENTRE = :nombre, APELLIDO_ENTRE = :apellido, CELULAR_ENTRE = :telefono, CORREO_ENTRE = :email, DIRECCION_ENTRE = :direccion, EXPERIENCIA_ENTRE = :experiencia
                WHERE ID_USUARIO = :id_usuario
            ");
            $update_stmt->bindParam(':experiencia', $_POST['experiencia'], PDO::PARAM_STR);
            $update_stmt->bindParam(':direccion', $_POST['direccion'], PDO::PARAM_STR);
        } elseif ($tipo_usuario === 3) {
            $update_stmt = $conn->prepare("
                UPDATE tab_representantes
                SET NOMBRE_REPRE = :nombre, APELLIDO_REPRE = :apellido, CELULAR_REPRE = :telefono, CORREO_REPRE = :email, DIRECCION_REPRE = :direccion
                WHERE ID_USUARIO = :id_usuario
            ");
            $update_stmt->bindParam(':direccion', $_POST['direccion'], PDO::PARAM_STR);
        }

        $update_stmt->bindParam(':nombre', $nuevo_nombre, PDO::PARAM_STR);
        $update_stmt->bindParam(':apellido', $nuevo_apellido, PDO::PARAM_STR);
        $update_stmt->bindParam(':telefono', $nuevo_telefono, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        if ($tipo_usuario === 2 || $tipo_usuario === 3) {
            $update_stmt->bindParam(':email', $nuevo_email, PDO::PARAM_STR);
        }

        $update_stmt->execute();

        // Actualizar la contraseña si se proporciona una nueva
        if ($nuevo_password && ($tipo_usuario === 2 || $tipo_usuario === 3)) {
            $update_password_stmt = $conn->prepare("
                UPDATE tab_usuarios SET PASSWORD_USUARIO = :password WHERE ID_USUARIO = :id_usuario
            ");
            $hashed_password = password_hash($nuevo_password, PASSWORD_BCRYPT);
            $update_password_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $update_password_stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $update_password_stmt->execute();
        }

        echo "<p>Perfil actualizado con éxito.</p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h1>Perfil de Usuario</h1>
    <form action="" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
        <br>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo $apellido; ?>" required>
        <br>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>
        <br>
        <?php if ($tipo_usuario === 2): ?>
            <label for="experiencia">Experiencia:</label>
            <textarea id="experiencia" name="experiencia"><?php echo $experiencia; ?></textarea>
            <br>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo $direccion; ?>">
            <br>
        <?php endif; ?>
        <?php if ($tipo_usuario === 3): ?>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo $direccion; ?>">
            <br>
            <label for="email">Correo:</label>
            <input type="email" id="email" name="email" value="<?php echo $correo; ?>">
            <br>
        <?php endif; ?>
        <input type="submit" value="Actualizar Perfil">
    </form>

    <?php if ($tipo_usuario === 3 && $deportistas): ?>
        <h2>Deportistas Asociados</h2>
        <ul>
            <?php foreach ($deportistas as $deportista): ?>
                <li><?php echo htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
