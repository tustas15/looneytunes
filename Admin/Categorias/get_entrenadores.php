<?php
header('Content-Type: application/json');
require 'conexion.php';

$categoriaId = $_GET['categoria_id'];

$sql = "SELECT ID_ENTRENADOR, CONCAT(NOMBRE_ENT, ' ', APELLIDO_ENT) AS nombre_completo
        FROM tab_entrenadores
        WHERE ID_ENTRENADOR IN (SELECT ID_ENTRENADOR FROM tab_categoria_entrenador WHERE ID_CATEGORIA = :categoria_id)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':categoria_id', $categoriaId);
$stmt->execute();
$entrenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($entrenadores);
?>
