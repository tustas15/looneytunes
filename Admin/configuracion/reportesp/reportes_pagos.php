<?php
// Conexión a la base de datos
session_start();
require_once('../conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
include '../../Includespro/header.php';
?>

<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="nav-fixed">
    <main>
        <div class="container-fluid px-4">
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
        <select id="deportista_reporte" class="form-select">
            <option value="">Seleccionar</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
        <input type="date" id="fecha_inicio" class="form-control">
    </div>
    <div class="mb-3">
        <label for="fecha_fin" class="form-label">Fecha de Fin</label>
        <input type="date" id="fecha_fin" class="form-control">
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
        var fechaInicio = $('#fecha_inicio').val();
        var fechaFin = $('#fecha_fin').val();

        $.ajax({
            url: 'generar_reporte.php',
            method: 'POST',
            data: {
                tipo_reporte: tipoReporte,
                deportista: deportista,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            },
            dataType: 'json',
            success: function(data) {
                table.clear().draw();
                data.forEach(function(item) {
                    table.row.add([
                        item.NOMBRE_DEPO + ' ' + item.APELLIDO_DEPO,
                        item.mes,
                        item.anio,
                        item.MONTO,
                        item.FECHA_PAGO,
                        item.MOTIVO
                    ]).draw();
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                alert("Hubo un error al generar el reporte. Por favor, intente de nuevo.");
            }
        });
    });

    $.ajax({
        url: 'cargar_deportistas.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(deportista) {
                $('#deportista_reporte').append(`<option value="${deportista.CEDULA_DEPO}">${deportista.APELLIDO_DEPO} ${deportista.NOMBRE_DEPO}</option>`);
            });
        }
    });
});
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
                        deportista: deportista,
                        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
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
                        $('#deportista_reporte').append(`<option value="${deportista.CEDULA_DEPO}">${deportista.APELLIDO_DEPO} ${deportista.NOMBRE_DEPO}</option>`);
                    });
                }
            });
      
    </script>
</body>
</html>
