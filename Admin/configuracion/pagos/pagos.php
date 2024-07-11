<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');


include '../../Includespro/header.php';
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
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
</head>

<body id="page-top">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Pagos</h1>
        <a href="../configuracion/respaldo/downloadFile.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generar Respaldo</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registrar Nuevo Pago</h6>
        </div>
        <div class="card-body">
            <form id="formulario-pago" method="POST">
                <div class="form-group">
                    <label for="cedula">Cédula del Representante:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" required>
                </div>

                <div class="form-group">
                    <label for="nombre_representante">Nombre del Representante:</label>
                    <input type="text" class="form-control" id="nombre_representante" name="nombre_representante" required>
                </div>

                <div class="form-group">
                    <label for="tipo_pago">Método de Pago:</label>
                    <select class="form-control" id="tipo_pago" name="tipo_pago" onchange="toggleFields(this.value)" required>
                        <option value="">Seleccione...</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>

                <div id="efectivoFields" style="display:none;">
                    <div class="form-group">
                        <label for="nombre_deportista_efectivo">Nombre del Deportista:</label>
                        <input type="text" class="form-control" id="nombre_deportista_efectivo" name="nombre_deportista_efectivo">
                    </div>

                    <div class="form-group">
                        <label for="motivo_efectivo">Motivo:</label>
                        <input type="text" class="form-control" id="motivo_efectivo" name="motivo_efectivo">
                    </div>

                    <div class="form-group">
                        <label for="monto_efectivo">Monto:</label>
                        <input type="number" step="0.01" class="form-control" id="monto_efectivo" name="monto_efectivo">
                    </div>

                    <div class="form-group">
                        <label for="fecha_efectivo">Fecha:</label>
                        <input type="date" class="form-control fecha-actual" id="fecha_efectivo" name="fecha_efectivo">
                    </div>
                </div>

                <div id="transferenciaFields" style="display:none;">
                    <div class="form-group">
                        <label for="nombre_deportista_transferencia">Nombre del Deportista:</label>
                        <input type="text" class="form-control" id="nombre_deportista_transferencia" name="nombre_deportista_transferencia">
                    </div>

                    <div class="form-group">
                        <label for="motivo_transferencia">Motivo:</label>
                        <input type="text" class="form-control" id="motivo_transferencia" name="motivo_transferencia">
                    </div>

                    <div class="form-group">
                        <label for="banco">Nombre del Banco:</label>
                        <select class="form-control" id="banco" name="banco">
        <option value="">Seleccione un banco...</option>
        <option value="Pichincha">Pichincha</option>
        <option value="Austro">Austro</option>
        <option value="Pacifico">Pacífico</option>
        <option value="Produbanco">Produbanco</option>
    </select>
                    </div>

                    <div class="form-group">
                        <label for="monto_transferencia">Monto:</label>
                        <input type="number" step="0.01" class="form-control" id="monto_transferencia" name="monto_transferencia">
                    </div>

                    <div class="form-group">
                        <label for="fecha_transferencia">Fecha:</label>
                        <input type="date" class="form-control fecha-actual" id="fecha_transferencia" name="fecha_transferencia">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-registrar">Registrar Pago</button>
            </form>

            <div id="mensaje-confirmacion" class="alert alert-success mt-3" style="display: none;">
                Pago registrado correctamente
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div id="historial_pagos">
            <div class="card-header py-3">
            </div>
        </div>
    </div>

    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <?php include '../../Includespro/footer.php'; ?>
    <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery y Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- DataTables y botones -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    <script>
        function toggleFields(paymentType) {
            const efectivoFields = document.getElementById('efectivoFields');
            const transferenciaFields = document.getElementById('transferenciaFields');
            if (paymentType === 'efectivo') {
                efectivoFields.style.display = 'block';
                transferenciaFields.style.display = 'none';
            } else if (paymentType === 'transferencia') {
                efectivoFields.style.display = 'none';
                transferenciaFields.style.display = 'block';
            } else {
                efectivoFields.style.display = 'none';
                transferenciaFields.style.display = 'none';
            }
            setFechaActual();
        }

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

            /* Función para inicializar DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#tabla_pagos')) {
                    $('#tabla_pagos').DataTable().destroy();
                }
                $('#tabla_pagos').DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    responsive: true
                });
            }
*/
            // Manejar el evento de submit del formulario
            $('#formulario-pago').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'procesar_pago.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#mensaje-confirmacion').show().delay(3000).fadeOut();
                        $('#formulario-pago')[0].reset();
                        cargarHistorialPagos();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al procesar el pago:", status, error);
                        alert("Error al procesar el pago. Por favor, intenta de nuevo.");
                    }
                });
            });

            // Función para establecer la fecha actual en los campos de fecha
            function setFechaActual() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //Enero es 0!
                var yyyy = today.getFullYear();

                today = yyyy + '-' + mm + '-' + dd;
                $('.fecha-actual').val(today);
            }

            // Llamar a la función cuando se carga la página
            setFechaActual();

            // Llamar a la función cada vez que se cambia el tipo de pago
            $('#tipo_pago').change(function() {
                setFechaActual();
            });
        });
    </script>

</body>

</html>