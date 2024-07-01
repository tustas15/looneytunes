<?php
session_start();
require '../Admin/configuracion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);

    // Obtener la dirección IP del usuario
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO, u.PASS, t.ID_TIPO, t.TIPO 
                                FROM tab_usuarios u 
                                INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
                                INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO
                                WHERE u.USUARIO = :usuario;");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['PASS'])) {
                // Registra la actividad de inicio de sesión en la base de datos
                $evento = "Se ha iniciado una nueva sesión";
                $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$user['ID_USUARIO'], $evento, $ip]);

                // Establece las variables de sesión
                $_SESSION['user_id'] = $user['ID_USUARIO'];
                $_SESSION['nombre'] = $user['USUARIO'];
                $_SESSION['tipo_usuario'] = $user['ID_TIPO'];

                // Redirige al usuario según su tipo
                switch ($user['ID_TIPO']) {
                    case 1: // Administrador
                        header("Location: ../admin/indexAd.php?id=" . $user['ID_USUARIO']);
                        break;
                    case 2: // Entrenador
                        header("Location: ../entrenador/indexEntrenador.php?id=" . $user['ID_USUARIO']);
                        break;
                    case 3: // Representante 
                        header("Location: ../representante/indexRep.php?id=" . $user['ID_USUARIO']);
                        break;
                    case 4: // Deportista
                        header("Location: ../deportista/indexDep.php?id=" . $user['ID_USUARIO']);
                        break;
                    default:
                        echo "Tipo de usuario no reconocido.";
                        exit();
                }
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "Usuario no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - SB Admin Pro</title>
    <link href="../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <!-- Basic login form-->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Login form-->
                                    <form method="POST" action="">
                                        <!-- Form Group (username)-->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputUsername">Nombre de Usuario</label>
                                            <input class="form-control" id="inputUsername" name="usuario" type="text" placeholder="Ingrese su nombre de usuario" />
                                        </div>
                                        <!-- Form Group (password)-->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputPassword">Contraseña</label>
                                            <input class="form-control" id="inputPassword" name="pass" type="password" placeholder="Ingrese su contraseña" />
                                        </div>
                                        <!-- Form Group (remember password checkbox)-->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="rememberPasswordCheck" type="checkbox" value="" />
                                                <label class="form-check-label" for="rememberPasswordCheck">Recordar contraseña</label>
                                            </div>
                                        </div>
                                        <!-- Form Group (login box)-->
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="auth-password-basic.html">¿Olvidaste tu contraseña?</a>
                                            <button class="btn btn-primary" type="submit">Iniciar sesión</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Registrar a usuarios aleatorios
                                <div class="card-footer text-center">
                                    <div class="small"><a href="auth-register-basic.html">¿Necesitas una cuenta? ¡Regístrate!</a></div>
                                </div> 
                                -->
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
                        <div class="col-md-6 small">Copyright &copy; Looney Tunes <span id="currentYear"></span></div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Política de privacidad</a>
                            &middot;
                            <a href="#!">Términos &amp; Condiciones</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../Assets/js/scripts.js"></script>
</body>

</html>