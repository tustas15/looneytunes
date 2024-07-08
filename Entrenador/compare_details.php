<?php
set_time_limit(30);
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id_temp_deportista']) || empty($_POST['id_temp_deportista'])) {
        echo json_encode(['error' => 'ID de deportista no proporcionado']);
        exit;
    }

    $id_temp_deportista = $_POST['id_temp_deportista'];

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
        $grafica_datos = [];
        $html = '<h3>Historial de datos del deportista</h3>';
        $html .= '<table class="table table-bordered">';
        $html .= '<thead><tr>
                    <th>Fecha de Ingreso</th>
                    <th>Número de Camiseta</th>
                    <th>Altura</th>
                    <th>Peso</th>
                    <th>Acción</th>
                  </tr></thead><tbody>';
        
        foreach ($registros as $registro) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($registro['FECHA_INGRESO'] ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($registro['NUMERO_CAMISA'] ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($registro['ALTURA'] ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($registro['PESO'] ?? 'N/A') . '</td>';
            $html .= '<td><button class="btn btn-danger btn-sm delete-historical-detail" data-id="' . $registro['ID_DETALLE'] . '">Eliminar</button></td>';
            $html .= '</tr>';

            $altura = $registro['ALTURA'] ?? 0;
            $peso = $registro['PESO'] ?? 0;
            $fecha = $registro['FECHA_INGRESO'] ?? 'N/A';

            if ($altura > 0 && $peso > 0) {
                $imc = $peso / (($altura / 100) * ($altura / 100));
                $grafica_datos[] = [
                    'fecha' => $fecha,
                    'imc' => $imc
                ];
            }
        }
        $html .= '</tbody></table>';

        $html .= '<div id="chart-container"><canvas id="imcChart" width="400" height="200"></canvas></div>';

        $response = [
            'html' => $html,
            'grafica_datos' => $grafica_datos
        ];
    } else {
        $response = [
            'html' => 'No se encontraron detalles para el deportista seleccionado.',
            'grafica_datos' => []
        ];
    }
} else {
    $response = [
        'html' => 'Método de solicitud no válido.',
        'grafica_datos' => []
    ];
}

echo json_encode($response);
exit;
?>