<?php
// Mostrar el mensaje de éxito o error desde process_Repre.php
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
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Registrar Representante</title>
    <link rel="stylesheet" href="css/registro.css">
    <!-- Agregar estilos de SB Admin 2 -->
    <link href="../PLANTILLA-BOOTSTRAP/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../PLANTILLA-BOOTSTRAP/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="header text-center">
            <h1>Crear una cuenta Representante</h1>
        </div>

        <!-- Formulario para crear una cuenta de Representante -->
        <form action="../procces/process_Repre.php" method="post" class="user">
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="nombre_r" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="apellido_r" placeholder="Apellido" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="celular_r" placeholder="Celular" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control form-control-user" name="correo_r" placeholder="Correo" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="direccion_r" placeholder="Direccion" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="cedula_r" placeholder="Cedula" required>
            </div>
            <button type="submit" class="btn btn-primary btn-user btn-block">Registrar Cuenta</button>
        </form>
        <!-- Fin del formulario de login -->

        <?php echo $message; ?>

        <!-- Botón para volver atrás -->
        <div class="text-center mt-4">
            <a href="../index.php" onclick="history.back();" class="btn btn-secondary btn-user">Volver</a>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../PLANTILLA-BOOTSTRAP/vendor/jquery/jquery.min.js"></script>
    <script src="../PLANTILLA-BOOTSTRAP/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../PLANTILLA-BOOTSTRAP/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../PLANTILLA-BOOTSTRAP/js/sb-admin-2.min.js"></script>
</body>

</html>