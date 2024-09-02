<?php
session_start();
require_once('../Admin/configuracion/conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// ID del usuario actual
$user_id = $_SESSION['user_id'];

// Consulta para obtener la foto del usuario
$sql = "
    SELECT f.FOTO 
    FROM tab_fotos_usuario f
    JOIN tab_usu_tipo ut ON ut.ID_TIPO = f.ID_TIPO
    WHERE ut.ID_USUARIO = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$foto = $stmt->fetchColumn();

// Codificar la foto en base64
$foto_src = $foto ? 'data:image/jpeg;base64,' . base64_encode($foto) : '/looneytunes/Assets/img/illustrations/profiles/profile-1.png';

// Obtener el ID del usuario logueado
$id_usuario_logueado = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['tipo_usuario']; // 'admin', 'entrenador', 'representante', 'deportista'

// Obtener el ID del usuario cuyo perfil se debe mostrar (si está presente en la URL)
$id_perfil = isset($_GET['id']) ? intval($_GET['id']) : $id_usuario_logueado;

// Inicializar las variables
$nombre = $apellido = $telefono = $correo = $direccion = $cedula = '';
$deportistas = [];

// Determinar el archivo de encabezado según el tipo de usuario
switch ($tipo_usuario) {
    case 1:
        include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
        break;
    case 2:
        include '/xampp/htdocs/looneytunes/entrenador/includes/header.php';
        break;
    case 3:
        include '/xampp/htdocs/looneytunes/representante/includes/header.php';
        break;
    case 4:
        include '/xampp/htdocs/looneytunes/deportista/includes/header.php';
        break;
    default:
        echo "Tipo de usuario desconocido.";
        exit();
}

// Obtener datos del usuario
try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$server;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar permisos de acceso
    $puede_ver_perfil = false;
    if ($tipo_usuario === 1) {
        $puede_ver_perfil = true;
    } elseif ($tipo_usuario === 3) {
        if ($id_perfil === $id_usuario_logueado) {
            $puede_ver_perfil = true;
        } else {
            $stmt = $conn->prepare("SELECT * FROM tab_representantes_deportistas WHERE ID_REPRESENTANTE = :id_representante AND ID_DEPORTISTA = :id_deportista");
            $stmt->bindParam(':id_representante', $id_usuario_logueado, PDO::PARAM_INT);
            $stmt->bindParam(':id_deportista', $id_perfil, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $puede_ver_perfil = true;
            }
        }
    } else {
        if ($id_perfil === $id_usuario_logueado) {
            $puede_ver_perfil = true;
        }
    }

    if (!$puede_ver_perfil) {
        echo "Acceso denegado.";
        exit();
    }

    // Obtener los datos del usuario basado en el ID del perfil
    if ($tipo_usuario === 1) {
        $stmt = $conn->prepare("SELECT ID_USUARIO, NOMBRE_ADMIN AS NOMBRE, APELLIDO_ADMIN AS APELLIDO, CELULAR_ADMIN AS CELULAR FROM tab_administradores WHERE ID_USUARIO = :id_usuario");
    } elseif ($tipo_usuario === 2) {
        $stmt = $conn->prepare("SELECT ID_USUARIO, NOMBRE_ENTRE AS NOMBRE, APELLIDO_ENTRE AS APELLIDO, CELULAR_ENTRE AS CELULAR, CORREO_ENTRE AS CORREO, DIRECCION_ENTRE AS DIRECCION, CEDULA_ENTRE AS CEDULA FROM tab_entrenadores WHERE ID_USUARIO = :id_usuario");
    } elseif ($tipo_usuario === 3) {
        $stmt = $conn->prepare("SELECT ID_USUARIO, NOMBRE_REPRE AS NOMBRE, APELLIDO_REPRE AS APELLIDO, CELULAR_REPRE AS CELULAR, CORREO_REPRE AS CORREO, DIRECCION_REPRE AS DIRECCION, CEDULA_REPRE AS CEDULA FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
    } elseif ($tipo_usuario === 4) {
        $stmt = $conn->prepare("SELECT ID_USUARIO, NOMBRE_DEPO AS NOMBRE, APELLIDO_DEPO AS APELLIDO, CEDULA_DEPO AS CEDULA, NUMERO_CELULAR AS CELULAR FROM tab_deportistas WHERE ID_USUARIO = :id_usuario");
    }

    $stmt->bindParam(':id_usuario', $id_perfil, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Asignar valores
    $nombre = isset($usuario['NOMBRE']) ? htmlspecialchars($usuario['NOMBRE']) : '';
    $apellido = isset($usuario['APELLIDO']) ? htmlspecialchars($usuario['APELLIDO']) : '';
    $telefono = isset($usuario['CELULAR']) ? htmlspecialchars($usuario['CELULAR']) : '';
    $correo = isset($usuario['CORREO']) ? htmlspecialchars($usuario['CORREO']) : '';
    $direccion = isset($usuario['DIRECCION']) ? htmlspecialchars($usuario['DIRECCION']) : '';
    $cedula = isset($usuario['CEDULA']) ? htmlspecialchars($usuario['CEDULA']) : '';

    // Obtener los deportistas asociados al representante
    if ($tipo_usuario === 3) {
        $deportistas_stmt = $conn->prepare("SELECT * FROM tab_representantes_deportistas INNER JOIN tab_deportistas ON tab_representantes_deportistas.ID_DEPORTISTA = tab_deportistas.ID_DEPORTISTA WHERE tab_representantes_deportistas.ID_REPRESENTANTE = :id_representante");
        $deportistas_stmt->bindParam(':id_representante', $id_perfil, PDO::PARAM_INT);
        $deportistas_stmt->execute();
        $deportistas = $deportistas_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Manejar la actualización del perfil
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_perfil === $id_usuario_logueado) {
        $nuevo_nombre = htmlspecialchars($_POST['nombre']);
        $nuevo_apellido = htmlspecialchars($_POST['apellido']);
        $nuevo_telefono = htmlspecialchars($_POST['telefono']);
        $nuevo_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $nuevo_password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

        // Actualizar datos en la base de datos
        if ($tipo_usuario === 1) {
            $update_stmt = $conn->prepare("UPDATE tab_administradores SET NOMBRE_ADMIN = :nombre, APELLIDO_ADMIN = :apellido, CELULAR_ADMIN = :telefono WHERE ID_USUARIO = :id_usuario");
        } elseif ($tipo_usuario === 2) {
            $update_stmt = $conn->prepare("UPDATE tab_entrenadores SET NOMBRE_ENTRE = :nombre, APELLIDO_ENTRE = :apellido, CELULAR_ENTRE = :telefono, CORREO_ENTRE = :email, DIRECCION_ENTRE = :direccion WHERE ID_USUARIO = :id_usuario");
        } elseif ($tipo_usuario === 3) {
            $update_stmt = $conn->prepare("UPDATE tab_representantes SET NOMBRE_REPRE = :nombre, APELLIDO_REPRE = :apellido, CELULAR_REPRE = :telefono, CORREO_REPRE = :email, DIRECCION_REPRE = :direccion WHERE ID_USUARIO = :id_usuario");
        }

        $update_stmt->bindParam(':nombre', $nuevo_nombre, PDO::PARAM_STR);
        $update_stmt->bindParam(':apellido', $nuevo_apellido, PDO::PARAM_STR);
        $update_stmt->bindParam(':telefono', $nuevo_telefono, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_usuario', $id_usuario_logueado, PDO::PARAM_INT);

        if ($tipo_usuario === 2) {
            $update_stmt->bindParam(':direccion', $_POST['direccion'], PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $nuevo_email, PDO::PARAM_STR);
        } elseif ($tipo_usuario === 3) {
            $update_stmt->bindParam(':direccion', $_POST['direccion'], PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $nuevo_email, PDO::PARAM_STR);
        }

        $update_stmt->execute();

        // Actualizar el correo electrónico
        if ($tipo_usuario === 2 || $tipo_usuario === 3) {
            $update_email_stmt = $conn->prepare("UPDATE tab_usuarios SET CORREO_USUARIO = :email WHERE ID_USUARIO = :id_usuario");
            $update_email_stmt->bindParam(':email', $nuevo_email, PDO::PARAM_STR);
            $update_email_stmt->bindParam(':id_usuario', $id_usuario_logueado, PDO::PARAM_INT);
            $update_email_stmt->execute();
        }

        // Actualizar la contraseña
        if (!empty($nuevo_password)) {
            $hashed_password = password_hash($nuevo_password, PASSWORD_DEFAULT);
            $update_password_stmt = $conn->prepare("UPDATE tab_usuarios SET pass = :password WHERE ID_USUARIO = :id_usuario");
            $update_password_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $update_password_stmt->bindParam(':id_usuario', $id_usuario_logueado, PDO::PARAM_INT);
            $update_password_stmt->execute();
        }

        echo "Perfil actualizado con éxito.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cierre de la conexión
$conn = null;
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Perfil de Usuario</h1>
        </div>

        <!-- Profile Details -->
        <div class="card mb-4">
            <div class="card-header">Detalles del Usuario</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                            </div>
                        </div>
                    </div>
                    <?php if ($tipo_usuario === 2 || $tipo_usuario === 3): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="correo">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="email" value="<?php echo htmlspecialchars($correo); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>" required>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Nueva Contraseña (opcional)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="../index.php" class="btn btn-secondary">Volver a la Lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
// Incluir el pie de página según el tipo de usuario
switch ($tipo_usuario) {
    case 1:
        include '/xampp/htdocs/looneytunes/admin/includespro/footer.php';
        break;
    case 2:
        include '/xampp/htdocs/looneytunes/entrenador/includes/footer.php';
        break;
    case 3:
        include '/xampp/htdocs/looneytunes/representante/includes/footer.php';
        break;
    case 4:
        include '/xampp/htdocs/looneytunes/deportista/includes/footer.php';
        break;
    default:
        echo "Tipo de usuario desconocido.";
        exit();
}
?>
