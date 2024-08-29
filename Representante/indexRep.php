<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

require_once('../admin/configuracion/conexion.php');


date_default_timezone_set('America/Guayaquil');

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Inicio de sesión


// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Comprobamos si el usuario es entrenador o representante
$id_usuario = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT ID_TIPO FROM tab_usu_tipo WHERE ID_USUARIO = :id_usuario");
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$tipo_usuario = $stmt->fetchColumn();

if ($tipo_usuario != '3') {
    echo "Acceso denegado. No eres un representante.";
    exit();
}



// Obtener información del representante
$stmt = $conn->prepare("SELECT * FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$representante = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre = $representante['NOMBRE_REPRE'] . ' ' . $representante['APELLIDO_REPRE'];

// Obtener los IDs de los deportistas asociados al representante
$stmt = $conn->prepare("SELECT id_deportista FROM tab_representantes_deportistas WHERE id_representante = :id_representante");
$stmt->bindParam(':id_representante', $representante['ID_REPRESENTANTE'], PDO::PARAM_INT);
$stmt->execute();
$deportistas_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Si hay deportistas asociados, obtener sus datos
$hijos = [];
if (!empty($deportistas_ids)) {
    $ids_placeholder = implode(',', array_fill(0, count($deportistas_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM tab_deportistas WHERE ID_DEPORTISTA IN ($ids_placeholder)");
    $stmt->execute($deportistas_ids);
    $hijos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener logs de actividad
try {
    $logsPerPage = 10;  // Número de logs por página
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Página actual
    $offset = ($page - 1) * $logsPerPage;

    $query = "SELECT * FROM tab_logs WHERE ID_USUARIO = :id_usuario ORDER BY DIA_LOG DESC, HORA_LOG DESC LIMIT :limit OFFSET :offset";
    $stmtLogs = $conn->prepare($query);
    $stmtLogs->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtLogs->bindParam(':limit', $logsPerPage, PDO::PARAM_INT);
    $stmtLogs->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmtLogs->execute();
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

    $totalLogsQuery = "SELECT COUNT(*) as total FROM tab_logs WHERE ID_USUARIO = :id_usuario";
    $stmtTotalLogs = $conn->prepare($totalLogsQuery);
    $stmtTotalLogs->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtTotalLogs->execute();
    $totalLogs = $stmtTotalLogs->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalLogs / $logsPerPage);

    if (empty($logs)) {
        $logsMessage = "<p>No hay registros de actividad para mostrar.</p>";
    }
} catch (Exception $e) {
    echo "Hubo un problema con la consulta: " . $e->getMessage();
    exit();
}


// Obtener los informes asociados al representante
try {
    $stmtInformes = $conn->prepare("
        SELECT inf.informe, inf.fecha_creacion, dep.NOMBRE_DEPO, dep.APELLIDO_DEPO
        FROM tab_informes inf
        JOIN tab_deportistas dep ON inf.id_deportista = dep.ID_DEPORTISTA
        WHERE inf.id_representante = :id_representante
        ORDER BY inf.fecha_creacion DESC LIMIT 5
    ");
    $stmtInformes->bindParam(':id_representante', $representante['ID_REPRESENTANTE'], PDO::PARAM_INT);
    $stmtInformes->execute();
    $informes = $stmtInformes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta de informes: " . $e->getMessage();
    exit();
}



// Función para calcular el tiempo transcurrido en formato legible
function timeElapsedString($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    ];
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



include './Includes/header.php';
?>
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
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
                                    <h1 class="text-primary">Bienvenido, representante <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>.</h1>
                                    <p class="text-gray-700 mb-0">Aquí puede ver la información de sus hijos deportistas.</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tarjetas de hijos -->
        <div class="row">
            <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                <h1 class="text-primary">Deportistas</h1>
                <br>
            </div>

            <?php
            $colores = ['bg-primary', 'bg-warning', 'bg-success', 'bg-danger', 'bg-info', 'bg-secondary', 'bg-dark'];
            foreach ($hijos as $index => $hijo) :
                $color = $colores[$index % count($colores)];
            ?>
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card <?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?> text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="text-white-75 small"><?php echo htmlspecialchars($hijo['NOMBRE_DEPO'] . ' ' . $hijo['APELLIDO_DEPO'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-lg fw-bold"></div>
                                </div>
                                <i class="feather-xl text-white-50" data-feather="user"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <a class="text-white stretched-link" href="./perfil.php?id_deportista=<?php echo htmlspecialchars($hijo['ID_DEPORTISTA'], ENT_QUOTES, 'UTF-8'); ?>">Ver perfil</a>
                            <div class="text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Sección de logs -->
        <div class="col-xxl-4 col-xl-6 mb-4">
            <div class="card card-header-actions h-100">
                <div class="card-header">
                    Actividad Reciente
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
                                            $datetime = $log['DIA_LOG'] . ' ' . $log['HORA_LOG'];
                                            echo htmlspecialchars(timeElapsedString($datetime), ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </div>
                                        <div class="timeline-item-marker-indicator <?php
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
                                            <a class="page-link" href="?page=<?php echo htmlspecialchars($page - 1, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Anterior">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                            <a class="page-link" href="?page=<?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($page < $totalPages) : ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo htmlspecialchars($page + 1, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Siguiente">
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
</main>
<?php
include './Includes/footer.php';
?>
