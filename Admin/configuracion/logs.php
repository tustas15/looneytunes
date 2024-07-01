<?php
// Habilita los mensajes de error para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluye la conexión a la base de datos
include_once "'../Admin/configuracion/conexion.php'";

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Depuración de la sesión
var_dump($_SESSION);

// Consulta los registros de actividad para el usuario actual
$idUsuario = $_SESSION['user_id'];
$query = "SELECT * FROM tab_logs WHERE ID_USUARIO = ? ORDER BY DIA_LOG DESC, HORA_LOG DESC";

try {
    // Preparar la consulta
    $stmt = $conn->prepare($query);

    // Verifica si la consulta se preparó correctamente
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Vincula el parámetro y ejecuta la consulta
    $stmt->bindParam(1, $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    // Obtiene el resultado de la consulta
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica si se obtuvieron resultados
    if (empty($result)) {
        echo "<p>No hay registros de actividad para mostrar.</p>";
    }

    // Cierra la consulta
    $stmt->closeCursor();

} catch (Exception $e) {
    echo "Hubo un problema con la consulta: " . $e->getMessage();
    exit();
}

// Cierra la conexión a la base de datos
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Registro de Actividades</title>

    <!-- Custom fonts for this template-->
    <link href="../Assetfree/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../Assetfree/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png">

</head>

<body class="bg-gradient-primary">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Registro de Actividades</h1>
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Evento</th>
                                                <th>Dirección IP</th> <!-- Nueva columna para la dirección IP -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Itera sobre los registros y muestra cada uno en la tabla
                                            if (!empty($result)) {
                                                foreach ($result as $row) {
                                                    echo "<tr>";
                                                    
                                                    // Muestra la fecha, la hora y el evento
                                                    echo !empty($row['DIA_LOG']) ? "<td>" . htmlspecialchars($row['DIA_LOG'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                    echo !empty($row['HORA_LOG']) ? "<td>" . htmlspecialchars($row['HORA_LOG'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                    echo !empty($row['EVENTO']) ? "<td>" . htmlspecialchars($row['EVENTO'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                    
                                                    // Muestra la dirección IP
                                                    echo !empty($row['IP']) ? "<td>" . htmlspecialchars($row['IP'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                    
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>No hay registros de actividad para mostrar.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <a href="../index.php" class="btn btn-primary btn-user btn-block">
                                        Volver al Inicio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../Assetfree/vendor/jquery/jquery.min.js"></script>
    <script src="../Assetfree/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../Assetfree/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../Assetfree/js/sb-admin-2.min.js"></script>
</body>

</html>
