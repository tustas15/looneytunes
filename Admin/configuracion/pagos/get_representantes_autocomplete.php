<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['query'])) {
        $query = $_POST['query'];
        $stmt = $pdo->prepare("SELECT APELLIDO_REPRE, NOMBRE_REPRE FROM tab_representantes WHERE APELLIDO_REPRE LIKE :query LIMIT 10");
        $stmt->execute(['query' => $query . '%']);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result) {
            foreach ($result as $row) {
                echo '<li class="list-group-item list-group-item-action">' . $row['APELLIDO_REPRE'] . ' ' . $row['NOMBRE_REPRE'] . '</li>';
            }
        } else {
            echo '<li class="list-group-item">No se encontraron resultados</li>';
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>






$term = $_GET['term'];

try {
    $stmt = $conn->prepare("SELECT r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE, rd.ID_DEPORTISTA 
                            FROM tab_representantes r
                            JOIN tab_representantes_deportistas rd ON r.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
                            WHERE r.APELLIDO_REPRE LIKE :term
                            ORDER BY r.APELLIDO_REPRE, r.NOMBRE_REPRE");
    $term = "%$term%";
    $stmt->bindParam(':term', $term, PDO::PARAM_STR);
    $stmt->execute();
    $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($representantes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
