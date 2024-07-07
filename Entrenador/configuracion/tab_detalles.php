<?php
// Verificar si el usuario ha iniciado sesión
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit();
}

$cedula_depo = $_POST['cedula_depo'] ?? null;

// Obtener información del deportista
$stmt = $conn->prepare("SELECT ID_DEPORTISTA, NOMBRE_DEPO, APELLIDO_DEPO FROM tab_deportistas WHERE CEDULA_DEPO = :cedula_depo");
$stmt->bindParam(':cedula_depo', $cedula_depo, PDO::PARAM_STR);
$stmt->execute();
$deportista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deportista) {
    echo "No se encontró el deportista con la cédula proporcionada.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_detalles'])) {
    $numero_camisa = $_POST['numero_camisa'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $fecha_ingreso = $_POST['fecha_ingreso'];

    // Obtener el ID_USUARIO correspondiente al deportista
    $stmt = $conn->prepare("SELECT ID_USUARIO, ID_DEPORTISTA FROM tab_deportistas WHERE CEDULA_DEPO = :cedula_depo");
    $stmt->bindParam(':cedula_depo', $cedula_depo, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $id_usuario = $resultado['ID_USUARIO'];
        $id_deportista = $resultado['ID_DEPORTISTA'];

        // Preparar y ejecutar la consulta SQL INSERT
        $stmt = $conn->prepare("INSERT INTO tab_detalles (ID_USUARIO, ID_DEPORTISTA, NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO) VALUES (:id_usuario, :id_deportista, :numero_camisa, :altura, :peso, :fecha_ingreso)");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt->bindParam(':numero_camisa', $numero_camisa, PDO::PARAM_STR);
        $stmt->bindParam(':altura', $altura, PDO::PARAM_STR);
        $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Los detalles se han guardado correctamente.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error al guardar los detalles: " . $stmt->errorInfo()[2];
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: tab_detalles.php?cedula_depo=" . urlencode($cedula_depo));
        exit();
    } else {
        $_SESSION['message'] = "No se encontró el deportista con la cédula proporcionada.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../entrenador/indexentrenador.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Deportista</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="../../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    <script>
        window.onload = function() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById('fecha_ingreso').value = today;
        }
    </script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <!-- Mensaje de éxito o error -->
                            <?php
                            if (isset($_SESSION['message'])) {
                                $message_type = $_SESSION['message_type'] ?? 'info';
                                echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['message'] . '</div>';
                                unset($_SESSION['message']);
                            }
                            ?>
                            <!-- Detalles del Deportista -->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">Detalles del Deportista</h3>
                                </div>
                                <div class="card-body">
                                    <h2>Detalles para: <?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) ?></h2>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <div class="mb-3">
                                            <label for="numero_camisa" class="form-label">Número de Camiseta:</label>
                                            <input type="text" id="numero_camisa" name="numero_camisa" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="altura" class="form-label">Altura:</label>
                                            <input type="text" id="altura" name="altura" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="peso" class="form-label">Peso:</label>
                                            <input type="text" id="peso" name="peso" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso:</label>
                                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control" required>
                                        </div>
                                        <input type="hidden" name="cedula_depo" value="<?= htmlspecialchars($cedula_depo) ?>">
                                        <button type="submit" name="guardar_detalles" class="btn btn-primary">Guardar Detalles</button>
                                        <a href="../indexentrenador.php" class="btn btn-primary">Regresar</a>
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
