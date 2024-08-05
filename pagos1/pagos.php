<?php
// Conexión a la base de datos
session_start();
require_once('../Admin/configuracion/conexion.php');
include '../../Includespro/header.php';
?>
<head>
<<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestión de Pagos</title>
    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Pagos</h1>
        <a href="../configuracion/respaldo/downloadFile.php" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generar Respaldo
        </a>
    </div>

        <form id="formulario-pago" action="procesar_pagos.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="apellido_representante" class="form-label">Representante</label>
                    <select id="apellido_representante" class="form-select" required>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cedula_representante" class="form-label">Cédula del Representante</label>
                    <input type="text" class="form-control" id="cedula_representante" readonly>
                </div>
                <div class="mb-3">
                    <label for="deportista" class="form-label">Deportista</label>
                    <select id="deportista" class="form-select" required>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cedula_deportista" class="form-label">Cédula del Deportista</label>
                    <input type="text" class="form-control" id="cedula_deportista" readonly>
                </div>
                <div class="mb-3">
                    <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                    <select id="tipo_pago" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <!-- Campos adicionales para efectivo -->
                <div id="campos-efectivo" class="d-none">
                    <div class="mb-3">
                        <label for="fecha_pago_efectivo" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago_efectivo" value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="motivo_efectivo" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo_efectivo">
                    </div>
                    <div class="mb-3">
                        <label for="monto_efectivo" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto_efectivo">
                    </div>

                    <div class="mb-3">
                        <label for="mes_efectivo" class="form-label">Mes de Pago</label>
                        <select id="mes_efectivo" class="form-select">
                            <?php
                            $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                            $mes_actual = date('n') - 1;
                            foreach ($meses as $index => $mes) {
                                $selected = ($index == $mes_actual) ? "selected" : "";
                                echo "<option value='$mes' $selected>$mes</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="anio_efectivo" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio_efectivo" value="<?= date('Y'); ?>">
                    </div>
                </div>

                <!-- Campos adicionales para transferencia -->
                <div id="campos-transferencia" class="d-none">
                    <div class="mb-3">
                        <label for="banco_destino" class="form-label">Banco de Destino</label>
                        <select id="banco_destino" class="form-select" required>
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="entidad_financiera" class="form-label">Entidad Financiera de Origen</label>
                        <input type="text" class="form-control" id="entidad_financiera" required>
                    </div>
                    <div class="mb-3">
                        <label for="comprobante" class="form-label">Comprobante</label>
                        <input type="file" class="form-control" id="comprobante" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_pago_transferencia" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago_transferencia" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_transferencia" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo_transferencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="monto_transferencia" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto_transferencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="mes_transferencia" class="form-label">Mes de Pago</label>
                        <select id="mes_transferencia" class="form-select" required>
                            <?php
                            foreach ($meses as $index => $mes) {
                                $selected = ($index == $mes_actual) ? "selected" : "";
                                echo "<option value='$mes' $selected>$mes</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="anio_transferencia" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio_transferencia" value="<?= date('Y'); ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Pago</button>
                
            </form>
            <div id="mensaje-confirmacion" class="alert alert-success mt-3" style="display: none;"></div>

<div id="procesar_pagos.php" style="display: none;">
    <h2>Historial de Pagos</h2>
        <table id="tabla-pagos" class="table table-striped">
            <thead>
                <tr>
                    <th>Representante</th>
                    <th>Cédula</th>
                    <th>Deportista</th>
                    <th>Tipo de Pago</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos de pagos se llenarán dinámicamente -->
            </tbody>
        </table>
    </div>

   <!-- jQuery y Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/js/sb-admin-2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
    var table = $('#tabla-pagos').DataTable({
        ajax: {
            url: 'procesar_pagos.php',
            dataSrc: ''
        },
        columns: [
            { data: 'representante' },
            { data: 'deportista' },
            { data: 'tipo_pago' },
            { data: 'monto' },
            { data: 'fecha' },
            { data: 'motivo' },
            { 
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-primary editar-pago" data-id="' + row.id + '">Editar</button> ' +
                           '<button class="btn btn-sm btn-danger eliminar-pago" data-id="' + row.id + '">Eliminar</button>';
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    // Llama a esta función después de inicializar la tabla
    actualizarTablaPagos();
});
         $('#formulario-pago').submit(function(event) {
    event.preventDefault();
    var formData = new FormData(this);

    // Agregar los campos adicionales según el tipo de pago
    if ($('#tipo_pago').val() === 'efectivo') {
        formData.append('fecha_pago', $('#fecha_pago_efectivo').val());
        formData.append('motivo', $('#motivo_efectivo').val());
        formData.append('monto', $('#monto_efectivo').val());
        formData.append('mes', $('#mes_efectivo').val());
        formData.append('anio', $('#anio_efectivo').val());
    } else if ($('#tipo_pago').val() === 'transferencia') {
        formData.append('banco_destino', $('#banco_destino').val());
        formData.append('entidad_financiera', $('#entidad_financiera').val());
        formData.append('fecha_pago', $('#fecha_pago_transferencia').val());
        formData.append('motivo', $('#motivo_transferencia').val());
        formData.append('monto', $('#monto_transferencia').val());
        formData.append('mes', $('#mes_transferencia').val());
        formData.append('anio', $('#anio_transferencia').val());
    }

    $.ajax({
        url: 'procesar_pagos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                alert('Pago registrado correctamente');
                $('#formulario-pago')[0].reset();
                actualizarTablaPagos();
            } else {
                alert('Error al registrar el pago: ' + result.message);
            }
        },
        error: function() {
            alert('Error al procesar la solicitud');
        }
    });
});

function actualizarTablaPagos() {
    $.ajax({
        url: 'obtener_pagos.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var tabla = $('#tabla-pagos').DataTable();
            tabla.clear();
            data.forEach(function(pago) {
                tabla.row.add([
                    pago.representante,
                    pago.deportista,
                    pago.tipo_pago,
                    pago.monto,
                    pago.fecha,
                    pago.motivo,
                    '<button class="btn btn-sm btn-primary editar-pago" data-id="' + pago.id + '">Editar</button> ' +
                    '<button class="btn btn-sm btn-danger eliminar-pago" data-id="' + pago.id + '">Eliminar</button>'
                ]);
            });
            tabla.draw();
            $('#historial-pagos').show();
        },
        error: function() {
            alert('Error al obtener los pagos');
        }
    });
}
        $('#apellido_representante').change(function() {
                var representanteId = $(this).val();
                if (representanteId) {
                    $.ajax({
                        url: 'get_deportistas.php',
                        type: 'POST',
                        data: { id_representante: representanteId },
                        success: function(response) {
                            var deportistas = JSON.parse(response);
                            var deportistaSelect = $('#deportista');
                            deportistaSelect.empty();
                            deportistas.forEach(function(deportista) {
                                deportistaSelect.append(new Option(deportista.nombre_completo, deportista.id));
                            });
                        }
                    });
                }
            });

            $('#tipo_pago').change(function() {
                var tipoPago = $(this).val();
                if (tipoPago === 'efectivo') {
                    $('#campos-efectivo').removeClass('d-none');
                    $('#campos-transferencia').addClass('d-none');
                } else if (tipoPago === 'transferencia') {
                    $('#campos-transferencia').removeClass('d-none');
                    $('#campos-efectivo').addClass('d-none');
                } else {
                    $('#campos-efectivo, #campos-transferencia').addClass('d-none');
                }
            });
       
        // Cargar bancos
        $.ajax({
            url: 'get_bancos.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    data.forEach(function(banco) {
                        $('#banco_destino').append(`<option value="${banco.id}">${banco.nombre}</option>`);
                    });
                } else {
                    console.log("No se encontraron bancos activos");
                }
            },
        });

        $(document).ready(function() {
            // Cargar historial de pagos al iniciar la página
            cargarHistorialPagos();

            // Función para cargar el historial de pagos
            function cargarHistorialPagos() {
                $.ajax({
                    type: 'GET',
                    url: 'historial_pagos.php',
                    success: function(response) {
                        $('#historial_pagos').html(response);
                        initDataTable();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar el historial de pagos:", status, error);
                        $('#historial_pagos').html("Error al cargar el historial de pagos.");
                    }
                });
            }
        });
        $(document).ready(function() {
            // Cargar apellidos de representantes
            $.ajax({
                url: 'get_representantes.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(representante) {
                            $('#apellido_representante').append(`<option value="${representante.ID_REPRESENTANTE}" data-deportista="${representante.ID_DEPORTISTA}">${representante.APELLIDO_REPRE} ${representante.NOMBRE_REPRE}</option>`);
                        });
                    } else {
                        console.log("No se encontraron representantes");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar representantes:", status, error);
                }
            });

            // Cuando se selecciona un representante
            $('#apellido_representante').on('change', function() {
                var id_representante = $(this).val();
                var id_deportista = $(this).find(':selected').data('deportista');
                if (id_representante) {
                    // Cargar cédula del representante
                    $.ajax({
                        url: 'get_nombre_representante.php',
                        method: 'GET',
                        data: {
                            id_representante: id_representante
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#cedula_representante').val(data.CEDULA_REPRE);
                        }
                    });

                    // Cargar deportistas asociados y seleccionar el deportista automáticamente
                    $.ajax({
                        url: 'get_deportistas.php',
                        method: 'GET',
                        data: {
                            id_representante: id_representante
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#deportista').empty().append('<option value="">Seleccionar</option>');
                            data.forEach(function(deportista) {
                                $('#deportista').append(`<option value="${deportista.ID_DEPORTISTA}">${deportista.APELLIDO_DEPO} ${deportista.NOMBRE_DEPO}</option>`);
                            });
                            // Seleccionar automáticamente el deportista asociado
                            $('#deportista').val(id_deportista).change();
                        }
                    });
                } else {
                    $('#cedula_representante').val('');
                    $('#deportista').empty().append('<option value="">Seleccionar</option>');
                    $('#cedula_deportista').val('');
                }
            });

            // Cuando se selecciona un deportista
            $('#deportista').on('change', function() {
                var id_deportista = $(this).val();
                if (id_deportista) {
                    $.ajax({
                        url: 'get_cedula_deportista.php',
                        method: 'GET',
                        data: {
                            id_deportista: id_deportista
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#cedula_deportista').val(data.CEDULA_DEPO);
                        }
                    });
                } else {
                    $('#cedula_deportista').val('');
                }
            });
            $(document).ready(function() {
                // Manejar el envío del formulario
$('#formulario-pago').submit(function(event) {
    event.preventDefault();
    var formData = new FormData(this);

    // Agregar los campos adicionales según el tipo de pago
    if ($('#tipo_pago').val() === 'efectivo') {
        formData.append('fecha_pago', $('#fecha_pago_efectivo').val());
        formData.append('motivo', $('#motivo_efectivo').val());
        formData.append('monto', $('#monto_efectivo').val());
        formData.append('mes', $('#mes_efectivo').val());
        formData.append('anio', $('#anio_efectivo').val());
    } else if ($('#tipo_pago').val() === 'transferencia') {
        formData.append('banco_destino', $('#banco_destino').val());
        formData.append('entidad_financiera', $('#entidad_financiera').val());
        formData.append('fecha_pago', $('#fecha_pago_transferencia').val());
        formData.append('motivo', $('#motivo_transferencia').val());
        formData.append('monto', $('#monto_transferencia').val());
        formData.append('mes', $('#mes_transferencia').val());
        formData.append('anio', $('#anio_transferencia').val());
    }

    $.ajax({
        url: 'procesar_pagos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert('Pago registrado correctamente');
                // Opcional: limpiar el formulario o redirigir a otra página
                $('#formulario-pago')[0].reset();
            } else {
                alert('Error al registrar el pago: ' + response.message);
            }
        },
        error: function() {
            alert('Error al procesar la solicitud');
        }
    });
});
            });
        });
    </script>
</body>
</html>

<?php include '../../Includespro/footer.php'; ?>