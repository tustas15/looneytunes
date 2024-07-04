<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}
// Inicio de sesión
session_start();
// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}
// Comprobamos si el usuario es entrenador
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}
// Comprobamos si el usuario entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
// Comprobamos si el usuario es entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];

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

// Cargar deportistas desde la base de datos si la variable de sesión está vacía
try {
    $id_usuario = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM TAB_TEMP_DEPORTISTAS WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['deportistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los deportistas: " . $e->getMessage();
    header('Location: error.php'); // Redirigir a una página de error
    exit;
}

// Manejar la eliminación de deportistas
if (isset($_POST['delete'])) {
    include './configuracion/eliminar_deportista.php'; // Mover la lógica de eliminación a un archivo separado
}
// Obtener la lista de deportistas de la sesión
$deportistas = $_SESSION['deportistas'] ?? [];

// Obtener logs de actividad
try {
    $idUsuario = $_SESSION['user_id'];
    $logsPerPage = 10;  // Número de logs por página
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Página actual
    $offset = ($page - 1) * $logsPerPage;

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

    $totalLogsQuery = "SELECT COUNT(*) as total FROM tab_logs WHERE ID_USUARIO = ?";
    $stmtTotalLogs = $conn->prepare($totalLogsQuery);
    $stmtTotalLogs->bindParam(1, $idUsuario, PDO::PARAM_INT);
    $stmtTotalLogs->execute();
    $totalLogs = $stmtTotalLogs->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalLogs / $logsPerPage);

    if (empty($logs)) {
        $logsMessage = "<p>No hay registros de actividad para mostrar.</p>";
    }
    $stmtLogs->closeCursor();
} catch (Exception $e) {
    echo "Hubo un problema con la consulta: " . $e->getMessage();
    exit();
}

$conn = null;

