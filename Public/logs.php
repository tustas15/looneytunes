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
include_once "../admin/configuracion/conexion.php";

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

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
        $noRecords = true;
    }

    // Cierra la consulta
    $stmt->closeCursor();

} catch (Exception $e) {
    echo "Hubo un problema con la consulta: " . $e->getMessage();
    exit();
}

// Variable para mostrar el modal
$showModal = false;

// Procesa la solicitud para vaciar los registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaciar_logs'])) {
    try {
        // Ejecuta la consulta para eliminar los registros del usuario actual
        $deleteQuery = "DELETE FROM tab_logs WHERE ID_USUARIO = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bindParam(1, $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        // Activa la variable para mostrar el modal
        $showModal = true;

        // Redirige para evitar reenvíos del formulario
        // header("Refresh: 2; url=logs.php");
        // exit();
    } catch (Exception $e) {
        echo "<p class='alert alert-danger'>Hubo un problema al eliminar los registros: " . $e->getMessage() . "</p>";
    }
}

// Cierra la conexión a la base de datos
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Registro de Actividades - Looney Tunes</title>
    <link href="/looneytunes/assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/Assets/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-backdrop.show {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-lg-12 col-md-9">
                            <div class="card o-hidden border-0 shadow-lg my-5">
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="p-5">
                                                <div class="text-center">
                                                    <h1 class="h4 text-gray-900 mb-4">Registro de Actividades</h1>
                                                </div>
                                                <!-- Formulario para vaciar los registros -->
                                                <form method="post">
                                                    <button type="submit" name="vaciar_logs" class="btn btn-danger btn-user btn-block">Vaciar Registros</button>
                                                </form>
                                                <table class="table mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Hora</th>
                                                            <th>Evento</th>
                                                            <th>Dirección IP</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (!empty($result)) {
                                                            foreach ($result as $row) {
                                                                echo "<tr>";
                                                                echo !empty($row['DIA_LOG']) ? "<td>" . htmlspecialchars($row['DIA_LOG'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                                echo !empty($row['HORA_LOG']) ? "<td>" . htmlspecialchars($row['HORA_LOG'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                                echo !empty($row['EVENTO']) ? "<td>" . htmlspecialchars($row['EVENTO'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                                echo !empty($row['IP']) ? "<td>" . htmlspecialchars($row['IP'], ENT_QUOTES, 'UTF-8') . "</td>" : "<td></td>";
                                                                echo "</tr>";
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='4' class='text-center'>No hay registros de actividad para mostrar.</td></tr>";
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <a href="../index.php" class="btn btn-primary btn-user btn-block">Volver al Inicio</a>
                                            </div>
                                        </div>
                                    </div>
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
                        <div class="col-md-6 small">Copyright &copy; Looney Tunes <span id="currentYear"></span></div>
                        <div class="col-md-6 text-md-end small">
                            <a href="../Public/Privacy_Policy.php">Privacy Policy</a>
                            &middot;
                            <a href="../Public/terms_condition.php">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal de éxito -->
    <?php if ($showModal): ?>
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Registros de actividad eliminados con éxito.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
