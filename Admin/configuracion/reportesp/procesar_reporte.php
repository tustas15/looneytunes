<?php
require_once('../conexion.php');

$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$tipo_reporte = $_POST['tipo_reporte'];
$opcion_especifica = $_POST['opcion_especifica'];
$tipo_informe = $_POST['tipo_informe'];

try {
    $query = "SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, 
                     c.CATEGORIA, p.METODO_PAGO, p.MONTO, p.FECHA_PAGO, p.MOTIVO
              FROM tab_pagos p
              JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
              JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
              JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
              JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
              WHERE p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";

    $params = [':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin];

    if ($opcion_especifica) {
        switch ($tipo_reporte) {
            case 'categoria':
                $query .= " AND c.ID_CATEGORIA = :opcion_id";
                break;
            case 'deportista':
                $query .= " AND d.ID_DEPORTISTA = :opcion_id";
                break;
            case 'representante':
                $query .= " AND r.ID_REPRESENTANTE = :opcion_id";
                break;
        }
        $params[':opcion_id'] = $opcion_especifica;
    }

    switch ($tipo_informe) {
        case 'individual_dia':
            $query .= " AND p.FECHA_PAGO = CURDATE()";
            break;
        case 'grupal_dia':
            $query .= " AND p.FECHA_PAGO = CURDATE() GROUP BY c.ID_CATEGORIA";
            break;
        case 'individual_nodia':
            $query .= " AND p.FECHA_PAGO < CURDATE()";
            break;
        case 'grupal_nodia':
            $query .= " AND p.FECHA_PAGO < CURDATE() GROUP BY c.ID_CATEGORIA";
            break;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generar tabla HTML con los resultados
    $html = "<table id='tabla-reporte' class='table table-striped'>
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Representante</th>
                        <th>Deportista</th>
                        <th>Categoría</th>
                        <th>Método de Pago</th>
                        <th>Monto</th>
                        <th>Fecha de Pago</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($results as $row) {
        $html .= "<tr>
                    <td>{$row['ID_PAGO']}</td>
                    <td>{$row['NOMBRE_REPRE']} {$row['APELLIDO_REPRE']}</td>
                    <td>{$row['NOMBRE_DEPO']}</td>
                    <td>{$row['CATEGORIA']}</td>
                    <td>{$row['METODO_PAGO']}</td>
                    <td>{$row['MONTO']}</td>
                    <td>{$row['FECHA_PAGO']}</td>
                    <td>{$row['MOTIVO']}</td>
                  </tr>";
    }

    $html .= "</tbody></table>";

    // Generar datos para el gráfico
    $chartData = [
        'labels' => array_column($results, 'CATEGORIA'),
        'data' => array_column($results, 'MONTO')
    ];

    echo $html . '<script>var chartData = ' . json_encode($chartData) . ';</script>';
} catch (Exception $e) {
    echo "Error al procesar el reporte: " . $e->getMessage();
}