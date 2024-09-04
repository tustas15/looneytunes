<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

require_once('../admin/configuracion/conexion.php');

// ID del usuario actual
$user_id = $_SESSION['user_id'];

// Consulta para obtener la foto del usuario
$sql = "
    SELECT f.FOTO 
    FROM tab_fotos_usuario f
    JOIN tab_usu_tipo ut ON ut.ID_TIPO = f.ID_TIPO
    WHERE ut.ID_USUARIO = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$foto = $stmt->fetchColumn();

// Codificar la foto en base64
$foto_src = $foto ? 'data:image/jpeg;base64,' . base64_encode($foto) : '/looneytunes/Assets/img/illustrations/profiles/profile-1.png';
date_default_timezone_set('America/Guayaquil');

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['user_id'];

try {
    // Obtener el ID_REPRESENTANTE correspondiente al ID_USUARIO
    $stmt = $conn->prepare("SELECT ID_REPRESENTANTE FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $id_representante = $stmt->fetchColumn();

    // Verificar si el ID_REPRESENTANTE fue encontrado
    if (!$id_representante) {
        echo "No se encontró el representante para este usuario.";
        exit();
    }

    // Obtener los pagos paginados para DataTables
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

    // Preparar la consulta base
    $sql = "
        SELECT p.ID_PAGO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, p.FECHA_PAGO, p.MONTO, p.MOTIVO, p.METODO_PAGO
        FROM tab_pagos p
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
    ";

    // Añadir búsqueda si se ha ingresado un valor
    if (!empty($searchValue)) {
        $sql .= " AND (d.NOMBRE_DEPO LIKE :search OR d.APELLIDO_DEPO LIKE :search OR p.MOTIVO LIKE :search)";
        $searchValue = "%$searchValue%";
    }

    // Añadir orden y límites
    $sql .= " ORDER BY p.FECHA_PAGO DESC LIMIT :start, :length";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    if (!empty($searchValue)) {
        $stmt->bindParam(':search', $searchValue, PDO::PARAM_STR);
    }
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar el total de registros
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tab_pagos WHERE ID_REPRESENTANTE = :id_representante");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $totalData = $stmt->fetchColumn();

    // Preparar la respuesta en formato JSON
    $response = [
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalData),  // Cambiar esto si hay búsqueda
        "data" => $pagos
    ];

    // Obtener los pagos agrupados por mes
    $stmt = $conn->prepare("
        SELECT DATE_FORMAT(p.FECHA_PAGO, '%Y-%m') AS mes, SUM(p.MONTO) AS total_mes
        FROM tab_pagos p
        WHERE p.ID_REPRESENTANTE = :id_representante
        GROUP BY mes
        ORDER BY mes
    ");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $pagos_por_mes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar datos para la gráfica
    $monthlyData = [
        'labels' => [],
        'data' => []
    ];

    foreach ($pagos_por_mes as $pago_mes) {
        $monthlyData['labels'][] = $pago_mes['mes'];
        $monthlyData['data'][] = (float)$pago_mes['total_mes'];
    }

    // Convertir los datos a formato JSON
    $monthlyData = json_encode($monthlyData);

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit();
}

// Incluir el encabezado (header)
include './Includes/header.php';
?>
<main>
    <header class="page-header bg-white pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-dark">Tabla de Pagos</h1>
                <p class="text-muted mb-0">Aquí puedes ver la información detallada de los pagos realizados.</p>
            </div>
        </div>
    </header>
    
    <!-- Contenido principal de la página -->
    <div class="container-xl px-4 mt-n10">
        <!-- Ejemplo de tabla para mostrar pagos -->
        <div class="card mb-4">
            <div class="card-header">Lista de Pagos</div>
            <div class="card-body">
                <!-- Tabla con DataTables -->
                <div class="table-responsive">
                <table id="pagosTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Deportista</th>
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pago['FECHA_PAGO'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars('$' . number_format($pago['MONTO'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pago['METODO_PAGO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pago['MOTIVO'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="pdf.php" class="btn btn-primary">Generar PDF</a>
                </div>
            </div>
        </div>

        <!-- Contenedor para el gráfico -->
        <div class="card mb-4">
            <div class="card-header">Gráfico de Pagos por Mes</div>
            <div class="card-body">
                <canvas id="pagosChart"></canvas>
            </div>
        </div>
    </div>
</main>

<!-- Script para el gráfico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos del gráfico (esto debe ser generado en el servidor)
    var monthlyData = <?php echo $monthlyData; ?>;

    // Configuración del gráfico
    var ctx = document.getElementById('pagosChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Total de Pagos',
                data: monthlyData.data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<!-- Script para cargar DataTables con AJAX -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#pagosTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location.href, // URL actual del archivo PHP
            "type": "GET"
        },
        "columns": [
            { "data": "NOMBRE_DEPO" },
            { "data": "FECHA_PAGO" },
            { "data": "MONTO" },
            { "data": "METODO_PAGO" },
            { "data": "MOTIVO" }
        ]
    });
});
</script>

<!-- Incluir el pie de página (footer) -->
<?php include './Includes/footer.php'; ?>
