<?php
session_start();
require_once('../conexion.php');
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

    // Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../IncludesPro/header.php';

?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/datatables.net@1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.jsdelivr.net/npm/datatables.net@1.10.24/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main>
    <div class="container-fluid px-4">
        <h2 class="mt-4">Generación de Reportes de Pagos</h2>

        <!-- Tarjetas de Resumen Actualizadas -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Monto Total</h5>
                    <h2 class="display-4" id="monto-total">$</h2>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas al Día</h5>
                        <h2 class="display-4" id="deportistas-al-dia">0</h2>
                        <button class="btn btn-light mt-2" id="ver-al-dia">Ver Listado</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas Atrasados</h5>
                        <h2 class="display-4" id="deportistas-atrasados">0</h2>
                        <button class="btn btn-light mt-2" id="ver-atrasados">Ver Listado</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-secondary text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Categoría Más Atrasada</h5>
                        <h2 class="display-4" id="categoria-atrasada">-</h2>
                    </div>
                </div>
            </div>

            <!-- Modal para mostrar listados -->
            <div class="modal fade" id="listadoModal" tabindex="-1" aria-labelledby="listadoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="listadoModalLabel">Listado de Deportistas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped" id="listadoTable">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El contenido se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Formulario de Generación de Reportes -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i> Parámetros del Reporte
                </div>
                <div class="card-body">
                    <form id="reportForm" method="POST">
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
                                <label for="tipo_informe" class="form-label">Tipo de Reporte</label>
                                <select id="tipo_informe" name="tipo_informe" class="form-select" required>
                                    <option value="">Seleccione tipo de reporte</option>
                                    <option value="categoria">Categoría</option>
                                    <option value="deportista">Deportista</option>
                                    <option value="representante">Representante</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="id_especifico" class="form-label">Seleccione</label>
                                <select id="id_especifico" name="id_especifico" class="form-select" disabled>
                                    <option value="">Seleccione una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>




            
            <!-- Tabla de Reportes -->
            <div class="card mb-4" id="reporteContainer" style="display:none;">
                <div class="card-header">
                    <i class="fas fa-table"></i> Tabla de Reportes
                    <button type="submit" class="btn btn-primary float-end">Generar PDF</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reporteTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <!-- Las columnas se generarán dinámicamente -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se insertarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Gráfico del Reporte -->
            <div class="col-lg-12">
                <div class="card mb-4" id="chartContainer" style="display:none;">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Gráfico de Pagos por Mes
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
</main>




<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fechaFin = document.getElementById('fecha_fin');
        const hoy = new Date().toISOString().split('T')[0];
        fechaFin.value = hoy;
    });

    $('#tipo_informe').on('change', function() {
        var tipo = $(this).val();
        console.log('Tipo de informe seleccionado:', tipo);

        if (tipo) {
            $.ajax({
                url: 'get_options.php',
                type: 'POST',
                data: {
                    tipo: tipo
                },
                success: function(response) {
                    console.log('Respuesta de get_options.php:', response);
                    $('#id_especifico').html(response);
                    $('#id_especifico').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', status, error);
                }
            });
        } else {
            $('#id_especifico').html('<option value="">Seleccione una opción</option>');
            $('#id_especifico').prop('disabled', true);
        }
    });





    $(document).ready(function() {
        $('#reportForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'generar_tabla.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);

                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        return;
                    }
                    if (response.message) {
                        Swal.fire('Información', response.message, 'info');
                        return;
                    }

                    var tipo = response.tipo_informe;
                    var columns;

                    if (tipo == 'categoria') {
                        columns = [{
                                title: "Nombre de la categoría",
                                data: "NOMBRE"
                            },
                            {
                                title: "Deportista",
                                data: "NOMBRE_COMPLETO"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    } else if (tipo == 'deportista') {
                        columns = [{
                                title: "Deportista",
                                data: "NOMBRE_COMPLETO"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    } else if (tipo == 'representante') {
                        columns = [{
                                title: "Nombre del representante",
                                data: "NOMBRE_COMPLETO_REPRE"
                            },
                            {
                                title: "Deportista",
                                data: "NOMBRE_COMPLETO_DEPO"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    }



                    $('#reporteContainer').show();
                    // Destruir la tabla si ya existe
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable) {
                        if (typeof $.fn.DataTable === 'undefined') {
                            console.log('DataTables no está cargado. Intentando cargar dinámicamente...');
                            $.getScript('https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js', function() {
                                console.log('DataTables cargado dinámicamente.');
                                // Aquí puedes inicializar tu DataTable o cualquier otra lógica que dependa de DataTables
                            });
                        } else {
                            console.log('DataTables ya está cargado.');
                        }

                        // Inicializar la tabla DataTables
                        ('#reporteTable').DataTable({
                            data: reponse.data,
                            columns: columns,
                            responsive: true,
                            dom: 'Bfrtip',
                            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                            }
                        });
                    } else {
                        // Si DataTables no está disponible, crea una tabla HTML simple
                        var tableHtml = '<table class="table"><thead><tr>';
                        columns.forEach(function(column) {
                            tableHtml += '<th>' + column.title + '</th>';
                        });
                        tableHtml += '</tr></thead><tbody>';
                        response.data.forEach(function(row) {
                            tableHtml += '<tr>';
                            columns.forEach(function(column) {
                                tableHtml += '<td>' + row[column.data] + '</td>';
                            });
                            tableHtml += '</tr>';
                        });
                        tableHtml += '</tbody></table>';
                        $('#reporteTable').html(tableHtml);
                    }
                    let chartData = {};
                    response.data.forEach(function(row) {
                        if (row.MES_ANIO && row.MONTO) {
                            if (chartData[row.MES_ANIO]) {
                                chartData[row.MES_ANIO] += parseFloat(row.MONTO);
                            } else {
                                chartData[row.MES_ANIO] = parseFloat(row.MONTO);
                            }
                        }
                    });

                    let labels = Object.keys(chartData).sort();
                    let data = labels.map(label => chartData[label]);

                    // Crear el gráfico
                    let ctx = document.getElementById('myChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Monto total por mes',
                                data: data,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Monto ($)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Mes/Año'
                                    }
                                }
                            }
                        }
                    });

                    // Mostrar el contenedor del gráfico
                    $('#chartContainer').show();












                    // Agregar evento al botón de generar PDF
