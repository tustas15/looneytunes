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
    <title>Reporte de Pagos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Reporte de Pagos</h2>
        <form id="reporte-form">
            <div class="mb-3">
                <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                <select id="tipo_reporte" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <option value="individual">Reporte Individual por Deportista</option>
                    <option value="al_dia">Deportistas al Día</option>
                    <option value="no_al_dia">Deportistas no al Día</option>
                </select>
            </div>
            <div id="deportista-section" class="mb-3 d-none">
                <label for="deportista_reporte" class="form-label">Deportista</label>
                <select id="deportista_reporte" class="form-select" required>
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generar Reporte</button>
        </form>

        <div class="mt-5">
            <table id="reporte-tabla" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Monto</th>
                        <th>Fecha de Pago</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos se cargarán aquí -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#reporte-tabla').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#tipo_reporte').on('change', function() {
                if ($(this).val() === 'individual') {
                    $('#deportista-section').removeClass('d-none');
                } else {
                    $('#deportista-section').addClass('d-none');
                }
            });

            $('#reporte-form').on('submit', function(e) {
                e.preventDefault();
                var tipoReporte = $('#tipo_reporte').val();
                var deportista = $('#deportista_reporte').val();

                $.ajax({
                    url: 'generar_reporte.php',
                    method: 'POST',
                    data: {
                        tipo_reporte: tipoReporte,
                        deportista: deportista
                    },
                    dataType: 'json',
                    success: function(data) {
                        table.clear().draw();
                        data.forEach(function(item) {
                            table.row.add([
                                item.nombre,
                                item.mes,
                                item.anio,
                                item.monto,
                                item.fecha_pago,
                                item.motivo
                            ]).draw();
                        });
                    }
                });
            });

            $.ajax({
                url: 'cargar_deportistas.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(deportista) {
                        $('#deportista_reporte').append(`<option value="${deportista.cedula}">${deportista.apellido} ${deportista.nombre}</option>`);
                    });
                }
            });
        });
    </script>
</body>
</html>
