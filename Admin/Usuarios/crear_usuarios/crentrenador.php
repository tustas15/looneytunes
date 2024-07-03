<?php
// Mostrar el mensaje de éxito o error desde procces_form.php
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'success') {
        $message = '<div style="margin: 20px; padding: 20px; border: 1px solid #4CAF50; background-color: #DFF2BF; color: #4CAF50; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
                        Registro exitoso
                    </div>';
    } else {
        $message = '<div style="margin: 20px; padding: 20px; border: 1px solid #FF0000; background-color: #FFBABA; color: #D8000C; font-family: Arial, sans-serif; font-size: 16px; border-radius: 5px; text-align: center;">
                        Error: ' . htmlspecialchars($_GET['message']) . '
                    </div>';
    }
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
    <title>Registrar Deportista</title>
    <link href="../../../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
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
                                <div class="card-header text-center">
                                    <h3 class="fw-light my-4">Crear una Cuenta de Entrenador</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Mensaje de éxito o error -->
                                    <?php echo $message; ?>

                                    <!-- Formulario para crear una cuenta de Entrenador -->
                                    <form action="../procces/procces_form.php" method="post" class="user">
                                        <!-- Form Group (nombre) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="nombre">Nombre</label>
                                            <input class="form-control" id="nombre" name="nombre" type="text" placeholder="Ingrese el nombre" required />
                                        </div>
                                        <!-- Form Group (apellido) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="apellido">Apellido</label>
                                            <input class="form-control" id="apellido" name="apellido" type="text" placeholder="Ingrese el apellido" required />
                                        </div>
                                        <!-- Form Group (experiencia) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="experiencia">Experiencia</label>
                                            <input class="form-control" id="experiencia" name="experiencia" type="text" placeholder="Ingrese la experiencia" required />
                                        </div>
                                        <!-- Form Group (celular) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="celular">Celular</label>
                                            <input class="form-control" id="celular" name="celular" type="text" placeholder="Ingrese el celular" required />
                                        </div>
                                        <!-- Form Group (correo) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="correo">Correo Electrónico</label>
                                            <input class="form-control" id="correo" name="correo" type="email" aria-describedby="emailHelp" placeholder="Introduzca la dirección de correo electrónico" required />
                                        </div>
                                        <!-- Form Group (direccion) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="direccion">Dirección</label>
                                            <input class="form-control" id="direccion" name="direccion" type="text" placeholder="Ingrese la dirección" required />
                                        </div>
                                        <!-- Form Group (cedula) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="cedula">Cédula</label>
                                            <input class="form-control" id="cedula" name="cedula" type="text" placeholder="Ingrese la cédula" required />
                                        </div>
                                        <!-- Botón de registro -->
                                        <button class="btn btn-primary btn-block" type="submit">Crear Cuenta</button>
                                        <a class="btn btn-primary btn-block" href="../../indexAd.php">Volver</a> <!-- Botón para volver -->
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
                        <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Privacy Policy</a>
                            &middot;
                            <a href="#!">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
                <script>
                    // JavaScript para actualizar el año actual en el footer
                    document.addEventListener('DOMContentLoaded', function() {
                        var currentYear = new Date().getFullYear();
                        document.getElementById('currentYear').textContent = currentYear;
                    });
                </script>
            </footer>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="../../../Assets/vendor/jquery/jquery.min.js"></script>
    <script src="../../../Assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="../../../Assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="../../../Assets/js/sb-admin-2.min.js"></script>
</body>

</html>