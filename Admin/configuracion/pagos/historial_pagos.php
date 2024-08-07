<?php
// Incluir archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    // Consulta SQL para obtener el historial de pagos
    $sql = "
        SELECT p.ID_PAGO, r.nombre_repre, r.apellido_repre, d.nombre_depo, d.apellido_depo, p.FECHA, p.METODO_PAGO, p.MONTO, p.MOTIVO, p.NOMBRE_ARCHIVO
        FROM tab_pagos p
        INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        ORDER BY p.FECHA DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generar el HTML para el historial de pagos
    if ($stmt->rowCount() > 0) {
        echo '<div class="card shadow mb-4">';
        echo '<div class="card-header py-3">';
        echo '<h6 class="m-0 font-weight-bold text-primary">Historial de Pagos</h6>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<div class="table-responsive">';
        echo '<table id="historial_pagos" class="table table-striped table-bordered">
 width="100%" cellspacing="0">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Representante</th>';
        echo '<th>Deportista</th>';
        echo '<th>Fecha</th>';
        echo '<th>Metodo de Pago</th>';
        echo '<th>Monto</th>';
        echo '<th>Motivo</th>';
        echo '<th>Comprobante</th>';
        echo '<th>Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($pagos as $pago) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($pago['nombre_repre'] . ' ' . $pago['apellido_repre']) . '</td>';
            echo '<td>' . htmlspecialchars($pago['nombre_depo'] . ' ' . $pago['apellido_depo']) . '</td>';
            echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($pago['FECHA']))) . '</td>';  // Formatear la fecha
            echo '<td>' . htmlspecialchars($pago['METODO_PAGO']) . '</td>';
            echo '<td>$' . number_format($pago['MONTO'], 2) . '</td>';
            echo '<td>' . htmlspecialchars($pago['MOTIVO']) . '</td>';
            echo '<td>';
            if ($pago['NOMBRE_ARCHIVO']) {
                echo '<a href="uploads/' . htmlspecialchars($pago['NOMBRE_ARCHIVO']) . '" target="_blank">Ver Comprobante</a>';
            } else {
                echo 'No Aplica';
            }
            echo '</td>';
            echo '<td>
                    <a href="editar.php?id=' . $pago['ID_PAGO'] . '" class="btn btn-primary btn-sm">Actualizar</a>
                    <a href="eliminar.php?id=' . $pago['ID_PAGO'] . '" class="btn btn-danger btn-sm">Eliminar</a>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Inicializar DataTables
        echo '<script>';
        echo '$(document).ready(function() {';
        echo '$("#historial_pagos").DataTable({';
        echo '    "language": {';
        echo '        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"';
        echo '    },';
        echo '    "dom": "Bfrtip",';
        echo '    "buttons": [';
        echo '        {';
        echo '            extend: "copy",';
        echo '            exportOptions: {';
        echo '                columns: ":not(.no-export)"';
        echo '            }';
        echo '        },';
        echo '        {';
        echo '            extend: "csv",';
        echo '            exportOptions: {';
        echo '                columns: ":not(.no-export)"';
        echo '            }';
        echo '        },';
        echo '        {';
        echo '            extend: "excel",';
        echo '            exportOptions: {';
        echo '                columns: ":not(.no-export)"';
        echo '            }';
        echo '        },';
        echo '        {';
        echo '            extend: "pdf",';
        echo '            exportOptions: {';
        echo '                columns: ":not(.no-export)"';
        echo '            }';
        echo '        },';
        echo '        {';
        echo '            extend: "print",';
        echo '            exportOptions: {';
        echo '                columns: ":not(.no-export)"';
        echo '            }';
        echo '        }';
        echo '    ],';
        echo '    "responsive": true,';
        echo '    "columnDefs": [';
        echo '        {';
        echo '            "targets": -1,';  // Asumiendo que la columna de acciones es la última
        echo '            "className": "no-export"';
        echo '        }';
        echo '    ]';
        echo '});';
        echo '});';
        echo '</script>';
    } else {
        echo '<div class="alert alert-info" role="alert">No se encontraron pagos.</div>';
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al obtener los pagos: ' . $e->getMessage() . '</div>';
}
