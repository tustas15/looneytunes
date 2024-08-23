<?php
session_start();
require_once('../conexion.php');

$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../includespro/header.php';
?>

<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>

<body class="nav-fixed">
    <main>
        <div class="container-fluid px-4">
            <h2 class="mt-4">Generación de Reportes de Pagos</h2>

            <!-- Formulario de generación de reportes -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i> Parámetros del Reporte
                </div>
                <div class="card-body">
                    <form id="reporte-form">
                        <div class="row mb-3">
                            <!-- Fecha Inicio Desglosada en Día, Mes y Año -->
                            <div class="col-md-3">
                                <label for="dia_inicio" class="form-label">Fecha Inicio</label>
                                <div class="d-flex">
                                    <!-- Día -->
                                    <select class="form-control" id="dia_inicio" name="dia_inicio" required>
                                        <option value="">Día</option>
                                        <?php
                                        for ($i = 1; $i <= 31; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- Mes -->
                                    <select class="form-control mx-2" id="mes_inicio" name="mes_inicio" required>
                                        <option value="">Mes</option>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            $mesNombre = date("F", mktime(0, 0, 0, $i, 10));
                                            echo "<option value='$i'>$mesNombre</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- Año -->
                                    <select class="form-control" id="anio_inicio" name="anio_inicio" required>
                                        <option value="">Año</option>
                                        <?php
                                        $currentYear = date("Y");
                                        for ($i = $currentYear; $i >= $currentYear - 100; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>

                            <!-- Filtrar por -->
                            <div class="col-md-3">
                                <label for="filtro" class="form-label">Filtrar por</label>
                                <select class="form-control" id="filtro" name="filtro" required>
                                    <option value="">Seleccionar Filtro</option>
                                    <option value="categoria">Categoría</option>
                                    <option value="deportista">Deportista</option>
                                </select>
                            </div>

                            <!-- Opción -->
                            <div class="col-md-3">
                                <label for="opcion" class="form-label">Opción</label>
                                <select class="form-control" id="opcion" name="opcion">
                                    <option value="">Todas las opciones</option>
                                    <!-- Las opciones se cargarán dinámicamente con AJAX -->
                                </select>
                            </div>

                            <!-- Botón para Generar Reporte -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Generar Reporte</button>
                            </div>
                        </div>
                    </form>



                </div>
                </div>

                <!-- Área para mostrar los resultados del reporte y gráficos -->
                <div id="resultados-reporte" class="mt-4">
                    <!-- Resultados del reporte se mostrarán aquí -->
                </div>
                <div id="grafico-reporte" class="mt-4">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar la fecha fin con la fecha actual
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_fin').val(today);

            // Manejar el evento de envío del formulario para generar el reporte
            $('#reporte-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: 'procesar_reporte.php',
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Generando Reporte...',
                            text: 'Por favor espera unos momentos.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        $('#resultados-reporte').html(response);
                        $('#tabla-reporte').DataTable();
                        // Llamar a la función para generar el gráfico
                        generarGrafico(response);
                    },
                    error: function() {
                        Swal.fire('Error', 'Hubo un problema al generar el reporte', 'error');
                    }
                });
            });

            // Cargar opciones dinámicamente basado en el filtro seleccionado
            $('#filtro').on('change', function() {
                const filtro = $(this).val();

                if (filtro === 'categoria' || filtro === 'deportista') {
                    $.ajax({
                        url: 'obtener_opciones.php',
                        type: 'POST',
                        data: {
                            filtro: filtro
                        },
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

            // Función para generar el gráfico usando Chart.js
            function generarGrafico(data) {
                const ctx = document.getElementById('myChart').getContext('2d');
                const chartData = JSON.parse(data); // Asumiendo que el servidor devuelve un JSON válido para el gráfico

                // Destruir cualquier instancia previa del gráfico para evitar conflictos
                if (window.myChart) {
                    window.myChart.destroy();
                }

                window.myChart = new Chart(ctx, {
                    type: 'bar', // Tipo de gráfico, puedes cambiarlo a 'line', 'pie', etc.
                    data: {
                        labels: chartData.labels, // Etiquetas de los ejes
                        datasets: [{
                            label: 'Pagos',
                            data: chartData.data, // Datos para el gráfico
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>

<?php
include '../../Includespro/footer.php';
?>