<?php
session_start();

if (!isset($_POST['cedula']) || !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Datos insuficientes para eliminar el deportista.";
    header('Location: index.php'); // Redirige a la página principal o a una página de error
    exit;
}

include '/xampp/htdocs/looneytunes/admin/configuracion/conexion.phpp';

$cedula_to_delete = $_POST['cedula'];
$id_usuario = $_SESSION['user_id'];

if (isset($conn)) {
    try {
        // Eliminar de la base de datos
        $sql = "DELETE FROM TAB_TEMP_DEPORTISTAS WHERE CEDULA_DEPO = :cedula AND ID_USUARIO = :id_usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cedula', $cedula_to_delete, PDO::PARAM_STR);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Registro del evento en tab_logs
        $evento = "Eliminación de deportista con cédula: $cedula_to_delete";
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP) VALUES (?, ?, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), ?)";
        $stmt_log = $conn->prepare($query);
        $stmt_log->execute([$id_usuario, $evento, $ip]);

        // Eliminar del array en la sesión
        foreach ($_SESSION['deportistas'] as $index => $deportista) {
            if ($deportista['CEDULA_DEPO'] == $cedula_to_delete) {
                unset($_SESSION['deportistas'][$index]);
                $_SESSION['deportistas'] = array_values($_SESSION['deportistas']); // Reindexar el array
                break;
            }
        }

        $_SESSION['success'] = "Deportista eliminado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar el deportista: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Conexión a la base de datos no establecida.";
}

header('Location: indexEntrenador.php'); // Redirige a la página principal o a la lista de deportistas
exit;
?>
