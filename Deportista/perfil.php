<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Verificar que se haya recibido el ID del deportista
if (isset($_GET['id_deportista'])) {
    $id_deportista = $_GET['id_deportista'];
} else {
    // Si no hay ID, redirigir a la página principal o mostrar un error
    header("Location: index.php");
    exit();
}

// Obtener los datos del deportista seleccionado
$sql_deportista = "SELECT * FROM tab_deportistas WHERE ID_DEPORTISTA = :id_deportista";
$stmt_deportista = $conn->prepare($sql_deportista);
$stmt_deportista->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
$stmt_deportista->execute();
$deportista = $stmt_deportista->fetch(PDO::FETCH_ASSOC);

if (!$deportista) {
    echo "Deportista no encontrado.";
    exit();
}

// Obtener los detalles del deportista
$sql_detalles = "SELECT * FROM tab_detalles WHERE ID_DEPORTISTA = :id_deportista ORDER BY FECHA_INGRESO DESC";
$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
$stmt_detalles->execute();
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

$imc_data = array();  // Array para almacenar los datos de IMC y fechas

include './Includes/header.php';
?>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-xxl-4 col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-body h-100 p-5">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-xxl-12">
                                <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                    <h2 class="text-primary">Perfil de <?= htmlspecialchars($deportista['NOMBRE_DEPO']) ?>.</h2>
                                    <p class="text-gray-700 mb-0"></p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabla de detalles -->
        <div class="row">
            <div class="col-12">
                <?php if ($detalles): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Fecha de Ingreso</th>
                                            <th>Número de Camisa</th>
                                            <th>Altura</th>
                                            <th>Peso</th>
                                            <th>IMC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles as $detalle): ?>
                                            <?php
                                            $altura = floatval($detalle['ALTURA']) / 100; // Convertir cm a m
                                            $peso = floatval($detalle['PESO']);
                                            $imc = round($peso / ($altura * $altura), 2); // Calcular y redondear IMC
                                            $imc_data[] = array('fecha' => $detalle['FECHA_INGRESO'], 'imc' => $imc);
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($detalle['FECHA_INGRESO']) ?></td>
                                                <td><?= htmlspecialchars($detalle['NUMERO_CAMISA']) ?></td>
                                                <td><?= htmlspecialchars($detalle['ALTURA']) ?></td>
                                                <td><?= htmlspecialchars($detalle['PESO']) ?></td>
                                                <td><?= $imc ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No se encontraron detalles para este deportista.</div>
                <?php endif; ?>
                <!-- Modal para la gráfica -->
                <div class="modal fade" id="imcModal" tabindex="-1" aria-labelledby="imcModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imcModalLabel">Gráfica de IMC</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <canvas id="imcChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Gráfica de IMC -->



    </div>

</main>

<?php
include './Includes/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var imcModal = document.getElementById('imcModal');
        imcModal.addEventListener('shown.bs.modal', function() {
            var ctx = document.getElementById('imcChart').getContext('2d');
            var imcData = <?php echo json_encode($imc_data); ?>;

            var labels = imcData.map(function(item) {
                return item.fecha;
            });
            var data = imcData.map(function(item) {
                return item.imc;
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'IMC',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        });
    });
</script>