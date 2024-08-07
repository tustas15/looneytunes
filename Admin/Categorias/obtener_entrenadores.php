<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (isset($_GET['categoria_id'])) {
    $categoria_id = intval($_GET['categoria_id']);
    $sqlEntrenadoresPorCategoria = "SELECT e.ID_ENTRENADOR, CONCAT(e.NOMBRE_ENTRE, ' ', e.APELLIDO_ENTRE) AS nombre_completo
                                    FROM tab_entrenadores e
                                    JOIN tab_entrenador_categoria ec ON e.ID_ENTRENADOR = ec.ID_ENTRENADOR
                                    WHERE ec.ID_CATEGORIA = :categoria_id";
    $stmtEntrenadoresPorCategoria = $conn->prepare($sqlEntrenadoresPorCategoria);
    $stmtEntrenadoresPorCategoria->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmtEntrenadoresPorCategoria->execute();
    $entrenadoresPorCategoria = $stmtEntrenadoresPorCategoria->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($entrenadoresPorCategoria);
}
?>
