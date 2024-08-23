<?php
session_start();
require_once('../conexion.php');

$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../includespro/header.php';
?>

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
                    <form id="reporte-form" class="needs-validation" novalidate>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Fecha Inicio -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                    </div>

                                    <!-- Fecha Fin -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                    </div>

                                    <!-- Categoría -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="categoria" class="form-label">Categoría</label>
                                        <select id="categoria" name="categoria" class="form-select">
                                            <option value="">Todas las categorías</option>
                                        </select>
                                    </div>

                                    <!-- Deportista -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="deportista" class="form-label">Deportista</label>
                                        <select id="deportista" name="deportista" class="form-select">
                                            <option value="">Todos los deportistas</option>
                                        </select>
                                    </div>

                                    <!-- Representante -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="representante" class="form-label">Representante</label>
                                        <select id="representante" name="representante" class="form-select">
                                            <option value="">Todos los representantes</option>
                                        </select>
                                    </div>

                                    <!-- Tipo de Reporte -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                                        <select id="tipo_reporte" name="tipo_reporte" class="form-select" required>
                                            <option value="">Seleccione un tipo</option>
                                            <option value="detallado">Detallado</option>
                                            <option value="resumen">Resumen</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Limpiar</button>
                                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Área para mostrar los resultados del reporte -->
                    <div id="resultados-reporte" class="mt-4">
                        <!-- Resultados del reporte se mostrarán aquí -->
                    </div>

                    <!-- Área para mostrar el gráfico -->
                    <div id="grafico-reporte" class="mt-4">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar la fecha fin con la fecha actual
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_fin').val(today);

            // Cargar opciones para categorías, deportistas y representantes
            ['categoria', 'deportista', 'representante'].forEach(function(filtro) {
                cargarOpciones(filtro);
            });

            // Función para cargar opciones
            function cargarOpciones(filtro) {
                $.ajax({
                    url: 'obtener_opciones.php',
                    type: 'POST',
                    data: {
                        filtro: filtro
                    },
                    success: function(response) {
                        $('#' + filtro).html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error al cargar opciones para " + filtro + ":", status, error);
                        Swal.fire('Error', 'Hubo un problema al cargar las opciones de ' + filtro, 'error');
                    }
                });
            }

            // Manejar el envío del formulario
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
                        inicializarTabla();
                        generarGrafico(response);
                    },
                    error: function() {
                        Swal.fire('Error', 'Hubo un problema al generar el reporte', 'error');
                    }
                });
            });

            // Función para inicializar la tabla de resultados
            function inicializarTabla() {
                if ($.fn.DataTable.isDataTable('#tabla-reporte')) {
                    $('#tabla-reporte').DataTable().destroy();
                }
                $('#tabla-reporte').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                    }
                });
            }

            // Función para generar el gráfico
            function generarGrafico(data) {
                const ctx = document.getElementById('myChart').getContext('2d');
                let chartData;
                try {
                    chartData = JSON.parse(data);
                } catch (e) {
                    console.error("Error al parsear los datos del gráfico:", e);
                    return;
                }

                if (window.myChart instanceof Chart) {
                    window.myChart.destroy();
                }

                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Pagos',
                            data: chartData.data,
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

</html>

<?php
include '../../Includespro/footer.php';
?>