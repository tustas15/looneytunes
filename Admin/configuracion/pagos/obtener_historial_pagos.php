<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Consulta SQL para obtener el historial de pagos
$sql = "SELECT * FROM pagos ORDER BY fecha_pago DESC";
$resultado = $conexion->query($sql);

// Generar la tabla HTML
echo '<table id="tabla-pagos" class="table table-striped">
        <thead>
            <tr>
                <th>Representante</th>
                <th>Deportista</th>
                <th>Tipo de Pago</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>';

while ($fila = $resultado->fetch_assoc()) {
    echo '<tr>
            <td>' . htmlspecialchars($fila['representante']) . '</td>
            <td>' . htmlspecialchars($fila['deportista']) . '</td>
            <td>' . htmlspecialchars($fila['tipo_pago']) . '</td>
            <td>' . htmlspecialchars($fila['monto']) . '</td>
            <td>' . htmlspecialchars($fila['fecha_pago']) . '</td>
            <td>' . htmlspecialchars($fila['motivo']) . '</td>
          </tr>';
}

echo '</tbody></table>';