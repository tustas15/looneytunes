<?php
require_once('../Admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_temp_deportista = $_POST['id_temp_deportista'];

    // Consulta para obtener los detalles del deportista temporal y sus detalles específicos
    $sql = "WITH Ordenados AS (
    SELECT td.ID_TEMP_DEPORTISTA, td.NOMBRE_DEPO, td.APELLIDO_DEPO, td.CEDULA_DEPO, 
           td.FECHA_NACIMIENTO, td.NUMERO_CELULAR, td.GENERO,
           d.NUMERO_CAMISA, d.ALTURA, d.PESO, d.FECHA_INGRESO,
           ROW_NUMBER() OVER (PARTITION BY td.ID_TEMP_DEPORTISTA ORDER BY d.FECHA_INGRESO DESC) AS fila
    FROM tab_temp_deportistas td 
    LEFT JOIN tab_detalles d ON td.ID_DEPORTISTA = d.ID_DEPORTISTA
    WHERE td.ID_TEMP_DEPORTISTA = :id_temp_deportista
)
SELECT ID_TEMP_DEPORTISTA, NOMBRE_DEPO, APELLIDO_DEPO, CEDULA_DEPO, 
       FECHA_NACIMIENTO, NUMERO_CELULAR, GENERO,
       NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO
FROM Ordenados
WHERE fila = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_temp_deportista', $id_temp_deportista, PDO::PARAM_INT);
    $stmt->execute();

    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deportista) {
        echo '<table class="table table-bordered">';
        echo '<tr><th>Nombre</th><td>' . htmlspecialchars($deportista['NOMBRE_DEPO']) . '</td></tr>';
        echo '<tr><th>Apellido</th><td>' . htmlspecialchars($deportista['APELLIDO_DEPO']) . '</td></tr>';
        echo '<tr><th>Cédula</th><td>' . htmlspecialchars($deportista['CEDULA_DEPO']) . '</td></tr>';
        echo '<tr><th>Fecha de Nacimiento</th><td>' . htmlspecialchars($deportista['FECHA_NACIMIENTO']) . '</td></tr>';
        echo '<tr><th>Número de Celular</th><td>' . htmlspecialchars($deportista['NUMERO_CELULAR']) . '</td></tr>';
        echo '<tr><th>Género</th><td>' . htmlspecialchars($deportista['GENERO']) . '</td></tr>';
        echo '<tr><th>Número de Camiseta</th><td>' . htmlspecialchars($deportista['NUMERO_CAMISA'] ?? 'N/A') . '</td></tr>';
        echo '<tr><th>Altura</th><td>' . htmlspecialchars($deportista['ALTURA'] ?? 'N/A') . '</td></tr>';
        echo '<tr><th>Peso</th><td>' . htmlspecialchars($deportista['PESO'] ?? 'N/A') . '</td></tr>';
        echo '<tr><th>Fecha de Ingreso</th><td>' . htmlspecialchars($deportista['FECHA_INGRESO'] ?? 'N/A') . '</td></tr>';
        echo '</table>';
    } else {
        echo 'No se encontraron detalles para el deportista seleccionado.';
    }
}
?>  