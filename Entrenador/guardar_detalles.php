<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

require_once('../admin/configuracion/conexion.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deportistaId = filter_input(INPUT_POST, 'deportistaId', FILTER_SANITIZE_NUMBER_INT);
    $numeroCamisa = filter_input(INPUT_POST, 'numeroCamisa', FILTER_SANITIZE_STRING);
    $altura = filter_input(INPUT_POST, 'altura', FILTER_SANITIZE_STRING);
    $peso = filter_input(INPUT_POST, 'peso', FILTER_SANITIZE_STRING);
    $fechaIngreso = filter_input(INPUT_POST, 'fechaIngreso', FILTER_SANITIZE_STRING);

    try {
        $stmt = $conn->prepare("INSERT INTO tab_detalles (ID_USUARIO, ID_DEPORTISTA, NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO) VALUES (:userId, :deportistaId, :numeroCamisa, :altura, :peso, :fechaIngreso)");
        $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
        $stmt->bindParam(':numeroCamisa', $numeroCamisa, PDO::PARAM_STR);
        $stmt->bindParam(':altura', $altura, PDO::PARAM_STR);
        $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
        $stmt->bindParam(':fechaIngreso', $fechaIngreso, PDO::PARAM_STR);

        

        if ($stmt->execute()) {
            //Selecionar el nombre del deportista con Observacion
    $stmt = $conn ->prepare("SELECT NOMBRE_DEPO from tab_deportistas where ID_DEPORTISTA = :deportistaId");
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->execute();
    $nom_depo = $stmt->fetch(PDO::FETCH_ASSOC);

    $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Nuevo Dato de ".$nom_depo['NOMBRE_DEPO'];
    $tipo_evento = "nuevo_dato_creado";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP,TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip,$tipo_evento]);
            echo json_encode(['success' => true]);
            
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar los detalles']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}