<?php
session_start();
require_once('../conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

// Incluye la cabecera y las dependencias CSS y JS necesarias
include '../../includespro/header.php';
?>
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <main>
    <div class="container-fluid px-4">
        <h2 class="mt-4">Generación de Reportes de Pagos</h2>

        <!-- Tarjetas de Resumen -->
        <div class="row mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Pagos</h5>
                        <h2 class="display-4" id="total-pagos">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Monto Total</h5>
                        <h2 class="display-4" id="monto-total">$0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas al Día</h5>
                        <h2 class="display-4" id="deportistas-al-dia">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas Atrasados</h5>
                        <h2 class="display-4" id="deportistas-atrasados">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Generación de Reportes -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Parámetros del Reporte
            </div>
            <div class="card-body">
                <form id="reportForm" method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Límite</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_informe" class="form-label">Tipo de Reporte</label>
                            <select id="tipo_informe" name="tipo_informe" class="form-select" required>
                                <option value="">Seleccione tipo de reporte</option>
                                <option value="categoria">Categoría</option>
                                <option value="deportista">Deportista</option>
                                <option value="representante">Representante</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="id_especifico" class="form-label">Seleccione</label>
                            <select id="id_especifico" name="id_especifico" class="form-select" disabled>
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Reportes -->
        <div class="card mb-4" id="reporteContainer" style="display:none;">
            <div class="card-header">
                <i class="fas fa-table"></i> Tabla de Reportes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reporteTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <!-- Las columnas se generarán dinámicamente -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se insertarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráfico del Reporte -->
        <div class="col-lg-12">
            <div class="card mb-4" id="chartContainer" style="display:none;">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Gráfico del Reporte
                </div>
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fechaFin = document.getElementById('fecha_fin');
        const hoy = new Date().toISOString().split('T')[0];
        fechaFin.value = hoy;
    });

    
        $('#tipo_informe').on('change', function() {
            var tipo = $(this).val();
            console.log('Tipo de informe seleccionado:', tipo);

            if (tipo) {
                $.ajax({
                    url: 'get_options.php',
                    type: 'POST',
                    data: { tipo: tipo },
                    success: function(response) {
                        console.log('Respuesta de get_options.php:', response);
                        $('#id_especifico').html(response);
                        $('#id_especifico').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud AJAX:', status, error);
                    }
                });
            } else {
                $('#id_especifico').html('<option value="">Seleccione una opción</option>');
                $('#id_especifico').prop('disabled', true);
            }
        });
 

    $('#reportForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'generar_tabla.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                console.log('Datos recibidos del servidor:', data);
                if(data.error) {
                    Swal.fire('Error', data.error, 'error');
                    return;
                }
                if(data.message) {
                    Swal.fire('Información', data.message, 'info');
                    return;
                }
                
                var tipo = $('#tipo_informe').val();
                var columns;

                if(tipo == 'categoria') {
                    columns = [
                        { title: "Nombre de la categoría", data: "NOMBRE" },
                        { title: "Nombre del deportista", data: "NOMBRE_DEPO" },
                        { title: "Mes/Año", data: "MES_ANIO" },
                        { title: "Monto pagado", data: "MONTO" },
                        { title: "Estado", data: "ESTADO" }
                    ];
                } else if(tipo == 'deportista') {
                    columns = [
                        { title: "Nombre del deportista", data: "NOMBRE" },
                        { title: "Mes/Año", data: "MES_ANIO" },
                        { title: "Monto pagado", data: "MONTO" },
                        { title: "Estado", data: "ESTADO" }
                    ];
                } else if(tipo == 'representante') {
                    columns = [
                        { title: "Nombre del representante", data: "NOMBRE" },
                        { title: "Nombre del deportista", data: "NOMBRE_DEPO" },
                        { title: "Mes/Año", data: "MES_ANIO" },
                        { title: "Monto pagado", data: "MONTO" },
                        { title: "Estado", data: "ESTADO" }
                    ];
                }

            }
        })
    })


</script>

   
    <?php include '../../Includespro/footer.php'; ?>
</body>
</html>