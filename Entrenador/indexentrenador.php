<?php

session_start();
// Conexión a la base de datos
include './includes/header.php';

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}



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

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$categoria_entrenador = isset($_SESSION['entrenador_categoria']) ? $_SESSION['entrenador_categoria'] : 'No asignada';

// Consulta para contar deportistas por categoría
$sql_contar = "SELECT id_categoria, COUNT(*) as cantidad FROM tab_categoria_deportista GROUP BY id_categoria";
$stmt_contar = $conn->query($sql_contar);

$categorias_count = array();

if ($stmt_contar) {
    while ($row = $stmt_contar->fetch(PDO::FETCH_ASSOC)) {
        $categorias_count[$row['id_categoria']] = $row['cantidad'];
    }
}

// Consulta para obtener los nombres de las categorías
$sql_categorias = "SELECT id_categoria, categoria FROM tab_categorias";
$stmt_categorias = $conn->query($sql_categorias);

$categorias = array();

if ($stmt_categorias) {
    while ($row = $stmt_categorias->fetch(PDO::FETCH_ASSOC)) {
        $categorias[$row['id_categoria']] = $row['categoria'];
    }
}
//

try {
    $id_usuario = $_SESSION['user_id'];
    $stmt1 = $conn->prepare("SELECT tab_categorias.CATEGORIA 
                             FROM tab_entrenadores 
                             LEFT JOIN tab_entrenador_categoria ON tab_entrenadores.ID_ENTRENADOR = tab_entrenador_categoria.ID_ENTRENADOR 
                             LEFT JOIN tab_categorias ON tab_entrenador_categoria.ID_CATEGORIA = tab_categorias.ID_CATEGORIA 
                             WHERE tab_entrenadores.ID_USUARIO = :id_usuario");
    $stmt1->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt1->execute();
    $categorias_entrenador = $stmt1->fetchAll(PDO::FETCH_COLUMN);
    $_SESSION['entrenador_categorias'] = $categorias_entrenador;
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los entrenadores: " . $e->getMessage();
    header('Location: error.php'); // Redirigir a una página de error
    exit();
}

// Obtener logs de actividad
try {
    $idUsuario = $_SESSION['user_id'];
    $logsPerPage = 10;  // Número de logs por página
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Página actual
    $offset = ($page - 1) * $logsPerPage;

    $query = "SELECT * FROM tab_logs WHERE ID_USUARIO = :id_usuario ORDER BY DIA_LOG DESC, HORA_LOG DESC LIMIT :limit OFFSET :offset";
    $stmtLogs = $conn->prepare($query);
    $stmtLogs->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
    $stmtLogs->bindParam(':limit', $logsPerPage, PDO::PARAM_INT);
    $stmtLogs->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmtLogs->execute();
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

    $totalLogsQuery = "SELECT COUNT(*) as total FROM tab_logs WHERE ID_USUARIO = :id_usuario";
    $stmtTotalLogs = $conn->prepare($totalLogsQuery);
    $stmtTotalLogs->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
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


$conn = null;

// Función para calcular el tiempo transcurrido en formato legible
function timeElapsedString($datetime, $full = false) {
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
                                    <h1 class="text-primary">Bienvenido, entrenador <?= $nombre ?>.</h1>
                                    <p class="text-gray-700 mb-0"></p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../Assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CARDS 1 -->
        <div class="row">
            <!-- Código HTML para mostrar las tarjetas -->
            <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                <h1 class="text-primary">Categorías</h1>
                <br>
            </div>

            <?php
// Otras partes del código...

$categorias_entrenador = $_SESSION['entrenador_categorias'] ?? [];

foreach ($categorias_entrenador as $nombre) {
    $categoria_id = array_search($nombre, $categorias);
    $cantidad = isset($categorias_count[$categoria_id]) ? $categorias_count[$categoria_id] : 0;

    // Asignar un color e ícono a cada tarjeta
    $color = 'bg-primary'; // Puedes cambiar el color si lo deseas
    $icon = 'users'; // Cambia el ícono si lo prefieres
?>
    <div class="col-lg-6 col-xl-3 mb-4">
        <div class="card <?php echo $color; ?> text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small"><?php echo $nombre; ?></div>
                        <div class="text-lg fw-bold number" data-role="<?php echo strtolower(str_replace(' ', '_', $nombre)); ?>">
                            <?php echo $cantidad; ?>
                        </div>
                    </div>
                    <i class="feather-xl text-white-50" data-feather="<?php echo $icon; ?>"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="report_categoria.php?categoria=<?php echo urlencode($nombre); ?>">Ver listado</a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
<?php
}
?>






        </div>
        
        <div class="row">
          
            <!-- logs -->
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
        </div>
    </div>
</main>
<?php
include './includes/footer.php';
?>