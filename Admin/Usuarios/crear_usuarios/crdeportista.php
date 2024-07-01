<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Registrar Deportista</title>
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
            <h1>Crear una cuenta Deportista</h1>
        </div>

        <!-- Formulario para crear una cuenta de Deportista -->
        <form action="../procces/process_Depo.php" method="post" class="user">
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="nombre_d" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="apellido_d" placeholder="Apellido" required>
            </div>
            <div class="form-group">
                <input type="date" class="form-control form-control-user" name="nacimiendo_d" placeholder="Fecha de nacimiento" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="cedula_d" placeholder="Cedula" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="celular_d" placeholder="Celular" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control form-control-user" name="genero" placeholder="Genero" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Introducir la dirección de correo electrónico..." required>
            </div>
            <!-- Campo desplegable para seleccionar representante -->
            <div class="form-group">
                <div class="select-container">
                    <select class="form-control" name="representante" required>
                        <option value="">Introducir el representante asociado al deportista</option>
                        <?php
                        // Conexión a la base de datos
                        require_once('../conexion/conexion.php');
                        $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO FROM tab_usuarios u
                                    INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
                                    WHERE ut.ID_TIPO = 3");
                        $stmt->execute();
                        $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($representantes as $representante) {
                            echo "<option value='" . $representante['ID_USUARIO'] . "'>" . htmlspecialchars($representante['USUARIO']) . "</option>";
                        }
                        ?>
                    </select>
                    <div class="select-overlay"></div>
                </div>
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