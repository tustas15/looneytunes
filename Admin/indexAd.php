<?php
// Conexión a la base de datos
require_once('../Admin/configuracion/conexion.php');
session_start();

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

// Parámetros para la paginación
$categoriasPorPagina = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $categoriasPorPagina;

try {
    // Consulta SQL para obtener las categorías y el número de deportistas por categoría
    $sql = "SELECT c.ID_CATEGORIA, c.CATEGORIA, COUNT(d.ID_DEPORTISTA) AS num_deportistas
            FROM tab_categorias c
            LEFT JOIN tab_deportistas d ON c.ID_CATEGORIA = d.ID_CATEGORIA
            GROUP BY c.ID_CATEGORIA, c.CATEGORIA
            LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $categoriasPorPagina, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de categorías
    $totalCategoriasQuery = "SELECT COUNT(*) as total FROM tab_categorias";
    $stmtTotalCategorias = $conn->prepare($totalCategoriasQuery);
    $stmtTotalCategorias->execute();
    $totalCategorias = $stmtTotalCategorias->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalCategorias / $categoriasPorPagina);

} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}

// Procesar formulario de creación de categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    try {
        $nuevaCategoria = $_POST['nueva_categoria'];
        $sql = "INSERT INTO tab_categorias (CATEGORIA) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevaCategoria]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "Error al crear la categoría: " . $e->getMessage();
    }
}

// Procesar formulario de eliminación de categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_categoria'])) {
    try {
        $idCategoria = $_POST['id_categoria'];
        $sql = "DELETE FROM tab_categorias WHERE ID_CATEGORIA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar la categoría: " . $e->getMessage();
    }
}

try {
    // Consulta SQL
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
}

// Obtener logs de actividad
try {
    $idUsuario = $_SESSION['user_id'];
    $logsPerPage = 10;  // Número de logs por página
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
    // Iterar sobre la matriz para construir la cadena de texto con las diferencias de tiempo.
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');  // Añadir la cantidad y pluralizar si es necesario.
        } else {
            unset($string[$k]);  // Eliminar la unidad de tiempo si no hay diferencia en esa unidad.
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);  // Si $full es false, solo mostrar la unidad de tiempo más significativa.
    return $string ? implode(', ', $string) . ' ago' : 'just now';  // Construir la cadena final.
}

include './includespro/header.php';
?>

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
                        <div class="dropdown no-caret">
                            <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="text-gray-500" data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownMenuButton">
                                <h6 class="dropdown-header">Filter Activity:</h6>
                                <a class="dropdown-item"><span class="badge bg-green-soft text-green my-1">INICIO SESIÓN</span></a>
                                <a class="dropdown-item"><span class="badge bg-red-soft text-red my-1">CIERRE SESIÓN</span></a>
                                <a class="dropdown-item"><span class="badge bg-purple-soft text-purple my-1">USUARIO NUEVO</span></a>
                                <a class="dropdown-item"><span class="badge bg-yellow-soft text-yellow my-1">SUBIDA BASE DATOS</span></a>
                            </div>
                        </div>
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
                            <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="text-gray-500" data-feather="more-vertical"></i></button>
                            <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#crearCategoriaModal">
                                    <div class="dropdown-item-icon"><i class="text-gray-500" data-feather="plus-circle"></i></div>
                                    Agregar Categoría
                                </a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#eliminarCategoriaModal">
                                    <div class="dropdown-item-icon"><i class="text-gray-500" data-feather="minus-circle"></i></div>
                                    Eliminar Categoría
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($categorias as $categoria) : ?>
                            <h4 class="small">
                                <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                <span class="float-end fw-bold"><?php echo $categoria['num_deportistas']; ?> / 20</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar 
                                    <?php 
                                    $percentage = ($categoria['num_deportistas'] / 20) * 100; 
                                    // Puedes cambiar los colores aquí
                                    if ($percentage <= 25) {
                                        echo 'bg-danger'; // Rojo para <= 25%
                                    } elseif ($percentage <= 50) {
                                        echo 'bg-warning'; // Amarillo para <= 50%
                                    } elseif ($percentage <= 75) {
                                        echo 'bg-info'; // Azul para <= 75%
                                    } else {
                                        echo 'bg-success'; // Verde para > 75%
                                    }
                                    ?>" 
                                    role="progressbar" 
                                    style="width: <?php echo $percentage; ?>%" 
                                    aria-valuenow="<?php echo $categoria['num_deportistas']; ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="20">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer position-relative">
                        <div class="d-flex align-items-center justify-content-between small text-body">
                            <a class="stretched-link text-body" href="./configuracion/revisar_categorias.php">Revisar Categorías</a>
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear Categoría -->
            <div class="modal fade" id="crearCategoriaModal" tabindex="-1" aria-labelledby="crearCategoriaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="crearCategoriaModalLabel">Agregar Nueva Categoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nueva_categoria" class="form-label">Nombre de la Categoría</label>
                                    <input type="text" class="form-control" id="nueva_categoria" name="nueva_categoria" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="crear_categoria" class="btn btn-primary">Crear Categoría</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Eliminar Categoría -->
            <div class="modal fade" id="eliminarCategoriaModal" tabindex="-1" aria-labelledby="eliminarCategoriaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eliminarCategoriaModalLabel">Eliminar Categoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">Seleccionar Categoría</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <?php foreach ($categorias as $categoria) : ?>
                                            <option value="<?php echo $categoria['ID_CATEGORIA']; ?>"><?php echo htmlspecialchars($categoria['CATEGORIA']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="eliminar_categoria" class="btn btn-danger">Eliminar Categoría</button>
                            </div>
                        </form>
                    </div>
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
                        <a class="text-white stretched-link" href="../Admin/configuracion/busqueda/indexadministrador.php">View Report</a>
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
                        <a class="text-white stretched-link" href="../Admin/configuracion/busqueda/indexentrenador.php">View Report</a>
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
                        <a class="text-white stretched-link" href="../Admin/configuracion/busqueda/indexrepresentante.php">View Tasks</a>
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
                        <a class="text-white stretched-link" href="../Admin/configuracion/busqueda/indexdeportista.php">View Requests</a>
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
                <div class="card card-header-actions h-100">
                    <div class="card-header">
                        Ganancia Mensual
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
                        <div class="chart-bar"><canvas id="myBarChart" width="100%" height="30"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body py-5">
                <div class="d-flex flex-column justify-content-center">
                    <img class="img-fluid mb-4" src="../assets/img/illustrations/data-report.svg" alt="" style="height: 10rem" />
                    <div class="text-center px-0 px-lg-5">
                        <h5>¡Nuevos informes están aquí! ¡Genera informes personalizados ahora!</h5>
                        <p class="mb-4">Nuestro nuevo sistema de generación de informes ya está en línea. Puede comenzar a crear informes personalizados para cualquier documento disponible en su cuenta.</p>
                        <a class="btn btn-primary p-3" href="#!">Empezar</a>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include './includespro/footer.php';
        ?>