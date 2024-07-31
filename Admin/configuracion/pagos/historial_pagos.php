<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$sql = "SELECT p.id_pago, r.APELLIDO_REPRE, r.NOMBRE_REPRE, r.CEDULA_REPRE, 
               d.APELLIDO_DEPO, d.NOMBRE_DEPO, d.CEDULA_DEPO, 
               p.tipo_pago, p.fecha, p.motivo, p.monto, 
               pd.mes, pd.anio, b.nombre as banco_nombre, pd.entidad_financiera
        FROM tab_pagos p
        JOIN tab_representantes r ON p.id_representante = r.ID_REPRESENTANTE
        JOIN tab_deportistas d ON p.id_deportista = d.ID_DEPORTISTA
        LEFT JOIN tab_pago_detalle pd ON p.id_pago = pd.id_pago
        LEFT JOIN tab_bancos b ON pd.banco_destino = b.id
        ORDER BY p.fecha DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Pagos</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Historial de Pagos</h2>
        <table id="historialPagos" class="table table-striped">
            <thead>
                <tr>
                    <th>Representante</th>
                    <th>Cédula Representante</th>
                    <th>Deportista</th>
                    <th>Cédula Deportista</th>
                    <th>Tipo Pago</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Motivo</th>
                    <th>Banco</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['APELLIDO_REPRE'] . ' ' . $row['NOMBRE_REPRE']; ?></td>
                    <td><?php echo $row['CEDULA_REPRE']; ?></td>
                    <td><?php echo $row['APELLIDO_DEPO'] . ' ' . $row['NOMBRE_DEPO']; ?></td>
                    <td><?php echo $row['CEDULA_DEPO']; ?></td>
                    <td><?php echo ucfirst($row['tipo_pago']); ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['mes'] . '-' . $row['anio']; ?></td>
                    <td><?php echo $row['motivo']; ?></td>
                    <td><?php echo '$' . number_format($row['monto'], 2); ?></td>
                    <td><?php echo $row['banco_nombre']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="generarComprobante(<?php echo $row['id_pago']; ?>)">Generar</button>
                        <button class="btn btn-sm btn-warning" onclick="editarPago(<?php echo $row['id_pago']; ?>)">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPago(<?php echo $row['id_pago']; ?>)">Eliminar</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#historialPagos').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });

        function generarComprobante(idPago) {
            // Implementar la generación del comprobante
        }

        function editarPago(idPago) {
            // Implementar la edición del pago
        }

        function eliminarPago(idPago) {
            // Implementar la eliminación del pago
        }
    </script>
</body>
</html>
