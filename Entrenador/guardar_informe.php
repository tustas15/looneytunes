<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Usuario no autorizado']));
}

$deportistaId = $_POST['deportistaId'];
$representanteId = $_POST['representanteId'];
$informe = $_POST['informe'];
$userId = $_SESSION['user_id'];

try {
    // Primero, obtén el ID_ENTRENADOR correspondiente al ID_USUARIO
    $stmt = $conn->prepare("SELECT ID_ENTRENADOR FROM tab_entrenadores WHERE ID_USUARIO = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $entrenador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entrenador) {
        throw new Exception("No se encontró un entrenador asociado a este usuario.");
    }

    $entrenadorId = $entrenador['ID_ENTRENADOR'];

    // Ahora inserta el informe usando el ID_ENTRENADOR correcto
    $stmt = $conn->prepare("INSERT INTO tab_informes (id_deportista, id_representante, id_entrenador, informe) VALUES (:deportistaId, :representanteId, :entrenadorId, :informe)");
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->bindParam(':representanteId', $representanteId, PDO::PARAM_INT);
    $stmt->bindParam(':entrenadorId', $entrenadorId, PDO::PARAM_INT);
    $stmt->bindParam(':informe', $informe, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Informe guardado con éxito']);
} catch (Exception $e) {
    error_log("Error al guardar el informe: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar el informe: ' . $e->getMessage()]);
}