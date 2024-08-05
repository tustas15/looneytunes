<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die(json_encode(['success' => false, 'message' => "Error de conexión a la base de datos."]));
}

// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => "Usuario no autenticado."]));
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => "Método no permitido."]));
}

// Obtener y sanitizar los datos del formulario
$deportistaId = filter_input(INPUT_POST, 'deportistaId', FILTER_SANITIZE_NUMBER_INT);
$representanteId = filter_input(INPUT_POST, 'representanteId', FILTER_SANITIZE_NUMBER_INT);
$informe = filter_input(INPUT_POST, 'informe', FILTER_SANITIZE_STRING);

// Validar los datos
if (!$deportistaId || !$representanteId || empty($informe)) {
    die(json_encode(['success' => false, 'message' => "Datos incompletos o inválidos."]));
}

try {
    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO tab_informes (ID_DEPORTISTA, ID_REPRESENTANTE, INFORME) VALUES (:deportistaId, :representanteId, :informe)");
    
    // Vincular los parámetros
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->bindParam(':representanteId', $representanteId, PDO::PARAM_INT);
    $stmt->bindParam(':informe', $informe, PDO::PARAM_STR);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Verificar si se insertó correctamente
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => "Informe enviado con éxito."]);
    } else {
        echo json_encode(['success' => false, 'message' => "No se pudo enviar el informe."]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Error al enviar el informe: " . $e->getMessage()]);
}