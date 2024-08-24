<?php
session_start();
require_once('../conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

// Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../includespro/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generación de Reportes de Pagos</title>
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="nav-fixed">
    <main>
        <div class="container-fluid px-4">
            <h2 class="mt-4">Generación de Reportes de Pagos</h2>

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

                                    <!-- Tipo de Reporte -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                                        <select id="tipo_reporte" name="tipo_reporte" class="form-select" required>
                                            <option value="">Seleccione un tipo</option>
                                            <option value="categoria">Categoría</option>
                                            <option value="deportista">Deportista</option>
                                            <option value="representante">Representante</option>
                                        </select>
                                    </div>

                                    <!-- Opciones específicas (se mostrará según la selección) -->
                                    <div class="col-md-6 col-lg-3" id="opciones_especificas" style="display:none;">
                                        <label for="opcion_especifica" class="form-label">Seleccione</label>
                                        <select id="opcion_especifica" name="opcion_especifica" class="form-select">
                                            <option value="">Cargando opciones...</option>
                                        </select>
                                    </div>

                                    <!-- Tipo de Informe -->
                                    <div class="col-md-6 col-lg-3">
                                        <label for="tipo_informe" class="form-label">Tipo de Informe</label>
                                        <select id="tipo_informe" name="tipo_informe" class="form-select" required>
                                            <option value="">Seleccione un tipo</option>
                                            <option value="individual_dia">Reporte individual por categoría del deportista al día</option>
                                            <option value="grupal_dia">Reporte grupal de categoría al día</option>
                                            <option value="individual_nodia">Reporte de categoría individual no al día</option>
                                            <option value="grupal_nodia">Reporte por categoría grupal no al día</option>
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

    <script>
        $(document).ready(function() {
            // Inicializar la fecha fin con la fecha actual
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_fin').val(today);

            // Manejar el cambio en el tipo de reporte
            $('#tipo_reporte').change(function() {
                const tipoReporte = $(this).val();
                if (tipoReporte) {
                    $('#opciones_especificas').show();
                    cargarOpciones(tipoReporte);
                } else {
                    $('#opciones_especificas').hide();
                }
            });

            function cargarOpciones(tipoReporte) {
                $.ajax({
                    url: 'obtener_opciones.php',
                    type: 'POST',
                    data: { tipo: tipoReporte },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            console.error("Error al cargar opciones:", response.error);
                            Swal.fire('Error', 'Hubo un problema al cargar las opciones: ' + response.error, 'error');
                            return;
                        }

                        var select = $('#opcion_especifica');
                        select.empty().append('<option value="">Seleccione una opción</option>');

                        $.each(response, function(index, item) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar opciones:", status, error);
                        Swal.fire('Error', 'Hubo un problema al cargar las opciones', 'error');
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