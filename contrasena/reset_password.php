<?php
require '../admin/configuracion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica si el token es válido y no ha expirado
    $stmt = $conn->prepare("SELECT ID_USUARIO, reset_token_exp FROM tab_usuarios WHERE reset_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user && strtotime($user['reset_token_exp']) > time()) {
        // Muestra el formulario para restablecer la contraseña
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
            <title>Reset Password</title>
            <link href="/looneytunes/assets/css/styles.css" rel="stylesheet">
            <link rel="icon" type="image/x-icon" href="/looneytunes/Assets/img/logo.png">
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
                                    <!-- Reset password form-->
                                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                                        <div class="card-header justify-content-center"><h3 class="fw-light my-4">Restablecer Contraseña</h3></div>
                                        <div class="card-body">
                                            <form method="POST" action="reset_password.php">
                                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="password">Nueva Contraseña</label>
                                                    <input class="form-control" id="password" name="password" type="password" placeholder="Ingrese nueva contraseña" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="confirm_password">Confirmar Contraseña</label>
                                                    <input class="form-control" id="confirm_password" name="confirm_password" type="password" placeholder="Confirme nueva contraseña" required>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                    <button class="btn btn-primary" type="submit">Restablecer Contraseña</button>
                                                </div>
                                            </form>
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
                                <div class="col-md-6 small">Copyright &copy; Looney Tunes <span id="currentYear"></span></div>
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
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
            <script src="js/scripts.js"></script>
        </body>
        </html>
        <?php
    } else {
        echo "El token es inválido o ha expirado.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
        $token = $_POST['token'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Verifica que las contraseñas coincidan
        if ($password === $confirm_password) {
            // Verifica si el token es válido y no ha expirado
            $stmt = $conn->prepare("SELECT ID_USUARIO FROM tab_usuarios WHERE reset_token = :token AND reset_token_exp > NOW()");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch();

            if ($user) {
                // Hashea la nueva contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Actualiza la contraseña en la base de datos y elimina el token
                $stmt = $conn->prepare("UPDATE tab_usuarios SET pass = :password, reset_token = NULL, reset_token_exp = NULL WHERE ID_USUARIO = :userId");
                $stmt->execute(['password' => $hashed_password, 'userId' => $user['ID_USUARIO']]);

                echo "Contraseña restablecida con éxito. Ahora puedes <a href='../public/login.php'>iniciar sesión</a>.";
            } else {
                echo "El token es inválido o ha expirado.";
            }
        } else {
            echo "Las contraseñas no coinciden.";
        }
    }
}
?>
