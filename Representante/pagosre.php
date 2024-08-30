<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'tipo_usuario';
include './includes/header.php';
?>

<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="nav-fixed">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Gestión de Pagos</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/looneytunes/admin/indexRep.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Pagos</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-dollar-sign me-1"></i>
                    Formulario de Pago
                </div>
                <div class="card-body">
                    <form id="paymentForm" enctype="multipart/form-data">
                        <input type="hidden" id="id_pago" name="id_pago">
                        <div class="row mb-3">
                            <div class="col-md-6">
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <select id="deportista" name="deportista" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        <!-- Opciones se llenarán dinámicamente -->
                                    </select>
                                    <label for="deportista">Deportista</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" id="cedula_deportista" class="form-control" readonly>
                                    <label for="cedula_deportista">Cédula del Deportista</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <select id="metodo_pago" name="metodo_pago" class="form-select">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                    <label for="metodo_pago">Método de Pago</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" id="monto" name="monto" class="form-control" required>
                                    <label for="monto">Monto</label>
                                </div>
                            </div>
                        </div>
                        <div id="banco_section" style="display:none;">
                            <div class="form-floating mb-3">
                                <select id="banco" name="banco" class="form-select" readonly>
                                    <option value="">Seleccionar</option>
                                    <!-- Opciones se llenarán dinámicamente -->
                                </select>
                                <label for="banco">Banco Destino</label>
                            </div>
                        </div>
                        <div id="entidad_section" style="display:none;">
                            <div class="form-floating mb-3">
                                <input type="text" id="entidad_origen" name="entidad_origen" class="form-control">
                                <label for="entidad_origen">Entidad Financiera de Origen</label>
                            </div>
                        </div>
                        <div id="comprobante_section" style="display:none;">
                            <div class="mb-3">
                                <label for="nombre_archivo" class="form-label">Comprobante</label>
                                <input id="nombre_archivo" name="nombre_archivo" type="file" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="date" class="form-control fecha-actual" id="fecha" name="fecha">
                                    <label for="fecha">Fecha</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3 mb-md-0">
                                    <select id="mes" class="form-select">
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
                                    <label for="mes">Mes</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" id="anio" class="form-control">
                                    <label for="anio">Año</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input id="motivo" name="motivo" class="form-control" readonly>
                            <label for="motivo">Motivo</label>
                        </div>
                        <input type="hidden" id="telefono" name="telefono">
                        <div class="mt-4 mb-0">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block">Registrar Pago</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="message"></div>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Historial de Pagos
                </div>
                <div class="card-body">
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
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('/looneytunes/admin/configuracion/whatsapp/index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al enviar el formulario.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                });
        });

        // Asegúrate de que estas funciones estén presentes en tu código
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>

    <!-- Aquí va tu script JavaScript existente -->
    <script>
        $(document).ready(function() {
            setFechaYMesActual();
            console.log("Documento listo. Iniciando carga de deportistas...");

            // Cargar deportistas asociados al representante actual
            $.ajax({
                url: 'get_deportistas_representante.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log("Datos de deportistas recibidos:", data);
                    var select = $('#deportista');
                    select.empty();
                    select.append('<option value="">Seleccionar</option>');
                    if (data.length > 0) {
                        $.each(data, function(index, deportista) {
                            select.append('<option value="' + deportista.ID_DEPORTISTA + '">' +
                                deportista.APELLIDO_DEPO + ' ' + deportista.NOMBRE_DEPO + '</option>');
                        });
                        console.log("Deportistas cargados exitosamente. Total:", data.length);
                    } else {
                        console.log("No se encontraron deportistas asociados al representante.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar deportistas:");
                    console.error("Estado:", status);
                    console.error("Error:", error);
                    console.error("Respuesta del servidor:", xhr.responseText);
                    alert("Hubo un error al cargar los deportistas. Por favor, revisa la consola para más detalles.");
                }
            });

            // Evento al cambiar el deportista
            $('#deportista').on('change', function() {
                var id_deportista = $(this).val();
                console.log("Deportista seleccionado. ID:", id_deportista);

                if (id_deportista) {
                    console.log("Cargando cédula del deportista...");
                    $.ajax({
                        url: '../Admin/configuracion/pagos/get_cedula_deportista.php',
                        method: 'GET',
                        data: {
                            id_deportista: id_deportista
                        },
                        dataType: 'json',
                        success: function(data) {
                            console.log("Datos de cédula recibidos:", data);
                            if (data && data.CEDULA_DEPO) {
                                $('#cedula_deportista').val(data.CEDULA_DEPO);
                                console.log("Cédula cargada exitosamente.");
                            } else {
                                console.log("No se recibió una cédula válida.");
                                $('#cedula_deportista').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al cargar cédula del deportista:");
                            console.error("Estado:", status);
                            console.error("Error:", error);
                            console.error("Respuesta del servidor:", xhr.responseText);
                            alert("Hubo un error al cargar la cédula del deportista. Por favor, revisa la consola para más detalles.");
                        }
                    });
                } else {
                    console.log("No se seleccionó ningún deportista. Limpiando campo de cédula.");
                    $('#cedula_deportista').val('');
                }
            });

            console.log("Inicialización completa.");










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

            $('#paymentForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var isUpdating = $('#id_pago').val() !== "";
                var url = isUpdating ? '../Admin/configuracion/pagos/actualizar.php' : 'registrar_pago.php';
                var successMessage = isUpdating ? 'Pago actualizado correctamente' : 'Pago registrado correctamente';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: successMessage
                            });
                            table.ajax.reload(null, false); // Recargar la tabla sin perder la página actual
                            $('#paymentForm')[0].reset();
                            setFechaYMesActual();
                            $('button[type="submit"]').text('Registrar Pago');
                            $('#id_pago').val(''); // Limpiar el campo oculto del ID
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la solicitud'
                        });
                    }
                });
            });

            // Edit button logic
            $('#historial_pagos').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                editarPago(id);
            });

            function editarPago(id) {
                $.ajax({
                    url: '../Admin/configuracion/pagos/obtener_pago.php',
                    type: 'GET',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#id_pago').val(data.ID_PAGO);
                        $('#representante').val(data.ID_REPRESENTANTE);
                        $('#deportista').val(data.ID_DEPORTISTA);
                        $('#metodo_pago').val(data.METODO_PAGO);
                        $('#monto').val(data.MONTO);
                        $('#fecha').val(data.FECHA_PAGO);
                        $('#mes').val(data.MES);
                        $('#anio').val(data.ANIO);
                        $('#motivo').val(data.MOTIVO);

                        $('button[type="submit"]').text('Guardar Cambios');

                        $('html, body').animate({
                            scrollTop: $("#paymentForm").offset().top
                        }, 500);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar los datos del pago'
                        });
                    }
                });
            }

            // Funciones auxiliares
            function setFechaYMesActual() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();

                var fechaActual = yyyy + '-' + mm + '-' + dd;
                $('#fecha').val(fechaActual);
                $('#mes').val(mm);
                $('#anio').val(yyyy);

                actualizarMotivo();
            }

            function actualizarMotivo() {
                var mesTexto = $('#mes option:selected').text();
                $('#motivo').val('Pago del mes de ' + mesTexto + ' ');
            }

            $('#mes, #anio').on('change', actualizarMotivo);



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
                            url: '../Admin/configuracion/pagos/eliminar.php',
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
                                    table.ajax.reload();
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
                                    'Error al procesar la solicitud',
                                    'error'
                                );
                            }
                        });
                    }
                })
            })








            // Cargar bancos
            $.ajax({
                url: '../Admin/configuracion/pagos/get_bancos.php',
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
            $('#metodo_pago').change(function() {
                var metodo = $(this).val();
                if (metodo === 'efectivo') {
                    $('#banco').val('').hide();
                    $('#entidad_origen').removeAttr('required').prop('disabled', true).hide();
                    $('#nombre_archivo').removeAttr('required').prop('disabled', true).hide();
                } else if (metodo === 'transferencia') {
                    $('#banco').val('').show(); // Asegurarse de que el campo banco sea requerido
                    $('#entidad_origen').attr('required', true).prop('disabled', false).show();
                    $('#nombre_archivo').attr('required', true).prop('disabled', false).show();
                }
            }).trigger('change');



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
                    url: 'registrar_pago.php',
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
                });
            });
        });
    </script>