<?php
// Mostrar el mensaje de éxito o error desde process_Depo.php
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
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">Crear una Cuenta de Deportista</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Mensaje de éxito o error -->
                                    <?php echo $message; ?>

                                    <!-- Formulario para crear una cuenta de Deportista -->
                                    <form action="../procces/process_Depo.php" method="post">
                                        <!-- Form Group (nombre) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="nombre_d">Nombre</label>
                                            <input class="form-control" id="nombre_d" name="nombre_d" type="text" placeholder="Ingrese el nombre" required />
                                        </div>
                                        <!-- Form Group (apellido) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="apellido_d">Apellido</label>
                                            <input class="form-control" id="apellido_d" name="apellido_d" type="text" placeholder="Ingrese el apellido" required />
                                        </div>
                                        <!-- Form Group (fecha de nacimiento) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="nacimiento_d">Fecha de Nacimiento</label>
                                            <input class="form-control" id="nacimiento_d" name="nacimiento_d" type="date" placeholder="Ingrese la fecha de nacimiento" required />
                                        </div>
                                        <!-- Form Group (cédula) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="cedula_d">Cédula</label>
                                            <input class="form-control" id="cedula_d" name="cedula_d" type="text" placeholder="Ingrese la cédula" required />
                                        </div>
                                        <!-- Form Group (celular) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="celular_d">Celular</label>
                                            <input class="form-control" id="celular_d" name="celular_d" type="text" placeholder="Ingrese el celular" required />
                                        </div>
                                        <!-- Form Group (género) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="genero">Género</label>
                                            <select class="form-control" id="genero" name="genero" required>
                                                <option value="">Seleccionar el género</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <!-- Form Group (categoría) -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="categoria_d">Categoría</label>
                                            <select class="form-control" id="categoria_d" name="categoria_d" required>
                                                <option value="">Seleccionar la categoría</option>
                                                <?php
                                                // Conexión a la base de datos
                                                require_once('../../configuracion/conexion.php');
                                                $stmt = $conn->prepare("SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias");
                                                $stmt->execute();
                                                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($categorias as $categoria) {
                                                    echo "<option value='" . $categoria['ID_CATEGORIA'] . "'>" . htmlspecialchars($categoria['CATEGORIA']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Campo desplegable para seleccionar representante -->
                                        <div class="mb-3">
                                            <label class="small mb-1" for="representante">Representante</label>
                                            <select class="form-control" id="representante" name="representante" required>
                                                <option value="">Seleccionar el representante asociado al deportista</option>
                                                <?php
                                                // Conexión a la base de datos
                                                $stmt = $conn->prepare("
                                                    SELECT r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE 
                                                    FROM tab_representantes r
                                                    LEFT JOIN tab_usu_tipo ut ON r.ID_REPRESENTANTE = ut.ID_USUARIO AND ut.ID_TIPO = 3
                                                ");
                                                $stmt->execute();
                                                $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($representantes as $representante) {
                                                    echo "<option value='" . $representante['ID_REPRESENTANTE'] . "'>" . htmlspecialchars($representante['NOMBRE_REPRE'] . ' ' . $representante['APELLIDO_REPRE']) . "</option>";
                                                }
                                                ?>
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
    </div>
    <?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
</body>

</html>
