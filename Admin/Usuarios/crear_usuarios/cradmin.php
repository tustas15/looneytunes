<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include './Admin/configuracion/conexion.php';

    try {
        // Iniciar la transacción
        $conn->beginTransaction();

        // Preparar la consulta SQL para insertar los datos en tab_usuarios
        $stmt = $conn->prepare("INSERT INTO tab_usuarios (usuario, pass) VALUES (:usuario, :pass)");
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Bind de parámetros
        $stmt->bindParam(':usuario', $_POST['nombre_a']);
        $stmt->bindParam(':pass', $hashed_password);
        $stmt->execute();

        $id_usuario = $conn->lastInsertId();

        $tipo = '1';

        // Preparar la consulta SQL para insertar en tab_usu_tipo
        $stmt = $conn->prepare('INSERT INTO tab_usu_tipo (id_tipo, id_usuario) VALUES (:id_tipo, :id_usuario)');
        $stmt->bindParam(':id_tipo', $tipo);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // Preparar la consulta SQL para insertar los datos en tab_administradores
        $stmt = $conn->prepare('INSERT INTO tab_administradores (ID_USUARIO, NOMBRE_ADMIN, APELLIDO_ADMIN, CELULAR_ADMIN) 
            VALUES (:ID_USUARIO, :NOMBRE_ADMIN, :APELLIDO_ADMIN, :CELULAR_ADMIN)');

        // Bind de parámetros
        $stmt->bindParam(':ID_USUARIO', $id_usuario);
        $stmt->bindParam(':NOMBRE_ADMIN', $_POST['nombre_a']);
        $stmt->bindParam(':APELLIDO_ADMIN', $_POST['apellido_a']);
        $stmt->bindParam(':CELULAR_ADMIN', $_POST['celular_a']);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();

        // Registrar el evento en la tabla tab_logs
        $evento = "Registro de cuenta de administrador: " . $_POST['nombre_a'] . " " . $_POST['apellido_a'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_usuario, $evento, $ip]);

        $message = '<div style="margin: 20px; padding: 20px; border: 1px solid #4CAF50; background-color: #DFF2BF; color: #4CAF50; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
                        Registro exitoso
                    </div>';
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $conn->rollBack();
        $message = '<div style="margin: 20px; padding: 20px; border: 1px solid #FF0000; background-color: #FFBABA; color: #D8000C; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
                        Error: ' . htmlspecialchars($e->getMessage()) . '
                    </div>';
    }

    // Cerrar la conexión
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Register - SB Admin Pro</title>
    <link href="/Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/Assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <!-- Basic registration form-->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">Create Account</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Registration form-->
                                    <form method="POST" action="">
                                        <!-- Form Row-->
                                        <div class="row gx-3">
                                            <div class="col-md-6">
                                                <!-- Form Group (first name)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="nombre_a">First Name</label>
                                                    <input class="form-control" id="nombre_a" name="nombre_a" type="text" placeholder="Enter first name" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Form Group (last name)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="apellido_a">Last Name</label>
                                                    <input class="form-control" id="apellido_a" name="apellido_a" type="text" placeholder="Enter last name" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Form Group (email address)            -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputEmailAddress">Email</label>
                                            <input class="form-control" id="inputEmailAddress" name="celular_a" type="email" aria-describedby="emailHelp" placeholder="Enter email address" required />
                                        </div>
                                        <!-- Form Row    -->
                                        <div class="row gx-3">
                                            <div class="col-md-6">
                                                <!-- Form Group (password)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="inputPassword">Password</label>
                                                    <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Enter password" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Form Group (confirm password)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="inputConfirmPassword">Confirm Password</label>
                                                    <input class="form-control" id="inputConfirmPassword" name="confirm_password" type="password" placeholder="Confirm password" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Form Group (create account submit)-->
                                        <button class="btn btn-primary btn-block" type="submit">Create Account</button>
                                    </form>
                                    <?php
                                    if (isset($message)) {
                                        echo $message;
                                    }
                                    ?>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="auth-login-basic.html">Have an account? Go to login</a></div>
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
                        <div class="col-md-6 small">Copyright &copy; Your Website 2021</div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Privacy Policy</a>
                            &middot;
                            <a href="#!">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/Assets/js/scripts.js"></script>
</body>

</html>