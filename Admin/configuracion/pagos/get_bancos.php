<?php
// Incluir el archivo de conexión
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    // Consulta para obtener bancos activos
    $stmt = $conn->prepare("SELECT ID_BANCO, NOMBRE FROM tab_bancos WHERE ESTADO = 'activo'");
    $stmt->execute();

    // Fetch all bancos
    $bancos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($bancos);
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje
    header('Content-Type: application/json');
    echo json_encode(array('error' => $e->getMessage()));
}
?>
