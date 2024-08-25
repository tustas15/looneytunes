<?php
// obtener_detalles_individuales.php

session_start();
require_once('../conexion.php');

$categoria = $_POST['categoria'] ?? '';

// Sanitizar la entrada
$categoria = mysqli_real_escape_string($conexion, $categoria);

// Obtener el ID de la categoría
$sql_categoria_id = "SELECT id FROM tab_categorias WHERE categoria = '$categoria'";
$resultado_categoria = mysqli_query($conexion, $sql_categoria_id);
$categoria_id = mysqli_fetch_assoc($resultado_categoria)['id'];

// Consulta para obtener los pagos individuales al día
$sql = "
    SELECT d.nombre AS deportista, p.monto, p.fecha, c.nombre AS categoria
    FROM tab_pagos p
    JOIN tab_categoria_deportista cd ON p.id_deportista = cd.id_deportista
    JOIN categorias c ON cd.id_categoria = c.id
    JOIN deportistas d ON p.id_deportista = d.id
    WHERE c.id = '$categoria_id'
      AND p.pagado = 1
    ORDER BY d.nombre, p.fecha
";

$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'datos' => $datos]);
?>
