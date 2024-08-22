<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('../admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Comprobar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Obtener y sanitizar el ID del informe
$id_informe = isset($_POST['id_informe']) ? intval($_POST['id_informe']) : 0;
$deportistaId = isset($_POST['id_deportista']) ? intval($_POST['id_deportista']) : 0;




if ($id_informe === 0) {
    die("No se especificó un informe válido.");
}

try {
    // Preparar la consulta de eliminación
    $stmt = $conn->prepare("DELETE FROM tab_informes WHERE id_informe = :id_informe");
    $stmt->bindParam(':id_informe', $id_informe, PDO::PARAM_INT);
    $stmt->execute();


    //Selecionar el nombre del deportista con Observacion
    $stmt = $conn ->prepare("SELECT NOMBRE_DEPO from tab_deportistas where ID_DEPORTISTA = :deportistaId");
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->execute();
    $nom_depo = $stmt->fetch(PDO::FETCH_ASSOC);

    $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Observacion Eliminada de ".$nom_depo['NOMBRE_DEPO'];
    $tipo_evento = "nuevo_observacion_eliminada";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP,TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?,?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip,$tipo_evento]);


    // Redirigir de vuelta a la página anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} catch (PDOException $e) {
    die("Error al eliminar el informe: " . $e->getMessage());
}
?>
