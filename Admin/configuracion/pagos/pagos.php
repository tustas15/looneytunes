<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago</title>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Formulario de Pago</h2>
        <form id="paymentForm" enctype="multipart/form-data">
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
                <select id="metodo_pago" nombre="metodo_pago" class="form-control">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            <div class="form-group" id="banco_section" style="display:none;">
                <label for="banco">Banco:</label>
                <select id="banco" name="banco" class="form-control" readonly>
                    <!-- Options will be populated by JS -->
                </select>
            </div>
            <div class="form-group" id="comprobante_section" style="display:none;">
                <label for="comprobante">Comprobante:</label>
                <input id="comprobante" name="comprobante" type="file" class="form-control">
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
                <!-- Data will be populated by JS -->
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
    // Inicializar datatable
    var table = $('#historial_pagos').DataTable({
        ajax: {
            url: 'historial_pagos.php',
            dataSrc: ''
        },
        columns: [
            { data: 'deportista' },
            { data: 'representante' },
            { data: 'metodo_pago' },
            { data: 'fecha_pago' },
            { data: 'motivo' },
            { data: 'monto' },
            {
                data: 'id_pago',
                render: function(data, type, row, meta) {
                    return '<button class="btn btn-danger delete-btn" data-id="' + data + '">Eliminar</button> ' +
                           '<button class="btn btn-warning edit-btn" data-id="' + data + '">Editar</button>';
                }
            }
        ],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
    table.buttons().container().appendTo('#historial_pagos_wrapper .col-md-6:eq(0)');

    // Cargar bancos
    $.ajax({
        url: 'get_bancos.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#banco').empty().append('<option value="">Seleccionar</option>');
            if (data.length > 0) {
                data.forEach(function(banco) {
                    $('#banco').append(`<option value="${banco.id}">${banco.nombre}</option>`);
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
                data: { id_representante: id_representante },
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
                data: { id_representante: id_representante },
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
                data: { id_deportista: id_deportista },
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
        } else {
            $('#banco_section').hide();
            $('#comprobante_section').hide();
        }
    });

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
            error: function(xhr, status, error) {
                console.error("Error en la llamada AJAX:", status, error);
                console.log("Respuesta del servidor:", xhr.responseText);
                $('#message').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
            }
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