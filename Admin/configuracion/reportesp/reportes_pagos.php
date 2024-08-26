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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.24/r-2.2.7/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.24/r-2.2.7/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/datatables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

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

        <!-- Tarjetas de Resumen -->
        <div class="row mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Pagos</h5>
                        <h2 class="display-4" id="total-pagos">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Monto Total</h5>
                        <h2 class="display-4" id="monto-total">$0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas al Día</h5>
                        <h2 class="display-4" id="deportistas-al-dia">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas Atrasados</h5>
                        <h2 class="display-4" id="deportistas-atrasados">0</h2>
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
                <form action="generar_tabla.php" method="post">
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
                            <select id="tipo_reporte" name="tipo_reporte" class="form-select" required>
                                <option value="">Seleccione tipo de reporte</option>
                                <option value="categoria">Categoría</option>
                                <option value="deportista">Deportista</option>
                                <option value="representante">Representante</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="opciones_especificas" style="display:none;">
                            <label for="opcion_especifica" class="form-label">Seleccione</label>
                            <select id="opcion_especifica" name="opcion_especifica[]" class="form-select">
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

        <!-- Resultados del Reporte -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table"></i> Resultados del Reporte
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla-reporte" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Deportista</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se insertarán aquí dinámicamente -->
                        </tbody>
                    </table>
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
</main>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'generar_tabla.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        llenarTablaResultados(response.data);
                       
                        //generarGrafico(response.data);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("AJAX Error: " + textStatus + ' : ' + errorThrown);
                    Swal.fire('Error', "Ocurrió un error al procesar la solicitud.", 'error');
                }
            });
        });

        function llenarTablaResultados(data) {
            function llenarTablaResultados(data) {
    var columns = [];
    var tipoReporte = $('#tipo_reporte').val();

    // Definir columnas basadas en el tipo de reporte
    if (tipoReporte === 'categoria') {
        columns = [
            { data: 'categoria', title: 'Categoría' },
            { data: 'deportista', title: 'Deportista' },
            { data: 'fecha', title: 'Fecha' },
            { data: 'monto', title: 'Monto', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
            { data: 'estado', title: 'Estado' }
        ];
    } else if (tipoReporte === 'deportista') {
        columns = [
            { data: 'deportista', title: 'Deportista' },
            { data: 'fecha', title: 'Fecha' },
            { data: 'monto', title: 'Monto', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
            { data: 'estado', title: 'Estado' }
        ];
    } else if (tipoReporte === 'representante') {
        columns = [
            { data: 'representante', title: 'Representante' },
            { data: 'fecha', title: 'Fecha' },
            { data: 'monto', title: 'Monto', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
            { data: 'estado', title: 'Estado' }
        ];
    }

    var tabla = $('#tabla-reporte').DataTable({
        destroy: true,
        data: data,
        columns: columns,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
}
        }

        $('#tipo_reporte').change(function() {
    var tipoReporte = $(this).val();
    if (tipoReporte) {
        $('#opciones_especificas').show();
        cargarOpciones([tipoReporte]);
    } else {
        $('#opciones_especificas').hide();
    }
});


    $('#generar_pdf').on('click', function() {
        var form = $('form');
        form.attr('action', 'generar_pdf_reporte.php');
        form.attr('method', 'post');
        form.attr('target', '_blank');
        form.submit();
        form.attr('action', '');
        form.attr('target', '');
    });


  




   
        $.ajax({
            url: 'obtener_opciones.php',
            type: 'POST',
            data: {
                tipos: tiposReporte // Envía el array de tipos
            },
            dataType: 'json',
            success: function(response) {
                console.log(response); // Verifica la respuesta del servidor
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
    })
</script>

<?php include '../../Includespro/footer.php'; ?>