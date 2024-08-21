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
    <link rel="icon" type="image/x-icon" href="/Assets/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Datos de la Cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($_GET['usuario']); ?></p>
                    <p><strong>Clave:</strong> <?php echo htmlspecialchars($_GET['clave']); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', event => {
            <?php if (isset($_GET['usuario']) && isset($_GET['clave'])): ?>
                var userModal = new bootstrap.Modal(document.getElementById('userModal'));
                userModal.show();
            <?php endif; ?>
        });
    </script>
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
                                    <form action="../procces/process_Admin.php" method="post">
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
                                            <a class="btn btn-primary btn-block" href="../../indexAd.php">Volver</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
    </div>
    
</body>
</html>
