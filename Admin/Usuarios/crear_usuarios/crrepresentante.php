<?php
// Mostrar el mensaje de éxito o error desde process_Repre.php
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'success') {
        $message = '<div class="alert alert-success" role="alert">Registro exitoso</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($_GET['message']) . '</div>';
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
                            <!-- Basic registration form -->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center">
                                    <h3 class="fw-light my-4">Crear una Cuenta de Representante</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Mensaje de éxito o error -->
                                    <?php echo $message; ?>

                                    <!-- Formulario para crear una cuenta de Representante -->
                                    <form action="../procces/process_Repre.php" method="post" class="user">
                                        <!-- Form Group (nombre) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="nombre_r">Nombre</label>
                                            <input class="form-control form-control-user" id="nombre_r" name="nombre_r" type="text" placeholder="Ingrese el nombre" required />
                                        </div>
                                        <!-- Form Group (apellido) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="apellido_r">Apellido</label>
                                            <input class="form-control form-control-user" id="apellido_r" name="apellido_r" type="text" placeholder="Ingrese el apellido" required />
                                        </div>
                                        <!-- Form Group (celular) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="celular_r">Celular</label>
                                            <input class="form-control form-control-user" id="celular_r" name="celular_r" type="text" placeholder="Ingrese el celular" required />
                                        </div>
                                        <!-- Form Group (correo electrónico) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="correo_r">Correo Electrónico</label>
                                            <input class="form-control form-control-user" id="correo_r" name="correo_r" type="email" placeholder="Ingrese el correo electrónico" required />
                                        </div>
                                        <!-- Form Group (dirección) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="direccion_r">Dirección</label>
                                            <input class="form-control form-control-user" id="direccion_r" name="direccion_r" type="text" placeholder="Ingrese la dirección" required />
                                        </div>
                                        <!-- Form Group (cédula) -->
                                        <div class="form-group mb-3">
                                            <label class="small mb-1" for="cedula_r">Cédula</label>
                                            <input class="form-control form-control-user" id="cedula_r" name="cedula_r" type="text" placeholder="Ingrese la cédula" required />
                                        </div>
                                        <!-- Botón de registro -->
                                        <button class="btn btn-primary btn-block" type="submit">Crear Cuenta</button>
                                        <a class="btn btn-primary btn-block" href="../../indexAd.php">Volver</a> <!-- Botón para volver -->
                                    </form>
                                    <!-- Fin del formulario de registro -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
