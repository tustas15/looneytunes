<?php
// Incluye la conexión a la base de datos
require('configuracion/conexion.php');

// Realiza la consulta a la base de datos
$query = "SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
               p.FECHA_PAGO, p.METODO_PAGO, p.MONTO, p.MOTIVO
          FROM tab_pagos p
          INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
          INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
          ORDER BY p.FECHA_PAGO DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Historial de Pagos</h2>
        <table id="historial_pagos" class="table table-striped" style="width:105%">
            <thead>
                <tr>
                    <th>Deportista</th>
                    <th>Representante</th>
                    <th>Tipo de Pago</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Monto</th>
                    <th>Formulario</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $deportista = $row['NOMBRE_DEPO'] . ' ' . $row['APELLIDO_DEPO'];
                        $representante = $row['NOMBRE_REPRE'] . ' ' . $row['APELLIDO_REPRE'];
                        $tipoPago = $row['METODO_PAGO'];
                        $fecha = $row['FECHA_PAGO'];
                        $motivo = $row['MOTIVO'];
                        $monto = $row['MONTO'];
                        $idPago = $row['ID_PAGO'];

                        echo "<tr>
                                <td>{$deportista}</td>
                                <td>{$representante}</td>
                                <td>{$tipoPago}</td>
                                <td>{$fecha}</td>
                                <td>{$motivo}</td>
                                <td>\${$monto}</td>
                                <td>
            <a href='../admin/configuracion/pagos/pagos.php'>
                 Pagos
            </a>
        </td>
      


                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <!-- SweetAlert2 JS -->

    <script>
        $(document).ready(function() {
            var table = $('#historial_pagos').DataTable({

            })
        })
    </script>


</body>

</html>