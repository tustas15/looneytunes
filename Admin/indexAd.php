<?php
// Conexión a la base de datos
require_once('../admin/configuracion/conexion.php');
session_start();

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

try {
    // Consulta SQL para obtener todas las categorías y el número de deportistas por categoría
    $sql = "SELECT c.ID_CATEGORIA, c.CATEGORIA, c.LIMITE_DEPORTISTAS, COUNT(cd.id_deportista) AS num_deportistas
            FROM tab_categorias c
            LEFT JOIN tab_categoria_deportista cd ON c.ID_CATEGORIA = cd.id_categoria
            GROUP BY c.ID_CATEGORIA, c.CATEGORIA, c.LIMITE_DEPORTISTAS";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($categorias === false) {
        throw new Exception("Error al obtener las categorías.");
    }
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
    exit();
}


// Procesar formulario para modificar el límite de deportistas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_limite'])) {
    try {
        $idCategoria = $_POST['categoria_limite']; // Cambié el nombre para que coincida con el campo del formulario
        $nuevoLimite = $_POST['nuevo_limite'];

        // Consulta para actualizar el límite de deportistas
        $sql = "UPDATE tab_categorias SET LIMITE_DEPORTISTAS = ? WHERE ID_CATEGORIA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevoLimite, $idCategoria]);

        // Obtén el nombre de la categoría para el registro en logs
        $get_categoria_sql = "SELECT CATEGORIA FROM tab_categorias WHERE ID_CATEGORIA = :categoria_id";
        $get_categoria_stmt = $conn->prepare($get_categoria_sql);
        $get_categoria_stmt->bindParam(':categoria_id', $idCategoria); // Corrección aquí
        $get_categoria_stmt->execute();
        $categoria = $get_categoria_stmt->fetchColumn();

        // Obtén el ID del usuario actual y la IP
        $user_id = $_SESSION['user_id']; // Asegúrate de que $_SESSION['user_id'] esté bien definido
        $ip = $_SERVER['REMOTE_ADDR']; // Asegúrate de que la IP esté correctamente obtenida

        // Registra la acción en tab_logs
        $log_action = "Límite modificado ". $nuevoLimite ." en "  . $categoria;;
        $sqlLog = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:user_id, :evento, CURRENT_TIME(), CURRENT_DATE(), :ip, 'nuevo_limite_categoria_deportistas_definido')";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->bindParam(':user_id', $user_id);
        $stmtLog->bindParam(':evento', $log_action);
        $stmtLog->bindParam(':ip', $ip);
        $stmtLog->execute();

        // Redirigir a la misma página para evitar resubida de formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar el límite de deportistas: " . $e->getMessage();
    }
}

try {
    // Consulta SQL para obtener conteo de usuarios
    $sql = "SELECT
            (SELECT COUNT(*) FROM tab_administradores) AS administradores,
            (SELECT COUNT(*) FROM tab_entrenadores) AS entrenadores,
            (SELECT COUNT(*) FROM tab_representantes) AS representantes,
            (SELECT COUNT(*) FROM tab_deportistas) AS deportistas";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados como un array asociativo
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
    exit();
}

