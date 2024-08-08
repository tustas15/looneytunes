<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
include '../../Includespro/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container">
        <h2>Formulario de Pago</h2>
        <form id="paymentForm" enctype="multipart/form-data">
            <input type="hidden" id="id_pago" name="id_pago">
            <div class="form-group">
                <label for="representante">Apellido del Representante:</label>
                <select id="representante" name="representante" class="form-control" required>
                    <!-- Options will be populated by JS -->
                </select>
            </div>
            <div class="form-group">
                <label for="cedula_representante">Cédula del Representante:</label>
                <input type="text" id="cedula_representante" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="deportista" class="form-label">Deportista</label>
                <select id="deportista" name="deportista" class="form-control" required>
                </select>
            </div>
            <div class="form-group">
                <label for="cedula_deportista">Cédula del Deportista:</label>
                <input type="text" id="cedula_deportista" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="metodo_pago">Método de Pago:</label>
                <select id="metodo_pago" name="metodo_pago" class="form-control">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            <div class="form-group" id="banco_section" style="display:none;">
                <label for="banco">Banco Destino:</label>
                <select id="banco" name="banco" class="form-control" readonly>
                    <!-- Options will be populated by JS -->
                </select>
            </div>
            <div class="form-group" id="entidad_section" style="display:none;">
                <label for="entidad_origen">Entidad Financiera de Origen:</label>
                <input type="text" id="entidad_origen" name="entidad_origen" class="form-control">
            </div>
            <div class="form-group" id="comprobante_section" style="display:none;">
                <label for="comprobante">Comprobante:</label>
                <input id="nombre_archivo" name="nombre_archivo" type="file" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="monto">Monto:</label>
                <input id="monto" name="monto" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control fecha-actual" id="fecha" name="fecha">
            </div>

            <div class="form-group">
                <label for="mes">Mes:</label>
                <select id="mes" class="form-control">
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="anio">Año:</label>
                <input type="number" id="anio" class="form-control">
            </div>

            <div class="form-group">
                <label for="motivo">Motivo:</label>
                <input id="motivo" name="motivo" class="form-control" readonly>
            </div>

            <script>
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

                function actualizarMotivo() {
                    var mesSeleccionado = document.getElementById('mes');
                    var mesTexto = mesSeleccionado.options[mesSeleccionado.selectedIndex].text;
                    //var anioSeleccionado = document.getElementById('anio').value;
                    document.getElementById('motivo').value = 'Pago del mes de ' + mesTexto + ' ';
                }

                // Llamar a la función cuando se carga la página
                window.onload = setFechaYMesActual;

                // Actualizar motivo cuando cambia el mes o el año
                document.getElementById('mes').addEventListener('change', actualizarMotivo);
                document.getElementById('anio').addEventListener('change', actualizarMotivo);
            </script>
            <button type="submit" class="btn btn-primary">Registrar Pago</button>
        </form>
        <div id="message"></div>

        <h2>Historial de Pagos</h2>
        <table id="historial_pagos" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Deportista</th>
                    <th>Representante</th>
                    <th>Tipo de Pago</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán aquí dinámicamente -->
            </tbody>
        </table>


    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#historial_pagos').DataTable({
                ajax: {
                    url: 'historial_pagos.php',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'deportista'
                    },
                    {
                        data: 'representante'
                    },
                    {
                        data: 'metodo_pago'
                    },
                    {
                        data: 'fecha_pago'
                    },
                    {
                        data: 'motivo'
                    },
                    {
                        data: 'monto'
                    },
                    {
                        data: 'acciones'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
                },

                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ]

            });





            // Manejar clic en el botón eliminar
            $('#historial_pagos').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'eliminar.php',
                            type: 'POST',
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Eliminado!',
                                        'El pago ha sido eliminado.',
                                        'success'
                                    );
                                    if (typeof table !== 'undefined') {
                                        table.ajax.reload();
                                    }
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Error al eliminar el pago: ' + response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Error al procesar la solicitud: ' + xhr.responseText,
                                    'error'
                                );
                            }
                        });
                    }
                });


            });

            // Manejar clic en los botones de editar

            $(document).ready(function() {
                $('.edit-btn').click(function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: 'obtener_pago.php',
                        type: 'GET',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#id_pago').val(data.ID_PAGO);
                            $('#deportista').val(data.DEPORTISTA);
                            $('#representante').val(data.REPRESENTANTE);
                            $('#tipo_pago').val(data.TIPO_PAGO);
                            $('#fecha').val(data.FECHA);
                            $('#motivo').val(data.MOTIVO);
                            $('#monto').val(data.MONTO);

                            $('#btnGuardar').text('Guardar Cambios');
                            $('#btnGuardar').data('action', 'update');
                        },
                        error: function() {
                            alert('Error al cargar los datos del pago');
                        }
                    });
                });

                $('#btnGuardar').click(function() {
                    var action = $(this).data('action');
                    var url = action === 'update' ? 'actualizar.php' : 'registrar_pago.php';

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            id_pago: $('#id_pago').val(),
                            deportista: $('#deportista').val(),
                            representante: $('#representante').val(),
                            tipo_pago: $('#tipo_pago').val(),
                            fecha: $('#fecha').val(),
                            motivo: $('#motivo').val(),
                            monto: $('#monto').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert('Operación realizada con éxito');
                                location.reload(); // Recargar la página para mostrar los cambios
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error al procesar la solicitud');
                        }
                    });
                });
            });






            // Cargar bancos
            $.ajax({
                url: 'get_bancos.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#banco').empty().append('<option value="">Seleccionar</option>');
                    if (data.length > 0) {
                        data.forEach(function(banco) {
                            $('#banco').append(`<option value="${banco.ID_BANCO}">${banco.NOMBRE}</option>`);
                        });
                    } else {
                        console.log("No se encontraron bancos activos");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar bancos:", status, error);
                }
            });

            // Cargar representantes
            $.ajax({
                url: 'get_representantes.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var select = $('#representante');
                    select.empty();
                    select.append('<option value="">Seleccionar</option>');
                    $.each(data, function(index, representante) {
                        select.append('<option value="' + representante.ID_REPRESENTANTE + '">' +
                            representante.APELLIDO_REPRE + ' ' + representante.NOMBRE_REPRE + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar representantes:", status, error);
                }
            });

            // Evento al cambiar el representante
            $('#representante').on('change', function() {
                var id_representante = $(this).val();
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
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al cargar cédula del representante:", status, error);
                        }
                    });

                    // Cargar deportistas asociados
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
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al cargar deportistas:", status, error);
                        }
                    });
                } else {
                    $('#cedula_representante').val('');
                    $('#deportista').empty().append('<option value="">Seleccionar</option>');
                    $('#cedula_deportista').val('');
                }
            });

            // Evento al cambiar el deportista
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
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al cargar cédula del deportista:", status, error);
                        }
                    });
                } else {
                    $('#cedula_deportista').val('');
                }
            });

            // Cambio de método de pago
            $('#metodo_pago').change(function() {
                var metodo = $(this).val();
                if (metodo === 'transferencia') {
                    $('#banco_section').show();
                    $('#comprobante_section').show();
                    $('#entidad_section').show();

                } else {
                    $('#banco_section').hide();
                    $('#comprobante_section').hide();
                    $('#entidad_section').hide();
                }
            });

            //para manejar los campos que se deben imprimir por cada metodo de pago 
            $(document).ready(function() {
                $('#metodo_pago').change(function() {
                    var metodo = $(this).val();
                    if (metodo === 'efectivo') {
                        $('#banco').val(o).hide();
                        $('#entidad_origen').removeAttr('required').prop('disabled', true).hide();
                        $('#nombre_archivo').removeAttr('required').prop('disabled', true).hide();
                    } else if (metodo === 'transferencia') {
                        $('#banco').val('').show(); // Asegurarse de que el campo banco sea requerido
                        $('#entidad_origen').attr('required', true).prop('disabled', false).show();
                        $('#nombre_archivo').attr('required', true).prop('disabled', false).show();
                    }
                }).trigger('change');
            })


            // Actualizar motivo al cambiar el mes
            $('#mes').change(function() {
                var mes = $(this).find('option:selected').text();
                $('#motivo').val('Pago del mes de ' + mes);
            });

            // Registrar pago
            $('#paymentForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'registrar_pagos.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log("Respuesta exitosa:", response);
                        if (response.success) {
                            $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
                            table.ajax.reload();
                            $('#paymentForm')[0].reset();
                        } else {
                            $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    },
                    // error: function(xhr, status, error) {
                    // console.error("Error en la llamada AJAX:", status, error);
                    // console.log("Respuesta del servidor:", xhr.responseText);
                    // $('#message').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                    // }
                });
            });

            // Funcionalidad para los botones de eliminar y editar
            $('#historial_pagos').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                // Implementar lógica para eliminar el pago
            });

            $('#historial_pagos').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                // Implementar lógica para editar el pago
            });
        });
    </script>
</body>

</html>