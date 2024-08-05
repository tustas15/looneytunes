$(document).ready(function() {
    // Ocultar y mostrar campos según el tipo de pago
    $('#tipo_pago').change(function() {
        var tipoPago = $(this).val();
        $('.payment-fields').hide();
        if (tipoPago === 'efectivo') {
            $('#campos-efectivo').show();
        } else if (tipoPago === 'transferencia') {
            $('#campos-transferencia').show();
        }
    });

    // Manejar el envío del formulario
    $('#formulario-pago').submit(function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario
        var formData = new FormData(this);

        $.ajax({
            url: 'procesar_pagos.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $('#mensaje-confirmacion').text('Pago registrado correctamente').removeClass('alert-danger').addClass('alert-success').show();
                        $('#formulario-pago')[0].reset();
                        cargarHistorialPagos();
                        setTimeout(function() {
                            $('#mensaje-confirmacion').hide();
                        }, 3000);
                    } else {
                        $('#mensaje-confirmacion').text('Error al registrar el pago: ' + result.message).removeClass('alert-success').addClass('alert-danger').show();
                    }
                } catch (e) {
                    console.error("Error al procesar la respuesta:", e);
                    $('#mensaje-confirmacion').text('Error al procesar la respuesta del servidor').removeClass('alert-success').addClass('alert-danger').show();
                }
            },
            error: function() {
                $('#mensaje-confirmacion').text('Error al procesar la solicitud').removeClass('alert-success').addClass('alert-danger').show();
            }
        });
    });

    // Cargar historial de pagos
    function cargarHistorialPagos() {
        $.ajax({
            type: 'GET',
            url: 'historial_pagos.php',
            success: function(response) {
                $('#historial_pagos').html(response);
                $('#tabla-pagos').DataTable();
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar el historial de pagos:", status, error);
                $('#historial_pagos').html("Error al cargar el historial de pagos.");
            }
        });
    }

    // Inicializar DataTables
    function initDataTable() {
        $('#tabla-pagos').DataTable({
            ajax: {
                url: 'historial_pagos.php',
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
    }

    // Cargar bancos al iniciar la página
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

    // Cargar apellidos de representantes
    $.ajax({
        url: 'get_representantes.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = $('#apellido_representante');
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

    // Inicializar DataTables
    initDataTable();
    cargarHistorialPagos();
});
