<?php
session_start();
require_once('../conexion.php');
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
// Incluye la cabecera y las dependencias CSS y JS necesarias

// Obtener estadísticas
function obtenerEstadisticas($conn)
{
    $stats = [];

    // Ejemplo de consulta para deportistas al día
    $stmt = $conn->query("SELECT COUNT(*) AS COUNT
FROM (
    SELECT ID_DEPORTISTA
    FROM TAB_PAGOS
    WHERE FECHA_PAGO >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    GROUP BY ID_DEPORTISTA
) AS Pagos_Ultimo_Mes;");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['deportistas_al_dia'] = $row['COUNT'];

    // Ejemplo de consulta para deportistas no al día
    $stmt = $conn->query("SELECT COUNT(*) AS COUNT
FROM (
    SELECT ID_DEPORTISTA
    FROM TAB_PAGOS
    WHERE FECHA_PAGO = (
        SELECT MAX(FECHA_PAGO)
        FROM TAB_PAGOS AS T2
        WHERE T2.ID_DEPORTISTA = TAB_PAGOS.ID_DEPORTISTA
    )
    GROUP BY ID_DEPORTISTA
    HAVING MAX(FECHA_PAGO) <= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
) AS UltimosRegistros;");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['deportistas_no_al_dia'] = $row['COUNT'];



    // Ejemplo de consulta para total meses pagados
    $stmt = $conn->query("SELECT COUNT(*) AS COUNT
FROM (
    SELECT ID_DEPORTISTA
    FROM TAB_PAGOS
    WHERE FECHA_PAGO = (
        SELECT MAX(FECHA_PAGO)
        FROM TAB_PAGOS AS T2
        WHERE T2.ID_DEPORTISTA = TAB_PAGOS.ID_DEPORTISTA
    )
    GROUP BY ID_DEPORTISTA
    HAVING MAX(FECHA_PAGO) <= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
) AS UltimosRegistros;");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['categoria_mayor_atraso'] = $row['COUNT'];

    // Ejemplo de consulta para total meses pagados
    $stmt = $conn->query("SELECT COUNT(DISTINCT MONTH(FECHA_PAGO)) AS COUNT FROM TAB_PAGOS WHERE FECHA_PAGO IS NOT NULL");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_meses_pagados'] = $row['COUNT'];

    return $stats;
}

$stats = obtenerEstadisticas($conn);
include '../../IncludesPro/header.php';

?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/datatables.net@1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.jsdelivr.net/npm/datatables.net@1.10.24/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main>
    <div class="container-fluid px-4">
        <h2 class="mt-4">Generación de Reportes de Pagos</h2>

        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas al Día</h5>
                        <h2 class="display-4" id="deportistas-al-dia"><?php echo $stats['deportistas_al_dia']; ?></h2>
                        <button class="btn btn-light mt-2" onclick="mostrarListado('al-dia')">Ver Listado</button>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Deportistas No al Día</h5>
                        <h2 class="display-4" id="deportistas-no-al-dia"><?php echo $stats['deportistas_no_al_dia']; ?></h2>
                        <button class="btn btn-light mt-2" onclick="mostrarListado('atrasados')">Ver Listado</button>
                    </div>
                </div>
            </div>




            <!-- Card para Categoría con Mayor Número de Pagos Atrasados -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Atrasados por Categoria</h5>
                        <h2 class="display-4" id="categoria-mayor-atraso"><?php echo $stats['categoria_mayor_atraso']; ?></h2>
                        <button class="btn btn-light mt-2" onclick="mostrarListado('categoria-mayor-atraso')">Ver Listado</button>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-blue text-white mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Meses Pagados</h5>
                        <h2 class="display-4" id="total-meses-pagados"><?php echo $stats['total_meses_pagados']; ?></h2>
                        <button class="btn btn-light mt-2" onclick="mostrarListado('meses-pagados')">Ver Listado</button>
                    </div>
                </div>
            </div>
        </div>








        <div class="modal fade" id="deportistasAlDiaModal" tabindex="-1" aria-labelledby="deportistasAlDiaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deportistasAlDiaModalLabel">Listado de Deportistas al Día</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped" id="deportistasAlDiaTable">
                            <thead>
                                <tr>
                                    <th>Deportista</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- El contenido se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="deportistasNoAlDiaModal" tabindex="-1" aria-labelledby="deportistasNoAlDiaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deportistasNoAlDiaModalLabel">Listado de Deportistas No al Día</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped" id="deportistasNoAlDiaTable">
                            <thead>
                                <tr>
                                    <th>Deportista</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- El contenido se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>






        <!-- Modal para la categoría con mayor número de pagos atrasados -->
        <div class="modal fade" id="categoriaMayorAtrasoModal" tabindex="-1" aria-labelledby="categoriaMayorAtrasoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoriaMayorAtrasoModalLabel">Atrasados por Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Cantidad de Atrasos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- El contenido se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>









        <div class="modal fade" id="mesesPagadosModal" tabindex="-1" aria-labelledby="mesesPagadosModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mesesPagadosModalLabel">Listado de Meses Pagados</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped" id="mesesPagadosTable">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Año</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- El contenido se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function mostrarListado(tipo, categoria = null) {
                const modalIds = {
                    'al-dia': '#deportistasAlDiaModal',
                    'atrasados': '#deportistasNoAlDiaModal',
                    'categoria-mayor-atraso': '#categoriaMayorAtrasoModal',
                    'meses-pagados': '#mesesPagadosModal',
                };

                const modalId = modalIds[tipo];
                if (!modalId) {
                    console.error("Tipo de listado desconocido:", tipo);
                    return;
                }

                const modal = new bootstrap.Modal(document.querySelector(modalId));
                const tbody = document.querySelector(`${modalId} tbody`);
                const thead = document.querySelector(`${modalId} thead`);
                tbody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';

                let url = `reportes_modal.php?action=getListado&tipo=${tipo}`;
                if (categoria) url += `&categoria=${encodeURIComponent(categoria)}`;

                console.log("Fetching URL: ", url);

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error("Error en la respuesta: " + response.statusText);
                        return response.json();
                    })
                    .then(response => {
                        console.log("Server response:", response); // Log the entire response for debugging

                        if (!response.success) {
                            throw new Error(response.error || "Error desconocido en el servidor");
                        }

                        const data = response.data;
                        tbody.innerHTML = '';

                        if (!Array.isArray(data)) {
                            throw new Error("Los datos recibidos no son un array");
                        }

                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4">No se encontraron datos</td></tr>';
                            return;
                        }

                        if (tipo === 'meses-pagados') {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    thead.innerHTML = `
        <tr>
            <th>Año</th>
            <th>Mes</th>
            <th>Total Pagado</th>
        </tr>
    `;
    
    data.forEach(d => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${d.ANIO}</td>
            <td>${meses[d.MES - 1]}</td> <!-- Convertimos el número del mes al nombre del mes -->
            <td>$${parseFloat(d.TOTAL).toFixed(2)}</td>
        `;
        tbody.appendChild(tr);
    });
} else if (tipo === 'categoria-mayor-atraso') {
                            thead.innerHTML = `
                    <tr>
                        <th>Categoría</th>
                        <th>Cantidad de Atrasos</th>
                    </tr>
                `;
                            data.forEach(d => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                        <td>${d.CATEGORIA}</td>
                        <td>${d.CANTIDAD_ATRASOS}</td>
                    `;
                                tbody.appendChild(tr);
                            });
                        } else {
                            thead.innerHTML = `
                    <tr>
                        <th>Deportista</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Ultimo Pago</th>
                    </tr>
                `;
                            data.forEach(d => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                        <td>${d.DEPORTISTA}</td>
                        <td>${d.CATEGORIA}</td>
                        <td>$${parseFloat(d.MONTO).toFixed(2)}</td>
                        <td>${d.FECHA}</td>
                    `;
                                tbody.appendChild(tr);
                            });
                        }
                    })
                    .catch(err => {
                        console.error("Error al cargar los datos: ", err);
                        tbody.innerHTML = `<tr><td colspan="4">Error al cargar los datos: ${err.message}</td></tr>`;
                    });

                modal.show();
            }
        </script>
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
               <!-- <button type="submit" class="btn btn-primary float-end">Generar PDF</button>-->
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
                        <tfoot>
                            <!-- La fila de total se insertará aquí -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráfico del Reporte -->
        <div class="col-lg-12">
            <div class="card mb-4" id="chartContainer" style="display:none;">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i> Gráfico de Pagos por Mes
                </div>
                <div class="card-body">
                    <canvas id="myChart"></canvas>
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
                data: {
                    tipo: tipo
                },
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





    $(document).ready(function() {
        $('#reportForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'generar_tabla.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);

                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        return;
                    }
                    if (response.message) {
                        Swal.fire('Información', response.message, 'info');
                        return;
                    }

                    var tipo = response.tipo_informe;
                    var columns;

                    if (tipo == 'categoria') {
                        columns = [{
                                title: "Categoría",
                                data: "NOMBRE"
                            },
                            {
                                title: "Deportista",
                                data: "NOMBRE_COMPLETO"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    } else if (tipo == 'deportista') {
                        columns = [{
                                title: "Deportista",
                                data: "NOMBRE_COMPLETOS"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    } else if (tipo == 'representante') {
                        columns = [{
                                title: "Representante",
                                data: "NOMBRE_COMPLETO_REPRE"
                            },
                            {
                                title: "Deportista",
                                data: "NOMBRE_COMPLETO_DEPO"
                            },
                            {
                                title: "Mes/Año",
                                data: "MES_ANIO"
                            },
                            {
                                title: "Monto pagado",
                                data: "MONTO"
                            },
                            {
                                title: "Estado",
                                data: "ESTADO"
                            }
                        ];
                    }

                    $('#reporteContainer').show();

                    // Calcular el total de montos
                    var total = response.data.reduce((sum, row) => sum + parseFloat(row.MONTO || 0), 0);

                    // Crear la fila de total adaptada al tipo de informe
                    var totalRow = {};
                    columns.forEach(column => {
                        if (column.data === "MONTO") {
                            totalRow[column.data] = total.toFixed(2);
                        } else if (column.data === "NOMBRE" || column.data === "NOMBRE_COMPLETOS" || column.data === "NOMBRE_COMPLETO_REPRE") {
                            totalRow[column.data] = "Total";
                        } else {
                            totalRow[column.data] = "";
                        }
                    });


                    response.data.push(totalRow);




                    // Destruir la tabla si ya existe
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable) {
                        if (typeof $.fn.DataTable === 'undefined') {
                            console.log('DataTables no está cargado. Intentando cargar dinámicamente...');
                            $.getScript('https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js', function() {
                                console.log('DataTables cargado dinámicamente.');
                                // Aquí puedes inicializar tu DataTable o cualquier otra lógica que dependa de DataTables
                            });
                        } else {
                            console.log('DataTables ya está cargado.');
                        }

                        // Inicializar la tabla DataTables
                        ('#reporteTable').DataTable({
                            data: reponse.data,
                            columns: columns,
                            responsive: true,
                            dom: 'Bfrtip',
                            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                            },
                            drawCallback: function(settings) {
                                var api = this.api();
                                var totalColumnIndex = columns.findIndex(col => col.data === "MONTO");
                                $(api.table().footer()).html(
                                    '<tr>' +
                                    columns.map((col, index) =>
                                        index === 0 ? '<th>Total:</th>' :
                                        index === totalColumnIndex ? `<th>${total.toFixed(2)}</th>` : '<th></th>'
                                    ).join('') +
                                    '</tr>'
                                );
                            }

                        });
                    } else {
                        // Si DataTables no está disponible, crea una tabla HTML simple
                        var tableHtml = '<table class="table"><thead><tr>';
                        columns.forEach(function(column) {
                            tableHtml += '<th>' + column.title + '</th>';
                        });
                        tableHtml += '</tr></thead><tbody>';
                        response.data.forEach(function(row) {
                            tableHtml += '<tr>';
                            columns.forEach(function(column) {
                                tableHtml += '<td>' + row[column.data] + '</td>';
                            });
                            tableHtml += '</tr>';
                        });
                        tableHtml += '</tbody></table>';
                        $('#reporteTable').html(tableHtml);
                    }
                    let chartData = {};
                    response.data.forEach(function(row) {
                        if (row.MES_ANIO && row.MONTO && row.MONTO !== total.toFixed(2)) {

                            if (row.MES_ANIO && row.MONTO) {
                                if (chartData[row.MES_ANIO]) {
                                    chartData[row.MES_ANIO] += parseFloat(row.MONTO);
                                } else {
                                    chartData[row.MES_ANIO] = parseFloat(row.MONTO);
                                }
                            }
                        }
                    });

                    let labels = Object.keys(chartData).sort();
                    let data = labels.map(label => chartData[label]);

                    // Crear el gráfico
                    let ctx = document.getElementById('myChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Monto total por mes',
                                data: data,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Monto ($)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Mes/Año'
                                    }
                                }
                            }
                        }
                    });

                    // Mostrar el contenedor del gráfico
                    $('#chartContainer').show();









                    // Agregar evento al botón de generar PDF
                    $('.btn-primary.float-end').on('click', function(e) {
                        console.log('Botón de generar PDF clickeado'); // Verificar si el botón fue clickeado
                        e.preventDefault();

                        var formData = new FormData($('#reportForm')[0]);
                        console.log('Datos del formulario:', formData); // Mostrar los datos del formulario para verificar que se están enviando correctamente

                        $.ajax({
                            url: 'generar_pdf.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            xhrFields: {
                                responseType: 'blob' // to avoid binary data being mangled on charset conversion
                            },
                            success: function(blob, status, xhr) {
                                console.log('PDF generado con éxito');
                                console.log('Estado de la respuesta:', status);
                                console.log('XHR:', xhr);

                                if (blob.size === 0) {
                                    console.error('El archivo generado está vacío.');
                                    alert('Hubo un problema al generar el PDF. El archivo parece estar vacío.');
                                    return;
                                }

                                var filename = "";
                                var disposition = xhr.getResponseHeader('Content-Disposition');
                                console.log('Content-Disposition:', disposition);

                                if (disposition && disposition.indexOf('attachment') !== -1) {
                                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                    var matches = filenameRegex.exec(disposition);
                                    if (matches != null && matches[1]) {
                                        filename = matches[1].replace(/['"]/g, '');
                                        console.log('Nombre del archivo:', filename);
                                    }
                                }

                                var URL = window.URL || window.webkitURL;
                                var downloadUrl = URL.createObjectURL(blob);
                                console.log('URL de descarga:', downloadUrl);

                                if (filename) {
                                    var a = document.createElement("a");
                                    if (typeof a.download === 'undefined') {
                                        console.log('Safari detectado, redirigiendo a la URL de descarga');
                                        window.location.href = downloadUrl;
                                    } else {
                                        a.href = downloadUrl;
                                        a.download = filename;
                                        document.body.appendChild(a);
                                        console.log('Iniciando descarga del archivo');
                                        a.click();
                                    }
                                } else {
                                    console.log('Descarga sin nombre de archivo, redirigiendo directamente');
                                    window.location.href = downloadUrl;
                                }

                                setTimeout(function() {
                                    console.log('Revocando URL de descarga');
                                    URL.revokeObjectURL(downloadUrl);
                                }, 100);
                            },

                        });
                    });
                }
            })
        })
    })
</script>


<?php include '../../Includespro/footer.php'; ?>
</body>

</html>