// Función para calcular el tiempo transcurrido en formato legible
function timeElapsedString($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

include './includes/header.php';
?>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <!--<div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </h1>
                        <div class="page-header-subtitle">Descripción general del panel y resumen de contenido</div>
                    </div>
                </div>-->
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-n10">

        <div class="row">
            <div class="col-xxl-4 col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-body h-100 p-5">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-xxl-12">
                                <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                    <h1 class="text-primary">Bienvenido, entrenador <?= $nombre ?>.</h1>
                                    <p class="text-gray-700 mb-0"></p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- CARDS 1-->
        <div class="row">
            <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                <h1 class="text-primary">Categorias</h1>
                <p class="text-gray-700 mb-0"></p>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">MOSQUITOS</div>
                                <div class="text-lg fw-bold number" data-role="administradores"><?php echo $result['administradores']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="user"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="#!">View Report</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">PRE MINI</div>
                                <div class="text-lg fw-bold number" data-role="entrenadores"><?php echo $result['entrenadores']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="clipboard"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="#!">View Report</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">MINI DAMAS</div>
                                <div class="text-lg fw-bold number" data-role="representantes"><?php echo $result['representantes']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="check-square"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="">View Tasks</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">MINI HOBRES</div>
                                <div class="text-lg fw-bold number" data-role="deportistas"><?php echo $result['deportistas']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="dribbble"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="">View Requests</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

        </div>
        <!-- CARDS 2-->
        <div class="row">
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">U13 DAMAS</div>
                                <div class="text-lg fw-bold number" data-role="administradores"><?php echo $result['administradores']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="user"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="#!">View Report</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">U13 HOMBRES</div>
                                <div class="text-lg fw-bold number" data-role="entrenadores"><?php echo $result['entrenadores']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="clipboard"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="#!">View Report</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">U15 DAMAS</div>
                                <div class="text-lg fw-bold number" data-role="representantes"><?php echo $result['representantes']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="check-square"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="">View Tasks</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 mb-4">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="text-white-75 small">U15 HOBRES</div>
                                <div class="text-lg fw-bold number" data-role="deportistas"><?php echo $result['deportistas']; ?></div>
                            </div>
                            <i class="feather-xl text-white-50" data-feather="dribbble"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-white stretched-link" href="">View Requests</a>
                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- logs -->
            <h1>Actividad Reciente</h1>
            <!-- logs -->
            <div class="col-xxl-4 col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header">
                        Recent Activity
                        <div class="dropdown no-caret">
                            <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="text-gray-500" data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownMenuButton">
                                <h6 class="dropdown-header">Filter Activity:</h6>
                                <a class="dropdown-item" href="#!"><span class="badge bg-green-soft text-green my-1">Commerce</span></a>
                                <a class="dropdown-item" href="#!"><span class="badge bg-blue-soft text-blue my-1">Reporting</span></a>
                                <a class="dropdown-item" href="#!"><span class="badge bg-yellow-soft text-yellow my-1">Server</span></a>
                                <a class="dropdown-item" href="#!"><span class="badge bg-purple-soft text-purple my-1">Users</span></a>
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
            <div class="col-xxl-4 col-xl-6 mb-4">
                <!-- Team members / people dashboard card example-->
                <div class="card mb-4">
                    <div class="card-header">People</div>
                    <div class="card-body">
                        <!-- Item 1-->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-1.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Sid Rooney</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople1" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople1">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Item 2-->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-2.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Keelan Garza</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople2" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople2">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Item 3-->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-3.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Kaia Smyth</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople3" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople3">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Item 4-->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-4.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Kerri Kearney</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople4">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Item 5-->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-5.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Georgina Findlay</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople5" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople5">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Item 6-->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-shrink-0 me-3">
                                <div class="avatar avatar-xl me-3 bg-gray-200"><img class="avatar-img img-fluid" src="assets/img/illustrations/profiles/profile-6.png" alt="" /></div>
                                <div class="d-flex flex-column fw-bold">
                                    <a class="text-dark line-height-normal mb-1" href="#!">Wilf Ingram</a>
                                    <div class="small text-muted line-height-normal">Position</div>
                                </div>
                            </div>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownPeople6" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownPeople6">
                                    <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a>
                                    <a class="dropdown-item" href="#!">Something else here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </ol>
        <!-- Formulario para ingresar cédula -->
<div class="card mb-4">
    <div class="card-header">Registrar Deportista</div>
    <div class="card-body">
        <form action="registrar.php" method="POST">
            <div class="mb-3">
                <label for="cedula" class="form-label">Número de Cédula</label>
                <input type="text" class="form-control" id="cedula" name="cedula_r" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
</div>

<!-- Tabla de Deportistas Temporales -->
<div class="card mb-4">
    <div class="card-header">LISTA DEPORTISTAS</div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Cédula</th>
                    <th>Número de Celular</th>
                    <th>Género</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Cédula</th>
                    <th>Número de Celular</th>
                    <th>Género</th>
                    <th>Acciones</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                // Fetch data from the TEMP_DEPORTISTAS table
                $sql = "SELECT * FROM TAB_TEMP_DEPORTISTAS WHERE ID_USUARIO = :id_usuario";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $tempDeportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tempDeportistas as $deportista) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($deportista['NOMBRE_DEPO']) . '</td>';
                    echo '<td>' . htmlspecialchars($deportista['APELLIDO_DEPO']) . '</td>';
                    echo '<td>' . htmlspecialchars($deportista['FECHA_NACIMIENTO']) . '</td>';
                    echo '<td>' . htmlspecialchars($deportista['CEDULA_DEPO']) . '</td>';
                    echo '<td>' . htmlspecialchars($deportista['NUMERO_CELULAR']) . '</td>';
                    echo '<td>' . htmlspecialchars($deportista['GENERO']) . '</td>';
                    echo '<td>
                    <form method="POST" action="eliminar_deportista.php">
                        <input type="hidden" name="id_deportista" value="' . htmlspecialchars($deportista['ID_DEPORTISTA']) . '">
                        <button type="submit" name="delete" class="btn btn-datatable btn-icon btn-transparent-dark me-2"><i data-feather="trash-2"></i></button>
                    </form>
                  </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php
include './includes/footer.php';
?>