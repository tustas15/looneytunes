<?php
// Conexión a la base de datos
$host = '127.0.0.1';
$db = 'looneytunes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if (isset($_GET['id'])) {
    $representanteId = $_GET['id'];
    
    $stmt = $pdo->prepare('
        SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO 
        FROM tab_deportistas d
        INNER JOIN tab_representantes_deportistas rd ON d.ID_usuario = rd.ID_DEPORTISTA
        WHERE rd.ID_REPRESENTANTE = ?
    ');
    $stmt->execute([$representanteId]);
    $deportistas = $stmt->fetchAll();
    
    echo json_encode($deportistas);
}
?>
