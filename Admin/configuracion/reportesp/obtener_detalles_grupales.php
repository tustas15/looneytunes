<?php
// obtener_detalles_grupales.php

session_start();
require_once('../conexion.php');

$categoria = $_POST['categoria'] ?? '';

// Sanitizar la entrada
$categoria = mysqli_real_escape_string($conexion, $categoria);

// Obtener el ID de la categoría
$sql_categoria_id = "SELECT id FROM tab_categorias WHERE nombre = '$categoria'";
$resultado_categoria = mysqli_query($conexion, $sql_categoria_id);
$categoria_id = mysqli_fetch_assoc($resultado_categoria)['id'];

// Consulta para obtener los pagos grupales al día
$sql = "
    SELECT 
        DATE_FORMAT(p.fecha, '%Y-%m') AS mes,
        SUM(p.monto) AS monto_total
    FROM pagos p
    JOIN tab_categoria_deportista cd ON p.id_deportista = cd.id_deportista
    WHERE cd.id_categoria = '$categoria_id'
      AND p.pagado = 1
    GROUP BY mes
    ORDER BY mes
";

$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'datos' => $datos]);
?>
