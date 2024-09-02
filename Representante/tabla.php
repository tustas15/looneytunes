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

    // Obtener los pagos asociados al representante
    $stmt = $conn->prepare("
        SELECT p.ID_PAGO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, p.FECHA_PAGO, p.MONTO, p.MOTIVO, p.METODO_PAGO
        FROM tab_pagos p
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
        ORDER BY d.NOMBRE_DEPO ASC, d.APELLIDO_DEPO ASC"
    );
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<<<<<<< HEAD
                <h1 class="text-dark">Tabla de Pagos</h1>
                <p class="text-muted mb-0">Aquí puedes ver la info detallada de los pagos realizados.</p>
=======
                <h1 class="text-white">Tabla de Pagos</h1>
                <p class="text-white-700 mb-0">Historial de Pagos de Deportistas Asociados</p>
>>>>>>> 7eb31307923f5c3f68b8f495efdd8a012dbcbb91
            </div>
        </div>
    </header>
    
    <!-- Contenido principal de la página -->
    <div class="container-xl px-4 mt-n10">
        <!-- Ejemplo de tabla para mostrar pagos -->
        <div class="card mb-4">
            <div class="card-header">Lista de Pagos</div>
            <div class="card-body">
                <!-- Campo de búsqueda -->

                <!-- Tabla con DataTables -->
                <table id="pagosTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Deportista</th>
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se generarán los datos con DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Incluir el pie de página (footer) -->
<?php include './Includes/footer.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.3/css/buttons.dataTables.min.css">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.colVis.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#pagosTable').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: 'historial_pagos.php',
            dataSrc: 'data'
        },
        columns: [
            { data: 'deportista' },
            { data: 'fecha_pago' },
            { data: 'monto' },
            { data: 'metodo_pago' },
            { data: 'motivo' },
            { data: 'acciones' }
        ],
        buttons: [
            {
                extend: 'pdfHtml5',
                text: 'Generar PDF',
                className: 'btn btn-success',
                title: 'Reporte de Pagos',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/Spanish.json'
        },
        columnDefs: [
            { targets: '_all', orderable: false }
        ]
    });

    // Función de búsqueda
    $('#searchInput').on('keyup', function() {
        $('#pagosTable').DataTable().search($(this).val()).draw();
    });
});
</script>
