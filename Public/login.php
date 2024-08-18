<?php
session_start();
require_once('../admin/configuracion/conexion.php');

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

$mensaje_error = ''; // Variable para almacenar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $remember = isset($_POST['remember']) ? true : false;

    // Obtener la dirección IP del usuario
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        // Verificar si el usuario está bloqueado o inactivo
        $stmt = $conn->prepare("SELECT ID_USUARIO, USUARIO, PASS, intentos_fallidos, bloqueado_hasta, status FROM tab_usuarios WHERE USUARIO = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar si el usuario está inactivo
            if ($user['status'] === 'inactivo') {
                $mensaje_error = 'Este perfil está inactivo. Por favor, contacta al administrador.';
            } else {
                $current_time = new DateTime();
                if ($user['bloqueado_hasta'] && new DateTime($user['bloqueado_hasta']) > $current_time) {
                    $mensaje_error = 'El usuario está bloqueado hasta ' . $user['bloqueado_hasta'];
                } else {
                    if (password_verify($password, $user['PASS'])) {
                        // Contraseña correcta, resetear intentos fallidos y desbloquear usuario
                        $stmt = $conn->prepare("UPDATE tab_usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE ID_USUARIO = :id");
                        $stmt->bindParam(':id', $user['ID_USUARIO']);
                        $stmt->execute();

                        // Registra la actividad de inicio de sesión en la base de datos
                        $evento = "Se ha iniciado una nueva sesión";
                        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$user['ID_USUARIO'], $evento, $ip]);

                        // Establece las variables de sesión
                        $_SESSION['user_id'] = $user['ID_USUARIO'];
                        $_SESSION['nombre'] = $user['USUARIO'];

                        // Obtener el tipo de usuario
                        $stmt = $conn->prepare("SELECT t.ID_TIPO FROM tab_usu_tipo ut INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO WHERE ut.ID_USUARIO = :id");
                        $stmt->bindParam(':id', $user['ID_USUARIO']);
                        $stmt->execute();
                        $tipo_usuario = $stmt->fetchColumn();
                        $_SESSION['tipo_usuario'] = $tipo_usuario;

                        // Manejo de la cookie "Recordar contraseña"
                        if ($remember) {
                            setcookie('usuario', $usuario, time() + (86400 * 30), "/"); // 30 días
                            setcookie('pass', $password, time() + (86400 * 30), "/"); // 30 días
                        } else {
                            // Eliminar las cookies si existen
                            if (isset($_COOKIE['usuario'])) {
                                setcookie('usuario', '', time() - 3600, "/");
                            }
                            if (isset($_COOKIE['pass'])) {
                                setcookie('pass', '', time() - 3600, "/");
                            }
                        }

                        // Redirige al usuario según su tipo
                        switch ($tipo_usuario) {
                            case 1: // Administrador
                                header("Location: ../admin/indexAd.php?id=" . $user['ID_USUARIO']);
                                exit;
                            case 2: // Entrenador
                                header("Location: ../entrenador/indexEntrenador.php?id=" . $user['ID_USUARIO']);
                                exit;
                            case 3: // Representante 
                                header("Location: ../representante/indexRep.php?id=" . $user['ID_USUARIO']);
                                exit;
                            case 4: // Deportista
                                header("Location: ../deportista/indexDep.php?id=" . $user['ID_USUARIO']);
                                exit;
                            default:
                                $mensaje_error = 'Tipo de usuario no reconocido.';
                        }
                    } else {
                        // Contraseña incorrecta, incrementar intentos fallidos
                        $intentos_fallidos = $user['intentos_fallidos'] + 1;
                        if ($intentos_fallidos >= 3) {
                            // Bloquear usuario por 15 minutos
                            $bloqueado_hasta = $current_time->modify('+15 minutes')->format('Y-m-d H:i:s');
                            $stmt = $conn->prepare("UPDATE tab_usuarios SET intentos_fallidos = :intentos, bloqueado_hasta = :bloqueado WHERE ID_USUARIO = :id");
                            $stmt->bindParam(':intentos', $intentos_fallidos);
                            $stmt->bindParam(':bloqueado', $bloqueado_hasta);
                            $stmt->bindParam(':id', $user['ID_USUARIO']);
                            $stmt->execute();
                            $mensaje_error = 'Usuario bloqueado por 15 minutos debido a múltiples intentos fallidos.';
                        } else {
                            // Actualizar intentos fallidos
                            $stmt = $conn->prepare("UPDATE tab_usuarios SET intentos_fallidos = :intentos WHERE ID_USUARIO = :id");
                            $stmt->bindParam(':intentos', $intentos_fallidos);
                            $stmt->bindParam(':id', $user['ID_USUARIO']);
                            $stmt->execute();
                            $mensaje_error = 'Contraseña incorrecta. Intentos fallidos: ' . $intentos_fallidos;
                        }
                    }
                }
            }
        } else {
            $mensaje_error = 'Usuario no encontrado.';
        }
    } catch (PDOException $e) {
        $mensaje_error = 'Error: ' . $e->getMessage();
    }
}

// Rellenar el formulario con cookies si existen
$storedUsuario = isset($_COOKIE['usuario']) ? $_COOKIE['usuario'] : '';
$storedPass = isset($_COOKIE['pass']) ? $_COOKIE['pass'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>LOONEY TUNES</title>
    <link href="../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body.bg-primary {
            background-image: url('../Assets/img/looney.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-header img {
            max-width: 50px;
            /* Ajusta el tamaño según sea necesario */
            margin-right: 10px;
            /* Espacio entre el logo y el texto */
        }
    </style>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <img src="../Assets/img/logo_sinfondo.png" alt="Logo de la Empresa" />
                                    <h3 class="fw-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputEmailAddress">Usuario</label>
                                            <input class="form-control" id="inputEmailAddress" type="text" placeholder="Usuario" name="usuario" value="<?php echo htmlspecialchars($storedUsuario); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputPassword">Contraseña</label>
                                            <input class="form-control" id="inputPassword" type="password" placeholder="Contraseña" name="pass" value="<?php echo htmlspecialchars($storedPass); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="rememberPasswordCheck" type="checkbox" name="remember" />
                                                <label class="form-check-label" for="rememberPasswordCheck">Recordar contraseña</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password.html">Olvidaste tu contraseña?</a>
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="register.html">Necesitas una cuenta? Registrate!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="footer-admin mt-auto footer-dark">
                <div class="container-xl px-4">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &copy; Looney Tunes 2023</div>
                        <div class="col-md-6 text-md-end small">
                            <a href="../Public/Privacy_Policy.php">Privacy Policy</a>
                            &middot;
                            <a href="../Public/terms_condition.php">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($mensaje_error); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../Assets/js/scripts.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($mensaje_error): ?>
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            <?php endif; ?>
        });
    </script>
</body>

</html>