<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    if (isset($_REQUEST['deportista_id']) && isset($_SESSION['csrf_token'])) {
        $deportista_id = $_REQUEST['deportista_id'];
        
        // Consulta para obtener la información del deportista
        $query = "SELECT NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO
                  FROM tab_deportistas
                  WHERE ID_DEPORTISTA = ?";
        
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->execute([$deportista_id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($info) {
                // Preparar los datos para DataTables
                $data = array(
                    array("Nombre", htmlspecialchars($info['NOMBRE_DEPO'], ENT_QUOTES)),
                    array("Apellido", htmlspecialchars($info['APELLIDO_DEPO'], ENT_QUOTES)),
                    array("Fecha de Nacimiento", htmlspecialchars($info['FECHA_NACIMIENTO'], ENT_QUOTES)),
                    array("Cédula", htmlspecialchars($info['CEDULA_DEPO'], ENT_QUOTES)),
                    array("Celular", htmlspecialchars($info['NUMERO_CELULAR'], ENT_QUOTES)),
                    array("Género", htmlspecialchars($info['GENERO'], ENT_QUOTES))
                );
                
                echo json_encode(array("data" => $data));
            } else {
                echo json_encode(array("error" => "No se encontró información del deportista."));
            }
        } else {
            echo json_encode(array("error" => "Error en la preparación de la consulta."));
        }
    } else {
        echo json_encode(array("error" => "Parámetros faltantes."));
    }
} catch (Exception $e) {
    echo json_encode(array("error" => "Ocurrió un error: " . $e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Deportista</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <table id="infoDeportista" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Valor</th>
                </tr>
            </thead>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#infoDeportista').DataTable({
            ajax: {
                url: 'tu_script_php.php', // Reemplaza con la ruta correcta a tu script PHP
                type: 'POST',
                data: {
                    deportista_id: '<?php echo $_GET["deportista_id"]; ?>', // Asume que el ID viene por GET
                    csrf_token: '<?php echo $_SESSION["csrf_token"]; ?>'
                }
            },
            columns: [
                { data: 0 },
                { data: 1 }
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
    </script>
</body>
</html>