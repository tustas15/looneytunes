<?php
session_start();
require_once('../Admin/configuracion/conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del usuario logueado
$id_usuario_logueado = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['tipo_usuario']; // 'admin', 'entrenador', 'representante'

// Obtener el ID del usuario cuyo perfil se debe mostrar (si está presente en la URL)
$id_perfil = isset($_GET['id']) ? intval($_GET['id']) : $id_usuario_logueado;

// Inicializar las variables
$nombre = $apellido = $telefono = $experiencia = $correo = $direccion = $cedula = $nombre_depo = $apellido_depo = $fecha_nacimiento_depo = $cedula_depo = $numero_celular_depo = $genero_depo = '';
$deportistas = [];

try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$server;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario tiene permiso para ver el perfil solicitado
    $puede_ver_perfil = false;

    if ($tipo_usuario === 1) {
        // El administrador puede ver todos los perfiles
        $puede_ver_perfil = true;
    } elseif ($tipo_usuario === 3) {
        // El representante puede ver su propio perfil y los perfiles de los deportistas que tiene asignados
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
        // Los entrenadores solo pueden ver su propio perfil
        if ($id_perfil === $id_usuario_logueado) {
            $puede_ver_perfil = true;
        }
    }

    if (!$puede_ver_perfil) {
        // Redirigir a una página de error o mostrar un mensaje de acceso denegado
        echo "Acceso denegado.";
        exit();
    }

    // Obtener los datos del usuario basado en el ID del perfil
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
            $deportistas_stmt = $conn->prepare("SELECT * FROM tab_representantes_deportistas INNER JOIN tab_deportistas ON tab_representantes_deportistas.ID_DEPORTISTA = tab_deportistas.ID_DEPORTISTA WHERE tab_representantes_deportistas.ID_REPRESENTANTE = :id_representante");
            $deportistas_stmt->bindParam(':id_representante', $id_perfil, PDO::PARAM_INT);
            $deportistas_stmt->execute();
            $deportistas = $deportistas_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
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
            $update_stmt = $conn->prepare("UPDATE tab_entrenadores SET NOMBRE_ENTRE = :nombre, APELLIDO_ENTRE = :apellido, CELULAR_ENTRE = :telefono, CORREO_ENTRE = :email, DIRECCION_ENTRE = :direccion, EXPERIENCIA_ENTRE = :experiencia WHERE ID_USUARIO = :id_usuario");
        } elseif ($tipo_usuario === 3) {
            $update_stmt = $conn->prepare("UPDATE tab_representantes SET NOMBRE_REPRE = :nombre, APELLIDO_REPRE = :apellido, CELULAR_REPRE = :telefono, CORREO_REPRE = :email, DIRECCION_REPRE = :direccion WHERE ID_USUARIO = :id_usuario");
        }

        $update_stmt->bindParam(':nombre', $nuevo_nombre, PDO::PARAM_STR);
        $update_stmt->bindParam(':apellido', $nuevo_apellido, PDO::PARAM_STR);
        $update_stmt->bindParam(':telefono', $nuevo_telefono, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_usuario', $id_usuario_logueado, PDO::PARAM_INT);

        if ($tipo_usuario === 2) {
            $update_stmt->bindParam(':experiencia', $_POST['experiencia'], PDO::PARAM_STR);
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
            $hash_password = password_hash($nuevo_password, PASSWORD_DEFAULT);
            $update_password_stmt = $conn->prepare("UPDATE tab_usuarios SET CLAVE_USUARIO = :password WHERE ID_USUARIO = :id_usuario");
            $update_password_stmt->bindParam(':password', $hash_password, PDO::PARAM_STR);
            $update_password_stmt->bindParam(':id_usuario', $id_usuario_logueado, PDO::PARAM_INT);
            $update_password_stmt->execute();
        }

        // Redirigir después de la actualización
        header("Location: profile.php?id=$id_perfil");
        exit();
    }

    // Manejar la selección del perfil del deportista por parte del representante
    if ($tipo_usuario === 3 && isset($_GET['id_deportista'])) {
        $id_deportista = intval($_GET['id_deportista']);
        $deportista_stmt = $conn->prepare("SELECT * FROM tab_deportistas WHERE ID_DEPORTISTA = :id_deportista");
        $deportista_stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $deportista_stmt->execute();
        $deportista = $deportista_stmt->fetch(PDO::FETCH_ASSOC);

        if ($deportista) {
            $nombre_depo = htmlspecialchars($deportista['NOMBRE_DEPO']);
            $apellido_depo = htmlspecialchars($deportista['APELLIDO_DEPO']);
            $fecha_nacimiento_depo = htmlspecialchars($deportista['FECHA_NACIMIENTO_DEPO']);
            $cedula_depo = htmlspecialchars($deportista['CEDULA_DEPO']);
            $numero_celular_depo = htmlspecialchars($deportista['NUMERO_CELULAR_DEPO']);
            $genero_depo = htmlspecialchars($deportista['GENERO_DEPO']);
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
include('./includes/header.php');
?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="user"></i></div>
                                Account Settings - Profile
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container-xl px-4 mt-n10">
            <div class="row">
                <div class="col-xl-4">
                    <!-- Profile picture card-->
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">Profile Picture</div>
                        <div class="card-body text-center">
                            <!-- Profile picture image-->
                            <img class="img-account-profile rounded-circle mb-2" src="../Assets/img/illustrations/profiles/profile-1.png" alt="" />
                            <!-- Profile picture help block-->
                            <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                            <!-- Profile picture upload button-->
                            <button class="btn btn-primary" type="button">Upload new image</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <!-- Account details card-->
                    <div class="card mb-4">
                        <div class="card-header">Account Details</div>
                        <div class="card-body">
                            <form method="POST" action="profile.php?id=<?php echo $id_perfil; ?>">
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputNombre">Nombre</label>
                                        <input class="form-control" id="inputNombre" type="text" name="nombre" placeholder="Ingrese su nombre" value="<?php echo $nombre; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputApellido">Apellido</label>
                                        <input class="form-control" id="inputApellido" type="text" name="apellido" placeholder="Ingrese su apellido" value="<?php echo $apellido; ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputTelefono">Teléfono</label>
                                    <input class="form-control" id="inputTelefono" type="text" name="telefono" placeholder="Ingrese su número de teléfono" value="<?php echo $telefono; ?>">
                                </div>
                                <?php if ($tipo_usuario === 2) : ?>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputExperiencia">Experiencia</label>
                                        <input class="form-control" id="inputExperiencia" type="text" name="experiencia" placeholder="Ingrese su experiencia" value="<?php echo htmlspecialchars($experiencia); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputDireccion">Dirección</label>
                                        <input class="form-control" id="inputDireccion" type="text" name="direccion" placeholder="Ingrese su dirección" value="<?php echo htmlspecialchars($direccion); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputEmail">Correo electrónico</label>
                                        <input class="form-control" id="inputEmail" type="email" name="email" placeholder="Ingrese su correo electrónico" value="<?php echo htmlspecialchars($correo); ?>">
                                    </div>
                                <?php elseif ($tipo_usuario === 3) : ?>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputDireccion">Dirección</label>
                                        <input class="form-control" id="inputDireccion" type="text" name="direccion" placeholder="Ingrese su dirección" value="<?php echo htmlspecialchars($direccion); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputEmail">Correo electrónico</label>
                                        <input class="form-control" id="inputEmail" type="email" name="email" placeholder="Ingrese su correo electrónico" value="<?php echo htmlspecialchars($correo); ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if ($tipo_usuario !== 1 && $id_perfil === $id_usuario_logueado) : ?>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputPassword">Contraseña</label>
                                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Ingrese su nueva contraseña (opcional)">
                                    </div>
                                <?php endif; ?>
                                <?php if ($id_perfil === $id_usuario_logueado) : ?>
                                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($tipo_usuario === 3 && $id_perfil === $id_usuario_logueado) : ?>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header">Deportistas Asociados</div>
                            <div class="card-body">
                                <form method="GET" action="profile.php?id=<?php echo $id_perfil; ?>">
                                    <div class="mb-3">
                                        <label class="small mb-1" for="selectDeportista">Seleccionar Deportista</label>
                                        <select class="form-control" id="selectDeportista" name="id_deportista">
                                            <option value="">Seleccione un deportista</option>
                                            <?php foreach ($deportistas as $deportista) : ?>
                                                <option value="<?php echo $deportista['ID_DEPORTISTA']; ?>">
                                                    <?php echo htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Ver perfil del deportista</button>
                                </form>
                                <?php if ($nombre_depo) : ?>
                                    <hr />
                                    <h5>Perfil del Deportista</h5>
                                    <p><b>Nombre:</b> <?php echo htmlspecialchars($nombre_depo); ?></p>
                                    <p><b>Apellido:</b> <?php echo htmlspecialchars($apellido_depo); ?></p>
                                    <p><b>Fecha de Nacimiento:</b> <?php echo htmlspecialchars($fecha_nacimiento_depo); ?></p>
                                    <p><b>Cédula:</b> <?php echo htmlspecialchars($cedula_depo); ?></p>
                                    <p><b>Teléfono:</b> <?php echo htmlspecialchars($numero_celular_depo); ?></p>
                                    <p><b>Género:</b> <?php echo htmlspecialchars($genero_depo); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include('./includes/footer.php'); ?>
</div>
</div>
</body>

</html>