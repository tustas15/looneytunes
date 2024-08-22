<?php
require_once('../../admin/configuracion/conexion.php');

try {
    $sql = "SELECT ID_DEPORTISTA, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) AS nombre_completo
            FROM tab_deportistas
            WHERE ID_DEPORTISTA NOT IN (SELECT ID_DEPORTISTA FROM tab_categoria_deportista)";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($deportistas);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