$('.btn-primary.float-end').on('click', function(e) {
    console.log('Botón de generar PDF clickeado'); // Verificar si el botón fue clickeado
    e.preventDefault();

    var formData = new FormData($('#reportForm')[0]);
    console.log('Datos del formulario:', formData); // Mostrar los datos del formulario para verificar que se están enviando correctamente

    $.ajax({
        url: 'generar_pdf.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhrFields: {
            responseType: 'blob' // to avoid binary data being mangled on charset conversion
        },
        success: function(blob, status, xhr) {
            console.log('PDF generado con éxito'); // Confirmar que se generó el PDF correctamente
            console.log('Estado de la respuesta:', status); // Mostrar el estado de la respuesta
            console.log('XHR:', xhr); // Verificar el objeto XHR para más detalles

            // Check for a filename
            var filename = "";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            console.log('Content-Disposition:', disposition); // Verificar el header Content-Disposition

            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                    console.log('Nombre del archivo:', filename); // Mostrar el nombre del archivo
                }
            }

            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                console.log('Usando msSaveBlob para IE'); // Indicar que se está usando la solución para IE
                window.navigator.msSaveBlob(blob, filename);
            } else {
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);
                console.log('URL de descarga:', downloadUrl); // Mostrar la URL de descarga

                if (filename) {
                    // Use HTML5 a[download] attribute to specify filename
                    var a = document.createElement("a");
                    // Safari doesn't support this yet
                    if (typeof a.download === 'undefined') {
                        console.log('Safari detectado, redirigiendo a la URL de descarga');
                        window.location.href = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        console.log('Iniciando descarga del archivo'); // Confirmar que la descarga se está iniciando
                        a.click();
                    }
                } else {
                    console.log('Descarga sin nombre de archivo, redirigiendo directamente');
                    window.location.href = downloadUrl;
                }

                setTimeout(function() {
                    console.log('Revocando URL de descarga'); // Confirmar que se está revocando la URL de descarga
                    URL.revokeObjectURL(downloadUrl);
                }, 100); // Cleanup
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al generar el PDF:', status, error); // Mostrar detalles del error
            console.log('XHR:', xhr); // Mostrar el objeto XHR para más detalles
            alert('Hubo un problema al generar el PDF. Por favor, intenta de nuevo.');
        }
    });
});






                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', status, error);
                    Swal.fire('Error', 'Hubo un problema al generar el reporte. Por favor, intenta de nuevo.', 'error');
                }
            });
        });

    });
</script>


<?php include '../../Includespro/footer.php'; ?>
</body>

</html>