<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Registrar Administrador</title>
    <link href="../../../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../../Assets/img/favicon.png" />
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
                                <div class="card-header justify-content-center"><h3 class="fw-light my-4">Crear Cuenta de Administrador</h3></div>
                                <div class="card-body">
                                    <!-- Mensaje de éxito o error -->
                                    <?php
                                        if (isset($_GET['message'])) {
                                            $message = htmlspecialchars($_GET['message']);
                                            echo '<div class="alert alert-info">' . $message . '</div>';
                                        }
                                    ?>
                                    <!-- Registration form-->
                                    <form action="../procces/process_Admin.php" method="post"> <!-- Corregir la ruta al archivo PHP -->
                                        <!-- Form Row-->
                                        <div class="row gx-3">
                                            <div class="col-md-6">
                                                <!-- Form Group (first name)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="nombre_a">Nombre</label>
                                                    <input class="form-control" id="nombre_a" name="nombre_a" type="text" placeholder="Ingrese el nombre" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Form Group (last name)-->
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="apellido_a">Apellido</label>
                                                    <input class="form-control" id="apellido_a" name="apellido_a" type="text" placeholder="Ingrese el apellido" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Form Group (celular)-->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="celular_a">Celular</label>
                                            <input class="form-control" id="celular_a" name="celular_a" type="text" placeholder="Ingrese el celular" required />
                                        </div>
                                        <!-- Form Group (create account submit)-->
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button class="btn btn-primary btn-block" type="submit">Crear Cuenta</button>
                                            <a class="btn btn-primary btn-block" href="../../indexAd.php">Volver</a> <!-- Botón para volver -->
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
                        <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
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
    <script>
    // JavaScript para actualizar el año actual en el footer
    document.addEventListener('DOMContentLoaded', function () {
        var currentYear = new Date().getFullYear();
        document.getElementById('currentYear').textContent = currentYear;
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../../Assets/js/scripts.js"></script>
</body>
</html>
