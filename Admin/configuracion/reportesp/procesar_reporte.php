<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Obtener parámetros del formulario
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$categoria = $_POST['categoria'];

// Construir la consulta SQL
$sql = "SELECT p.*, d.NOMBRE_DEPO, d.APELLIDO_DEPO, c.CATEGORIA
        FROM tab_pagos p
        JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
        JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
        WHERE p.FECHA_PAGO BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin];

if (!empty($categoria)) {
    $sql .= " AND c.ID_CATEGORIA = ?";
    $params[] = $categoria;
}

$sql .= " ORDER BY p.FECHA_PAGO";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Inicializar arrays para los datos del gráfico
$labels = [];
$data = [];

// Generar la tabla HTML con los resultados
$html = '<table id="tabla-reporte" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Deportista</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Método de Pago</th>
                </tr>
            </thead>
            <tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
                <td>' . $row['FECHA_PAGO'] . '</td>
                <td>' . $row['NOMBRE_DEPO'] . ' ' . $row['APELLIDO_DEPO'] . '</td>
                <td>' . $row['CATEGORIA'] . '</td>
                <td>$' . number_format($row['MONTO'], 2) . '</td>
                <td>' . $row['METODO_PAGO'] . '</td>
              </tr>';
    
    // Agregar datos para el gráfico
    $mes = date('M Y', strtotime($row['FECHA_PAGO']));
    if (!isset($data[$mes])) {
        $data[$mes] = 0;
        $labels[] = $mes;
    }
    $data[$mes] += $row['MONTO'];
}

$html .= '</tbody></table>';

// Preparar datos para el gráfico
$chartData = [
    'labels' => array_values(array_unique($labels)),
    'datasets' => [
        [
            'label' => 'Monto total de pagos',
            'data' => array_values($data),
            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 1
        ]
    ]
];

// Devolver los resultados como JSON
echo json_encode([
    'html' => $html,
    'chartData' => $chartData
]);