// Obtener logs de actividad
try {
    $idUsuario = $_SESSION['user_id'];
    $logsPerPage = 13;  // Número de logs por página
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Página actual
    $offset = ($page - 1) * $logsPerPage;

    // Consulta para obtener los logs
    $query = "SELECT * FROM tab_logs WHERE ID_USUARIO = ? ORDER BY DIA_LOG DESC, HORA_LOG DESC LIMIT ? OFFSET ?";
    $stmtLogs = $conn->prepare($query);
    if ($stmtLogs === false) {
        throw new Exception("Error al preparar la consulta: " . $conn->errorInfo()[2]);
    }
    $stmtLogs->bindParam(1, $idUsuario, PDO::PARAM_INT);
    $stmtLogs->bindParam(2, $logsPerPage, PDO::PARAM_INT);
    $stmtLogs->bindParam(3, $offset, PDO::PARAM_INT);
    $stmtLogs->execute();
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de logs
    $totalLogsQuery = "SELECT COUNT(*) as total FROM tab_logs WHERE ID_USUARIO = ?";
    $stmtTotalLogs = $conn->prepare($totalLogsQuery);
    $stmtTotalLogs->bindParam(1, $idUsuario, PDO::PARAM_INT);
    $stmtTotalLogs->execute();
    $totalLogs = $stmtTotalLogs->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalLogs / $logsPerPage);

    // Mensaje si no hay logs
    if (empty($logs)) {
        $logsMessage = "<p>No hay registros de actividad para mostrar.</p>";
    }

    // Cerrar cursores
    $stmtLogs->closeCursor();
} catch (Exception $e) {
    echo "Hubo un problema con la consulta: " . $e->getMessage();
    exit();
}

$conn = null;

// Función para calcular el tiempo transcurrido en formato legible
function timeElapsedString($datetime, $full = false)
{
    $now = new DateTime;  // Crear un objeto DateTime con la hora actual.
    $ago = new DateTime($datetime);  // Crear un objeto DateTime con la fecha y hora proporcionadas.
    $diff = $now->diff($ago);  // Calcular la diferencia entre la hora actual y la proporcionada.

    $diff->w = floor($diff->d / 7);  // Calcular el número de semanas.
    $diff->d -= $diff->w * 7;  // Ajustar los días restantes después de contabilizar las semanas.

    // Definir una matriz que asocia cada unidad de tiempo con su nombre en singular.
    $string = [
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    ];

    // Crear una matriz que asocia cada unidad de tiempo con su nombre en plural.
    $stringPlural = [
        'y' => 'años',
        'm' => 'meses',
        'w' => 'semanas',
        'd' => 'días',
        'h' => 'horas',
        'i' => 'minutos',
        's' => 'segundos',
    ];

    // Recorrer cada unidad de tiempo en la matriz.
    foreach ($string as $k => &$v) {
        // Si la diferencia para esta unidad de tiempo es diferente de cero,
        // guardar el nombre en singular o plural según corresponda.
        if ($diff->$k) {
            $v = $diff->$k . ' ' . ($diff->$k > 1 ? $stringPlural[$k] : $v);
        } else {
            // Si la diferencia es cero, eliminar la entrada de la matriz.
            unset($string[$k]);
        }
    }

    // Devolver la diferencia de tiempo formateada.
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? '' . implode(', ', $string) : 'justo ahora';
}

include './includespro/header.php';
?>
<style>
    .bg-green {
        background-color: #28a745;
    }

    .bg-red {
        background-color: #dc3545;
    }

    .bg-purple {
        background-color: #6f42c1;
    }

    .bg-yellow {
        background-color: #ffc107;
    }

    .bg-blue {
        background-color: #007bff;
    }

    .bg-orange {
        background-color: #fd7e14;
    }

    .bg-pink {
        background-color: #e83e8c;
    }

    .bg-teal {
        background-color: #20c997;
    }

    .bg-gray {
        background-color: #6c757d;
    }

    .bg-teal {
        background-color: #20c997;
    }
