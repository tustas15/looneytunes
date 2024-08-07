<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (isset($_GET['categoria_id'])) {
    $categoria_id = intval($_GET['categoria_id']);
    
    try {
        $sql = "SELECT d.ID_DEPORTISTA, CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS nombre_completo
                FROM tab_deportistas d
                JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
                WHERE cd.ID_CATEGORIA = :categoria_id";
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->execute();
        $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devuelve los deportistas en formato JSON
        echo json_encode($deportistas);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No se proporcionó ID de categoría']);
}
?>
