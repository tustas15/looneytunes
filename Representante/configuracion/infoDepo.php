<?php
session_start();
include '../Admin/configuracion/conexion.php';

// Inicializar variables
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$resultados = null;

// Procesar la búsqueda cuando se envíe el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
}


// Validar la cédula para prevenir inyecciones SQL
if (preg_match('/^[0-9]{10}$/', $cedula)) {
    // Consulta para obtener los datos del deportista y sus detalles
    $query = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, det.NUMERO_CAMISA, det.ALTURA, det.PESO, det.FECHA_INGRESO
                  FROM tab_deportistas d
                  LEFT JOIN tab_detalles det ON d.ID_DEPORTISTA = det.ID_USUARIO
                  WHERE d.CEDULA_DEPO = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Ejecutar consulta pasando los parámetros directamente
        $stmt->execute([$cedula]);

        // Obtener resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Error en la preparación de la consulta";
    }
} else {
    echo "Cédula inválida";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include '../representante/vistaRepre/header.php'; ?>
    <style>
        .tabla-contenedor {
            display: flex;
            gap: 20px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include '../representante/vistaRepre/sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <?php include '../representante/vistaRepre/navigation.php'; ?>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="container mt-5">
                        <h2>Página de inicio</h2>
                        <p>Bienvenido, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>!</p>

                        <h3>Buscar Deportista</h3>
                        <form id="searchForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="form-group">
                                <label for="cedula">Ingrese la cédula del deportista:</label>
                                <input type="text" class="form-control" id="cedula" name="cedula" required pattern="[0-9]{10}">
                            </div>

                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </form>
                    </div>

                    <!-- Mostrar resultados de búsqueda si existen -->
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula']) && $resultados !== null && count($resultados) > 0) : ?>
                        <div class="container mt-5">
                            <h3>Datos Encontrados</h3>
                            <div class="tabla-contenedor">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Información</th>
                                            <th>Rendimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($resultado['NOMBRE_DEPO'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($resultado['APELLIDO_DEPO'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><button class="btn btn-info ver-info" data-deportista-id="<?= $resultado['ID_DEPORTISTA'] ?>">Ver</button></td>
                                                <td><button class="btn btn-success ver-rendimiento" data-deportista-id="<?= $resultado['ID_DEPORTISTA'] ?>">Ver</button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else : ?>
                        <p>No se encontraron resultados o no se ha realizado una búsqueda.</p>
                    <?php endif; ?>
                    <!-- Contenedores para cargar la información y el rendimiento -->
                    <div id="info-container"></div>
                    <div id="rendimiento-container"></div>
                </div>
                <!-- End of Page Content -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include '../representante/vistaRepre/footer.php'; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include '../representante/vistaRepre/scripts.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Asegúrate de que todas las rutas sean correctas -->
    <script>
        $(document).ready(function() {
            $('.ver-info').click(function() {
                var deportista_id = $(this).data('deportista-id');
                $.ajax({
                    type: 'POST',
                    url: 'loadInfoDeportista.php',
                    data: {
                        deportista_id: deportista_id,
                        csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>'
                    },
                    success: function(response) {
                        $('#info-container').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar la información del deportista:", status, error);
                        $('#info-container').html("Error al cargar la información del deportista.");
                    }
                });
            });

            // Acción para ver rendimiento
            $('.ver-rendimiento').click(function() {
                var deportista_id = $(this).data('deportista-id');
                $.ajax({
                    type: 'POST',
                    url: 'loadRendimientoDeportista.php',
                    data: {
                        deportista_id: deportista_id,
                        csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>'
                    },
                    success: function(response) {
                        $('#rendimiento-container').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar el rendimiento del deportista:", status, error);
                        $('#rendimiento-container').html("Error al cargar el rendimiento del deportista.");
                    }
                });
            });
        });
    </script>
</body>

</html>