</style>
<main>
    <!-- Mostrar Mensajes -->
    <?php
    if (isset($_SESSION['message'])) {
        $message_type = $_SESSION['message_type'] ?? 'info';
        echo '<div class="container mt-3">';
        echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['message'] . '</div>';
        echo '</div>';
        // Borrar el mensaje de la sesión después de mostrarlo
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </h1>
                        <div class="page-header-subtitle">Descripción general del panel y resumen de contenido</div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <!--Message Welcome-->
            <div class="col-xxl-4 col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-body h-100 p-5">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-xxl-12">
                                <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                    <h1 class="text-primary">Bienvenido, <?= $nombre ?>.</h1>
                                    <p class="text-gray-700 mb-0">¡Explore nuestro kit de herramientas de interfaz de usuario completamente diseñado! Explore nuestras páginas de aplicaciones, componentes y utilidades prediseñadas.</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Recent Activity-->
            <div class="col-xxl-4 col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header">
                        Actividades Recientes
                        
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-xs">
                            <?php if (!empty($logs)) : ?>
                                <?php foreach ($logs as $log) : ?>
                                    <div class="timeline-item">
                                        <div class="timeline-item-marker">
                                            <div class="timeline-item-marker-text">
                                                <?php
                                                // Convertimos la fecha y la hora en un solo valor datetime
                                                $datetime = $log['DIA_LOG'] . ' ' . $log['HORA_LOG'];
                                                echo timeElapsedString($datetime);
                                                ?>
                                            </div>
                                            <div class="timeline-item-marker-indicator <?php
                                                                                        // Definir el color según el tipo de evento
                                                                                        switch ($log['TIPO_EVENTO']) {
                                                                                            case 'inicio_sesion':
                                                                                                echo 'bg-green';
                                                                                                break;
                                                                                            case 'cierre_sesion':
                                                                                                echo 'bg-red';
                                                                                                break;
                                                                                            case 'nuevo_usuario':
                                                                                                echo 'bg-purple';
                                                                                                break;
                                                                                            case 'subida_base_datos':
                                                                                                echo 'bg-yellow';
                                                                                                break;
                                                                                            case 'nuevo_producto_creado':
                                                                                                echo 'bg-blue';
                                                                                                break;
                                                                                            case 'nueva_categoria_producto_creado':
                                                                                                echo 'bg-orange';
                                                                                                break;
                                                                                            case 'nueva_categoria_deportista_creado':
                                                                                                echo 'bg-pink';
                                                                                                break;
                                                                                            case 'nuevo_informe_enviado':
                                                                                                echo 'bg-teal';
                                                                                                break;
                                                                                            case 'nuevo_pago_agregado':
                                                                                                echo 'bg-gray';
                                                                                                break;
                                                                                            case 'nuevo_limite_categoria_deportistas_definido':
                                                                                                echo 'bg-green';
                                                                                                break;
                                                                                            case 'usuario_inactivo':
                                                                                                echo 'bg-red';
                                                                                                break;
                                                                                            case 'usuario_activo':
                                                                                                echo 'bg-purple';
                                                                                                break;
                                                                                            case 'actualizacion_perfil':
                                                                                                echo 'bg-teal'; // Asegúrate de que este color esté definido en tu CSS
                                                                                                break;
                                                                                            default:
                                                                                                echo 'bg-gray';
                                                                                                break;
                                                                                        }
                                                                                        ?>"></div>
                                        </div>
                                        <div class="timeline-item-content">
                                            <?php echo htmlspecialchars($log['EVENTO'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1) : ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Anterior">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <?php if ($page < $totalPages) : ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Siguiente">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php else : ?>
                                <p>No hay registros de actividad para mostrar.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Categorías -->
            <div class="col-xxl-4 col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header">
                        Categorías del Club
                        <div class="dropdown no-caret">
                            <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="text-gray-500" data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modificarLimiteModal">
                                    <div class="dropdown-item-icon"><i class="text-gray-500" data-feather="edit"></i></div>
                                    Establecer límite
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($categorias) && is_array($categorias) && !empty($categorias)) : ?>
                            <?php foreach ($categorias as $categoria) :
                                // Verifica si el límite de deportistas existe y es mayor que 0
                                $limite = isset($categoria['LIMITE_DEPORTISTAS']) && $categoria['LIMITE_DEPORTISTAS'] > 0 ? $categoria['LIMITE_DEPORTISTAS'] : 20;
                                $numDeportistas = isset($categoria['num_deportistas']) ? $categoria['num_deportistas'] : 0;

                                // Evita la división por cero
                                $percentage = ($limite > 0) ? ($numDeportistas / $limite) * 100 : 0;
                            ?>
                                <h4 class="small">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                    <span class="float-end fw-bold"><?php echo $numDeportistas; ?> / <?php echo $limite; ?></span>
                                </h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar 
                            <?php
                                // Cambia el color de la barra en función del porcentaje
                                if ($percentage <= 25) {
                                    echo 'bg-danger'; // Rojo para <= 25%
                                } elseif ($percentage <= 50) {
                                    echo 'bg-warning'; // Amarillo para <= 50%
                                } elseif ($percentage <= 75) {
                                    echo 'bg-info'; // Azul para <= 75%
                                } else {
                                    echo 'bg-success'; // Verde para > 75%
                                }
                            ?>" role="progressbar" style="width: <?php echo $percentage; ?>%;" aria-valuenow="<?php echo $numDeportistas; ?>" aria-valuemin="0" aria-valuemax="<?php echo $limite; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No se encontraron categorías.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer position-relative">
                        <div class="d-flex align-items-center justify-content-between small text-body">
                            <a class="stretched-link text-body" href="./categorias/revisar_categorias.php">Revisar Categorías</a>
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Modificar Límite de Deportistas -->
            <div class="modal fade" id="modificarLimiteModal" tabindex="-1" aria-labelledby="modificarLimiteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modificarLimiteModalLabel">Modificar Límite de Deportistas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="categoria_limite" class="form-label">Seleccionar Categoría</label>
                                    <select class="form-select" id="categoria_limite" name="categoria_limite" required>
                                        <?php foreach ($categorias as $categoria) : ?>
                                            <option value="<?php echo $categoria['ID_CATEGORIA']; ?>"><?php echo htmlspecialchars($categoria['CATEGORIA']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nuevo_limite" class="form-label">Nuevo Límite de Deportistas</label>
                                    <input type="number" class="form-control" id="nuevo_limite" name="nuevo_limite" min="1" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="modificar_limite" class="btn btn-primary">Modificar Límite</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Example Colored Cards for Dashboard Demo-->
            <div class="row">
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="text-white-75 small">Administradores</div>
                                    <div class="text-lg fw-bold number" data-role="administradores"><?php echo $result['administradores']; ?></div>
                                </div>
                                <i class="feather-xl text-white-50" data-feather="user"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <a class="text-white stretched-link" href="../admin/configuracion/busqueda/indexadministrador.php">Listado</a>
                            <div class="text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="text-white-75 small">Entrenadores</div>
                                    <div class="text-lg fw-bold number" data-role="entrenadores"><?php echo $result['entrenadores']; ?></div>
                                </div>
                                <i class="feather-xl text-white-50" data-feather="clipboard"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <a class="text-white stretched-link" href="../admin/configuracion/busqueda/indexentrenador.php">Listado</a>
                            <div class="text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="text-white-75 small">Representantes</div>
                                    <div class="text-lg fw-bold number" data-role="representantes"><?php echo $result['representantes']; ?></div>
                                </div>
                                <i class="feather-xl text-white-50" data-feather="check-square"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <a class="text-white stretched-link" href="../admin/configuracion/busqueda/indexrepresentante.php">Listado</a>
                            <div class="text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="text-white-75 small">Deportistas</div>
                                    <div class="text-lg fw-bold number" data-role="deportistas"><?php echo $result['deportistas']; ?></div>
                                </div>
                                <i class="feather-xl text-white-50" data-feather="dribbble"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <a class="text-white stretched-link" href="../admin/configuracion/busqueda/indexdeportista.php">Listado</a>
                            <div class="text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Example Charts for Dashboard Demo-->
            <div class="row">
                <div class="col-xl-6 mb-4">
                    <div class="card card-header-actions h-100">
                        <div class="card-header">
                            Desgloce de Ganancias
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="areaChartDropdownExample" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="text-gray-500" data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="areaChartDropdownExample">
                                    <a class="dropdown-item" href="#!">Last 12 Months</a>
                                    <a class="dropdown-item" href="#!">Last 30 Days</a>
                                    <a class="dropdown-item" href="#!">Last 7 Days</a>
                                    <a class="dropdown-item" href="#!">This Month</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#!">Custom Range</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area"><canvas id="myAreaChart" width="100%" height="30"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mb-4">
                    <!-- Pie chart with legend example-->
                    <div class="card h-100">
                        <div class="card-header">Traffic Sources</div>
                        <div class="card-body">
                            <div class="chart-pie mb-4"><canvas id="myPieChart" width="100%" height="50"></canvas></div>
                            <div class="list-group list-group-flush" id="trafficSourcesList">
                                <!-- La lista se actualizará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        fetch('configuracion/procesar_datos.php')
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    console.error(data.error);
                                    return;
                                }

                                var categorias = Array.isArray(data.categorias) ? data.categorias : [];
                                var cantidades = Array.isArray(data.cantidades) ? data.cantidades : [];

                                // Crear gráfico de pastel
                                var ctx = document.getElementById('myPieChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: categorias,
                                        datasets: [{
                                            data: cantidades,
                                            backgroundColor: ['#007bff', '#6f42c1', '#28a745'],
                                        }]
                                    }
                                });

                                // Actualizar la lista de fuentes de tráfico
                                var list = document.getElementById('trafficSourcesList');
                                list.innerHTML = ''; // Limpiar la lista existente

                                categorias.forEach((categoria, index) => {
                                    var colorClass = ['text-blue', 'text-purple', 'text-green'][index % 3];
                                    var listItem = document.createElement('div');
                                    listItem.className = 'list-group-item d-flex align-items-center justify-content-between small px-0 py-2';
                                    listItem.innerHTML = `
                            <div class="me-3">
                                <i class="fas fa-circle fa-sm me-1 ${colorClass}"></i>
                                ${categoria}
                            </div>
                            <div class="fw-500 text-dark">${cantidades[index]}%</div>
                        `;
                                    list.appendChild(listItem);
                                });
                            })
                            .catch(error => console.error('Error:', error));
                    });
                </script>
            </div>

            <!-- Tarjeta para generar Informes -->
            <div class="card mb-4">
                <div class="card-body py-5">
                    <div class="d-flex flex-column justify-content-center">
                        <img class="img-fluid mb-4" src="../assets/img/illustrations/data-report.svg" alt="" style="height: 10rem" />
                        <div class="text-center px-0 px-lg-5">
                            <h5>¡Genera Reportes ahora!</h5>
                            <p class="mb-4">Nuestro sistema de generación de reportes ya está en línea. Puede comenzar a crear reportes personalizados para excel y pdf disponible en su cuenta.</p>
                            <button type="button" class="btn btn-primary p-3" data-bs-toggle="modal" data-bs-target="#reportModal">
                                Empezar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel">Generar Informe</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="reportForm" method="POST" action="./configuracion/generate_report.php">
                                <div class="mb-3">
                                    <label for="reportType" class="form-label">Tipo de Informe</label>
                                    <select class="form-select" id="reportType" name="report_type" required>
                                        <option value="">Seleccione...</option>
                                        <option value="administradores">Administradores</option>
                                        <option value="entrenadores">Entrenadores</option>
                                        <option value="representantes">Representantes</option>
                                        <option value="deportistas">Deportistas</option>
                                        <option value="inventario">Inventario</option>
                                        <option value="categorias">Categorías Deportistas</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="reportFormat" class="form-label">Formato de Informe</label>
                                    <select class="form-select" id="reportFormat" name="report_format" required>
                                        <option value="">Seleccione...</option>
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Generar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            include './includespro/footer.php';
            ?>