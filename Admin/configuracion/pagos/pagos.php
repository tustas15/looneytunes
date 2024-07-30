<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
include '../../Includespro/header.php';
?>

<head>
    <meta charset="UTF-8">
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">


</head>

<body>
    <div class="container mt-5">
        <h2>Gestión de Pagos</h2>
        <form id="formulario-pago">
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
                <label for="tipo_pago" class="form-label">Tipo de pago</label>
                <select id="tipo_pago" class="form-select" onchange="mostrarModalPago(this.value)">
                    <option value="">Seleccione</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>

            <!-- Modal para Pago en Efectivo -->
            <div class="modal fade" id="modalEfectivo" tabindex="-1" aria-labelledby="modalEfectivoLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEfectivoLabel">Pago en Efectivo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formulario-efectivo">
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
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="btnRegistrarEfectivo">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para Pago por Transferencia -->
            <div class="modal fade" id="modalTransferencia" tabindex="-1" aria-labelledby="modalTransferenciaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTransferenciaLabel">Pago por Transferencia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formulario-transferencia" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="banco_transferencia" class="form-label">Entidad Financiera</label>
                                    <select id="banco_transferencia" class="form-select">
                                        <option value="Pichincha">Pichincha</option>
                                        <option value="Produbanco">Produbanco</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="numero_factura" class="form-label">Número de Factura</label>
                                    <input type="text" class="form-control" id="numero_factura" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="cuenta_origen" class="form-label">Cuenta de Origen</label>
                                    <input type="text" class="form-control" id="cuenta_origen">
                                </div>
                                <div class="mb-3">
                                    <label for="cuenta_destino" class="form-label">Cuenta de Destino</label>
                                    <input type="text" class="form-control" id="cuenta_destino">
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_transferencia" class="form-label">Fecha de Transferencia</label>
                                    <input type="date" class="form-control" id="fecha_transferencia" value="<?= date('Y-m-d'); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="monto_transferencia" class="form-label">Monto</label>
                                    <input type="number" class="form-control" id="monto_transferencia">
                                </div>
                                <div class="mb-3">
                                    <label for="motivo_transferencia" class="form-label">Descripcion</label>
                                    <input type="text" class="form-control" id="motivo_transferencia">
                                </div>
                                <div class="mb-3">
                                    <label for="comprobante_transferencia" class="form-label">Comprobante de Transferencia</label>
                                    <input type="file" class="form-control" id="comprobante_transferencia" name="comprobante_transferencia" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="btnRegistrarTransferencia">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                        </div>
                    </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
            <script>
                
                function mostrarModalPago(tipo) {
                    if (tipo === 'efectivo') {
                        var modalEfectivo = new bootstrap.Modal(document.getElementById('modalEfectivo'));
                        modalEfectivo.show();
                    } else if (tipo === 'transferencia') {
                        var modalTransferencia = new bootstrap.Modal(document.getElementById('modalTransferencia'));
                        modalTransferencia.show();
                    }
                }
                document.addEventListener('DOMContentLoaded', function() {
                    // Para el modal de efectivo
                    document.querySelector('#modalEfectivo .btn-secondary').addEventListener('click', function() {
                        var modalEfectivo = bootstrap.Modal.getInstance(document.getElementById('modalEfectivo'));
                        modalEfectivo.hide();
                    });

                    // Para el modal de transferencia
                    document.querySelector('#modalTransferencia .btn-secondary').addEventListener('click', function() {
                        var modalTransferencia = bootstrap.Modal.getInstance(document.getElementById('modalTransferencia'));
                        modalTransferencia.hide();
                    });
                });
                // Manejar el envío del formulario de efectivo
                $('#btnRegistrarEfectivo').on('click', function() {
                    var formEfectivo = $('#formulario-efectivo').serialize();
                    $.ajax({
                        url: 'process_efectivo.php',
                        method: 'POST',
                        data: formEfectivo,
                        success: function(response) {
                            console.log('Pago en Efectivo:', response);
                            $('#modalEfectivo').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en el pago en efectivo:', error);
                        }
                    });
                });

                // Manejar el envío del formulario de transferencia
                $('#btnRegistrarTransferencia').on('click', function() {
                    var formData = new FormData(document.getElementById('formulario-transferencia'));
                    $.ajax({
                        url: './subir.php', // El archivo PHP para manejar la subida de archivos
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Pago por Transferencia:', response);
                            $('#modalTransferencia').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en la transferencia:', error);
                        }
                    });
                });
            </script>
            <button type="submit" class="btn btn-primary">Registrar Pago</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para mostrar campos adicionales según tipo de pago
            $('#tipo_pago').on('change', function() {
                var tipoPago = $(this).val();
                if (tipoPago === 'efectivo') {
                    $('#campos-efectivo').removeClass('d-none');
                    $('#campos-transferencia').addClass('d-none');
                } else if (tipoPago === 'transferencia') {
                    $('#campos-efectivo').addClass('d-none');
                    $('#campos-transferencia').removeClass('d-none');
                } else {
                    $('#campos-efectivo').addClass('d-none');
                    $('#campos-transferencia').addClass('d-none');
                }
            });
        })
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
            
            // Generar número de factura
            $.ajax({
                url: 'generar_numero_factura.php',
                method: 'POST',
                data: {
                    id_representante: id_representante,
                    apellido: apellido
                },
                success: function(response) {
                    $('#numero_factura').val(response);
                }
            });
        })

        // Manejar el envío del formulario
        $('#formulario-pago').submit(function(event) {
            event.preventDefault();
            // Aquí puedes agregar la lógica para procesar el pago
            console.log('Formulario enviado');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php include '../../Includespro/footer.php'; ?>