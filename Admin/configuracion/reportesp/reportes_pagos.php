<?php
session_start();
require_once('../conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

// Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../includespro/header.php';
?>

<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Carga de jQuery antes que otros scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables y otros scripts después de jQuery -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main>
    <div class="container-fluid px-4">
        <h2 class="mt-4">Generación de Reportes de Pagos</h2>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Parámetros del Reporte
            </div>
            <div class="card-body">
                <form id="reporte-form" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Límite</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                            <select id="tipo_reporte" name="tipo_reporte[]" class="form-select" multiple="multiple" required>
                                <option value="categoria">Categoría</option>
                                <option value="deportista">Deportista</option>
                                <option value="representante">Representante</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="opciones_especificas" style="display:none;">
                            <label for="opcion_especifica" class="form-label">Seleccione</label>
                            <select id="opcion_especifica" name="opcion_especifica[]" class="form-select" multiple="multiple">
                                <option value="">Cargando opciones...</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="resumen-reporte" class="card mb-4" style="display:none;">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Resumen del Reporte
            </div>
            <div class="card-body">
                <!-- El resumen se llenará dinámicamente con JavaScript -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table"></i> Resultados del Reporte
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla-reporte" class="table table-striped table-hover">
                                <!-- La tabla se llenará dinámicamente -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i> Gráfico del Reporte
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#tipo_reporte').select({
            placeholder: "Seleccione tipo(s) de reporte",
            allowClear: true
        });

        $('#opcion_especifica').select({
            placeholder: "Seleccione opción(es) específica(s)",
            allowClear: true
        });

        $('#tipo_reporte').change(function() {
            const tiposReporte = $(this).val();
            if (tiposReporte && tiposReporte.length > 0) {
                $('#opciones_especificas').show();
                cargarOpciones(tiposReporte);
            } else {
                $('#opciones_especificas').hide();
            }
        });

        function cargarOpciones(tiposReporte) {
            $.ajax({
                url: 'obtener_opciones.php',
                type: 'POST',
                data: {
                    tipos: tiposReporte
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        console.error("Error al cargar opciones:", response.error);
                        Swal.fire('Error', 'Hubo un problema al cargar las opciones: ' + response.error, 'error');
                        return;
                    }

                    var select = $('#opcion_especifica');
                    select.empty();

                    $.each(response, function(tipo, opciones) {
                        var optgroup = $('<optgroup>').attr('label', tipo.charAt(0).toUpperCase() + tipo.slice(1));
                        $.each(opciones, function(index, item) {
                            optgroup.append($('<option>').val(tipo + '_' + item.id).text(item.nombre));
                        });
                        select.append(optgroup);
                    });

                    select.trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar opciones:", status, error);
                    Swal.fire('Error', 'Hubo un problema al cargar las opciones', 'error');
                }
            });
        }







        
        $('#reporte-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'generar_reporte_pagos.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        console.log(response.message);
                        console.log('Total Pagos:', response.totalPagos);

                        // Actualizar el resumen
                        actualizarResumen(response);

                        // Inicializar la tabla
                        inicializarTabla(response.resultados);

                        // Generar el gráfico
                        generarGrafico(response.estadisticas);

                        // Mostrar mensaje de éxito
                        Swal.fire('Éxito', 'Reporte generado correctamente', 'success');
                    } else {
                        console.error('Error:', response.error);
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error en la solicitud:', textStatus, errorThrown);
                    Swal.fire('Error', 'Hubo un problema al procesar la solicitud', 'error');
                }
            });
        });

        function actualizarResumen(data) {
            $('#resumen-reporte').show();
            $('.card-body', '#resumen-reporte').html(`
        <p>Total Pagos: ${data.totalPagos}</p>
        <p>Monto Total: $${data.montoTotal.toFixed(2)}</p>
    `);
        }

        function inicializarTabla(datos) {
            if ($.fn.DataTable.isDataTable('#tabla-reporte')) {
                $('#tabla-reporte').DataTable().destroy();
            }

            $('#tabla-reporte').html('<thead><tr><th>ID</th><th>Monto</th><th>Fecha</th><th>Deportista</th><th>Categoría</th><th>Representante</th></tr></thead><tbody></tbody>');

            var tabla = $('#tabla-reporte').DataTable({
                data: datos,
                columns: [{
                        data: 'ID_PAGO'
                    },
                    {
                        data: 'MONTO',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    {
                        data: 'FECHA_PAGO'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.NOMBRE_DEPO + ' ' + row.APELLIDO_DEPO;
                        }
                    },
                    {
                        data: 'CATEGORIA'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.NOMBRE_REPRE + ' ' + row.APELLIDO_REPRE;
                        }
                    }
                ],
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });
        }

        function generarGrafico(datos) {
            const ctx = document.getElementById('myChart').getContext('2d');
            if (window.myChart instanceof Chart) {
                window.myChart.destroy();
            }
            window.myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pagado', 'No Pagado'],
                    datasets: [{
                        data: [datos.pagados, datos.noPagados],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Estado de Pagos'
                    }
                }
            });
        }
    })
</script>

<?php include '../../Includespro/footer.php'; ?>