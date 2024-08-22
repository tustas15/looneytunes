<?php
include '../../configuracion/conexion.php';

// Obtener las categorías de la base de datos
$stmt = $conn->prepare("SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostrar el mensaje de éxito o error desde procces_form.php
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'Registro exitoso') {
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
                                <div class="card-header text-center">
                                    <h3 class="fw-light my-4">Crear una Cuenta de Entrenador</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Mensaje de éxito o error -->
                                    <?php echo $message; ?>

                                    <!-- Formulario para crear una cuenta de Entrenador -->
                                    <form action="../procces/process_form.php" method="post" class="user">
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
                                        <!-- Form Group (categoría) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="categoria">Categoría</label>
                                            <select class="form-control" id="categoria" name="categoria" required>
                                                <option value="">Seleccione una categoría</option>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?php echo $categoria['ID_CATEGORIA']; ?>"><?php echo $categoria['CATEGORIA']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
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
        <?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
    </div>
</body>
