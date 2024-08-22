<?php
session_start();
require_once('../admin/configuracion/conexion.php');

if (!isset($_SESSION['user_id'])) {
    die(); // Termina el script silenciosamente si el usuario no está autorizado
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

    //Selecionar el nombre del deportista con Observacion
    $stmt = $conn ->prepare("SELECT NOMBRE_DEPO from tab_deportistas where ID_DEPORTISTA = :deportistaId");
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->execute();
    $nom_depo = $stmt->fetch(PDO::FETCH_ASSOC);

    //LOGS
    $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Nueva Observacion de ".$nom_depo['NOMBRE_DEPO'];
    $tipo_evento = "nuevo_observacion_enviada";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP,TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$userId, $evento, $ip,$tipo_evento]);



    // Mensaje de éxito eliminado
} catch (Exception $e) {
    error_log("Error al guardar el informe: " . $e->getMessage());
    // Mensaje de error eliminado
}




?>
