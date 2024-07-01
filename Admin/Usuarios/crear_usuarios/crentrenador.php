<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Registrar Entrenador</title>
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
            <h1>Crear una cuenta Entrenador</h1>
        </div>
        
        <!-- Reemplazar el formulario actual con el formulario de login -->
        <form action="../procces/process_form.php" method="post" class="user">
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="nombre" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="apellido" placeholder="Apellido" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="experiencia" placeholder="Experiencia" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="celular" placeholder="Celular" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="correo" placeholder="Correo" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="direccion" placeholder="Direccion" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="cedula" placeholder="Cedula" required>
            </div>
            <button type="submit" class="btn btn-primary btn-user btn-block">Registrar Cuenta</button>
        </form>
        <!-- Fin del formulario de login -->
        
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
