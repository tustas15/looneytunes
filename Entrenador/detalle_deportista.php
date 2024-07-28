<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];

// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener y sanitizar el ID del deportista
$id_deportista = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if ($id_deportista <= 0) {
    die("No se especificó un ID de deportista válido.");
}

try {
    $stmt = $conn->prepare("
        SELECT 
            tab_deportistas.*,
            tab_representantes.NOMBRE_REPRE,
            tab_detalles.ID_DETALLE,
            tab_detalles.NUMERO_CAMISA,
            tab_detalles.ALTURA,
            tab_detalles.PESO,
            tab_detalles.FECHA_INGRESO,
            tab_categorias.*
        FROM 
            tab_deportistas
        LEFT JOIN 
            tab_representantes_deportistas ON tab_deportistas.ID_DEPORTISTA = tab_representantes_deportistas.ID_DEPORTISTA
        LEFT JOIN 
            tab_representantes ON tab_representantes_deportistas.ID_REPRESENTANTE = tab_representantes.ID_REPRESENTANTE
        LEFT JOIN 
            tab_detalles ON tab_deportistas.ID_DEPORTISTA = tab_detalles.ID_DEPORTISTA
        LEFT JOIN
            tab_categorias ON tab_deportistas.ID_CATEGORIA = tab_categorias.ID_CATEGORIA
        WHERE 
            tab_deportistas.ID_DEPORTISTA = :id
        ORDER BY 
            tab_detalles.FECHA_INGRESO DESC
    ");
    $stmt->bindParam(':id', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    die("Error al cargar los datos del deportista: " . $e->getMessage());
}

// Extraer los datos para la gráfica
$dataPoints = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($row['ALTURA']) && !empty($row['PESO'])) {
        $altura_m = $row['ALTURA'] / 100;
        $imc = $row['PESO'] / ($altura_m * $altura_m);
        $dataPoints[] = [
            'fecha_ingreso' => $row['FECHA_INGRESO'],
            'imc' => number_format($imc, 2)
        ];
    }
}

$jsonDataPoints = json_encode($dataPoints);

// Incluir el encabezado
include './includes/header.php';
?>

<!-- Añadir el CSS de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-white">Datos del Deportista</h1>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <?php
                        // Reejecutar la consulta para obtener todos los registros nuevamente
                        $stmt->execute();
                        $deportista = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($deportista):
                        ?>
                            <h2><?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) ?></h2>
                            <p><?= htmlspecialchars($deportista['CATEGORIA']) ?></p>
                            <table id="detallesTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha de Ingreso</th>
                                        <th>N° Camisa</th>
                                        <th>Altura (cm)</th>
                                        <th>Peso (kg)</th>
                                        <th>IMC</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Reejecutar la consulta para la tabla de detalles
                                    $stmt->execute();
                                    while ($deportista = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        if (!empty($deportista['ALTURA']) && !empty($deportista['PESO'])) {
                                            $altura_m = $deportista['ALTURA'] / 100;
                                            $imc = $deportista['PESO'] / ($altura_m * $altura_m);
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($deportista['FECHA_INGRESO']) . "</td>";
                                            echo "<td>" . htmlspecialchars($deportista['NUMERO_CAMISA']) . "</td>";
                                            echo "<td>" . htmlspecialchars($deportista['ALTURA']) . "</td>";
                                            echo "<td>" . htmlspecialchars($deportista['PESO']) . "</td>";
                                            echo "<td>" . number_format($imc, 2) . "</td>";
                                            echo "<td><button class='btn btn-danger btn-sm' onclick='eliminarDetalle(" . htmlspecialchars($deportista['ID_DETALLE']) . ")'>Eliminar</button></td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No se encontraron datos para este deportista.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Contenedor para la gráfica -->
            <div class="col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <canvas id="imcChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Incluir el pie de página
include './includes/footer.php';
?>

<!-- Añadir los scripts de DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script>
function eliminarDetalle(idDetalle) {
    if (confirm("¿Estás seguro de que deseas eliminar este detalle?")) {
        window.location.href = 'eliminar_detalle.php?id=' + idDetalle;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Obtener los datos de PHP
    var dataPoints = <?php echo $jsonDataPoints; ?>;
    
    // Preparar los datos para la gráfica
    var labels = dataPoints.map(function(dp) { return dp.fecha_ingreso; });
    var data = dataPoints.map(function(dp) { return dp.imc; });

    // Crear la gráfica
    var ctx = document.getElementById('imcChart').getContext('2d');
    var imcChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'IMC',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'month',
                        displayFormats: {
                            month: 'MMM YYYY'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Fecha de Ingreso'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'IMC'
                    }
                }
            }
        }
    });
});
</script>
