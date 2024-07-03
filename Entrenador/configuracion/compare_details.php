<?php
require_once('../Admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_temp_deportista = $_POST['id_temp_deportista'];

    // Consulta modificada para obtener todos los registros históricos
    $sql = "SELECT td.ID_TEMP_DEPORTISTA, td.NOMBRE_DEPO, td.APELLIDO_DEPO, td.CEDULA_DEPO, 
                   td.FECHA_NACIMIENTO, td.NUMERO_CELULAR, td.GENERO,
                   d.ID_DETALLE, d.NUMERO_CAMISA, d.ALTURA, d.PESO, d.FECHA_INGRESO
            FROM tab_temp_deportistas td 
            LEFT JOIN tab_detalles d ON td.ID_DEPORTISTA = d.ID_DEPORTISTA
            WHERE td.ID_TEMP_DEPORTISTA = :id_temp_deportista
            ORDER BY d.FECHA_INGRESO DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_temp_deportista', $id_temp_deportista, PDO::PARAM_INT);
    $stmt->execute();

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($registros) {
        echo '<h3>Historial de datos del deportista</h3>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr>
                <th>Fecha de Ingreso</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Fecha de Nacimiento</th>
                <th>Número de Celular</th>
                <th>Género</th>
                <th>Número de Camiseta</th>
                <th>Altura</th>
                <th>Peso</th>
                <th>Acción</th>
              </tr></thead><tbody>';
        
        foreach ($registros as $registro) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($registro['FECHA_INGRESO'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['NOMBRE_DEPO']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['APELLIDO_DEPO']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['CEDULA_DEPO']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['FECHA_NACIMIENTO']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['NUMERO_CELULAR']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['GENERO']) . '</td>';
            echo '<td>' . htmlspecialchars($registro['NUMERO_CAMISA'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['ALTURA'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['PESO'] ?? 'N/A') . '</td>';
            echo '<td><button class="btn btn-danger btn-sm delete-historical-detail" data-id="' . $registro['ID_DETALLE'] . '">Eliminar</button></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Add JavaScript for delete functionality
        echo '<script>
        $(document).ready(function() {
            $(".delete-historical-detail").on("click", function() {
                var id = $(this).data("id");
                if (confirm("¿Está seguro de que desea eliminar este detalle histórico?")) {
                    $.ajax({
                        url: "eliminar_detalle.php",
                        type: "POST",
                        data: {id_detalle: id},
                        success: function(response) {
                            if (response === "success") {
                                alert("Detalle eliminado con éxito");
                                $("#select-alumno").trigger("change");
                            } else {
                                alert("Error al eliminar el detalle");
                            }
                        },
                        error: function() {
                            alert("Error de conexión con el servidor");
                        }
                    });
                }
            });
        });
        </script>';
    } else {
        echo 'No se encontraron detalles para el deportista seleccionado.';
    }
}
?>