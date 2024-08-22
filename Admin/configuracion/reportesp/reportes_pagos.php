<?php
// Conexión a la base de datos
session_start();
require_once('../conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
include '../../Includespro/header.php';
?>

<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="nav-fixed">
    <main>
        <div class="container-fluid px-4">
            <h2 class="mt-4">Reporte de Pagos</h2>
            <form id="reporte-form">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro" class="form-label">Filtrar por</label>
                        <select class="form-control" id="filtro" name="filtro" required>
                            <option value="">Seleccionar Filtro</option>
                            <option value="categoria">Categoría</option>
                            <option value="deportista">Deportista</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="opcion" class="form-label">Opción</label>
                        <select class="form-control" id="opcion" name="opcion">
                            <option value="">Todas las opciones</option>
                            <!-- Las opciones se cargarán dinámicamente con AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </div>
            </form>

            <div id="resultados-reporte" class="mt-4">
                <!-- Aquí se mostrarán los resultados del reporte -->
            </div>

            <div id="grafico-reporte" class="mt-4">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    $(document).ready(function() {
        // Establecer la fecha fin como la fecha actual
        var today = new Date().toISOString().split('T')[0];
        $('#fecha_fin').val(today);

        $('#reporte-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'procesar_reporte.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#resultados-reporte').html(response);
                    $('#tabla-reporte').DataTable();
                    generarGrafico();
                },
                error: function() {
                    Swal.fire('Error', 'Hubo un problema al generar el reporte', 'error');
                }
            });
        });

        $('#filtro').change(function() {
            var filtro = $(this).val();

            if (filtro === 'categoria' || filtro === 'deportista') {
                $.ajax({
                    url: 'obtener_opciones.php',
                    type: 'POST',
                    data: { filtro: filtro },
                    success: function(response) {
                        $('#opcion').html(response);
                    },
                    error: function() {
                        Swal.fire('Error', 'Hubo un problema al cargar las opciones', 'error');
                    }
                });
            } else {
                $('#opcion').html('<option value="">Todas las opciones</option>');
            }
        });

        function generarGrafico() {
            // Aquí iría el código para generar el gráfico con Chart.js
            // Esto dependerá de los datos específicos que quieras mostrar
        }
    });
    </script>

    <script>
    $(document).ready(function() {
        $('#reporte-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'procesar_reporte.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#resultados-reporte').html(response);
                    // Inicializar DataTables
                    $('#tabla-reporte').DataTable();
                    // Generar gráfico
                    generarGrafico();
                },
                error: function() {
                    Swal.fire('Error', 'Hubo un problema al generar el reporte', 'error');
                }
            });
        });

        function generarGrafico() {
            // Aquí iría el código para generar el gráfico con Chart.js
            // Esto dependerá de los datos específicos que quieras mostrar
        }
         // Asegúrate de que estas funciones estén presentes en tu código
         function setFechaYMesActual() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0!
            var yyyy = today.getFullYear();

            // Establecer la fecha actual
            var fechaActual = yyyy + '-' + mm + '-' + dd;
            document.getElementById('fecha').value = fechaActual;

            // Establecer el mes actual
            document.getElementById('mes').value = mm;

            // Establecer el año actual
            document.getElementById('anio').value = yyyy;

            // Actualizar el motivo
            actualizarMotivo();
        }

    });
    </script>
    
</body>

<?php
include '../../Includespro/footer.php';
?>