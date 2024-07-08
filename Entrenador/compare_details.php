<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

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
        // Generar datos para la gráfica
        $grafica_datos = [];
        foreach ($registros as $registro) {
            $altura = $registro['ALTURA'] ?? 0;
            $peso = $registro['PESO'] ?? 0;
            $fecha = $registro['FECHA_INGRESO'] ?? 'N/A';

            if ($altura > 0 && $peso > 0) {
                // Calcular IMC: peso (kg) / (altura (m))^2
                $imc = $peso / (($altura / 100) * ($altura / 100));
                $grafica_datos[] = [
                    'fecha' => $fecha,
                    'imc' => $imc
                ];
            }
        }

        echo '<h3>Historial de datos del deportista</h3>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr>
                <th>Fecha de Ingreso</th>
                <th>Número de Camiseta</th>
                <th>Altura</th>
                <th>Peso</th>
                <th>Acción</th>
              </tr></thead><tbody>';
        
        foreach ($registros as $registro) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($registro['FECHA_INGRESO'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['NUMERO_CAMISA'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['ALTURA'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($registro['PESO'] ?? 'N/A') . '</td>';
            echo '<td><button class="btn btn-danger btn-sm delete-historical-detail" data-id="' . $registro['ID_DETALLE'] . '">Eliminar</button></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Pasar datos de la gráfica a JavaScript
        echo '<script>
        var graficaDatos = ' . json_encode($grafica_datos) . ';
        </script>';

        // Add JavaScript for delete functionality and Chart.js
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
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

            // Crear gráfica
            var ctx = document.getElementById("imcChart").getContext("2d");
            var imcChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: graficaDatos.map(d => d.fecha),
                    datasets: [{
                        label: "IMC",
                        data: graficaDatos.map(d => d.imc),
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        x: {
                            type: "time",
                            time: {
                                unit: "month"
                            },
                            reverse: true // Esta propiedad invierte el eje X
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        </script>';
        echo '<canvas id="imcChart" width="400" height="200"></canvas>';
    } else {
        echo 'No se encontraron detalles para el deportista seleccionado.';
    }
}
?>
