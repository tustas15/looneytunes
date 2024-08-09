<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 3) {
    exit('No autorizado');
}

$id_deportista = isset($_GET['id_deportista']) ? intval($_GET['id_deportista']) : 0;

$stmt = $conn->prepare("SELECT * FROM tab_deportistas WHERE ID_DEPORTISTA = :id_deportista");
$stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
$stmt->execute();
$deportista = $stmt->fetch(PDO::FETCH_ASSOC);

if ($deportista) {
    echo "<h3>Perfil de " . htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) . "</h3>";
    echo "<p>Fecha de Nacimiento: " . htmlspecialchars($deportista['FECHA_NACIMIENTO']) . "</p>";
    // Agrega más detalles del deportista aquí
} else {
    echo "Deportista no encontrado";
}