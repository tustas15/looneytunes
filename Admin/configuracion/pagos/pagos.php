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
</head>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Pagos</h2>
        <form id="formulario-pago">
            <div class="mb-3">
                <label for="apellido_representante" class="form-label">Apellido del Representante</label>
                <select id="apellido_representante" class="form-select" required>
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre_representante" class="form-label">Nombre del Representante</label>
                <input type="text" class="form-control" id="nombre_representante" readonly>
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
            <button type="submit" class="btn btn-primary">Registrar Pago</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar apellidos de representantes
            $.ajax({
                url: 'get_representantes.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(representante) {
                            $('#apellido_representante').append(`<option value="${representante.ID_REPRESENTANTE}" data-deportista="${representante.ID_DEPORTISTA}">${representante.APELLIDO_REPRE}</option>`);
                        });
                    } else {
                        console.log("No se encontraron representantes");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar representantes:", status, error);
                }
            });

            // Cuando se selecciona un apellido
            $('#apellido_representante').on('change', function() {
                var id_representante = $(this).val();
                var id_deportista = $(this).find(':selected').data('deportista');
                if (id_representante) {
                    // Cargar nombre y cédula del representante
                    $.ajax({
                        url: 'get_nombre_representante.php',
                        method: 'GET',
                        data: { id_representante: id_representante },
                        dataType: 'json',
                        success: function(data) {
                            $('#nombre_representante').val(data.NOMBRE_REPRE);
                            $('#cedula_representante').val(data.CEDULA_REPRE);
                        }
                    });

                    // Cargar deportistas asociados y seleccionar el deportista automáticamente
                    $.ajax({
                        url: 'get_deportistas.php',
                        method: 'GET',
                        data: { id_representante: id_representante },
                        dataType: 'json',
                        success: function(data) {
                            $('#deportista').empty().append('<option value="">Seleccionar</option>');
                            data.forEach(function(deportista) {
                                $('#deportista').append(`<option value="${deportista.ID_DEPORTISTA}">${deportista.NOMBRE_DEPO} ${deportista.APELLIDO_DEPO}</option>`);
                            });
                            // Seleccionar automáticamente el deportista asociado
                            $('#deportista').val(id_deportista).change();
                        }
                    });
                } else {
                    $('#nombre_representante').val('');
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
                        data: { id_deportista: id_deportista },
                        dataType: 'json',
                        success: function(data) {
                            $('#cedula_deportista').val(data.CEDULA_DEPO);
                        }
                    });
                } else {
                    $('#cedula_deportista').val('');
                }
            });

            // Manejar el envío del formulario
            $('#formulario-pago').submit(function(event) {
                event.preventDefault();
                // Aquí puedes agregar la lógica para procesar el pago
                console.log('Formulario enviado');
            });
        });
    </script>
</body>
</html>


<?php include '../../Includespro/footer.php'; ?>