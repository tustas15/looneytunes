<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/tutorial/conexion/conexion.php');

try {
    // Consulta para obtener todos los usuarios tipo "entrenador"
    $stmt = $conn->prepare("
        SELECT u.ID_USUARIO, u.USUARIO
        FROM tab_usuarios u
        INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
        INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO
        WHERE t.ID_TIPO = :tipo_entrenador
    ");
    $tipo_entrenador = 2;  // ID_TIPO para entrenadores
    $stmt->bindParam(':tipo_entrenador', $tipo_entrenador, PDO::PARAM_INT);
    $stmt->execute();
    $entrenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mostrar la lista de entrenadores
    echo "<h1>Lista de Entrenadores</h1>";
    echo "<ul>";
    foreach ($entrenadores as $entrenador) {
        echo "<li><a href='../../../Public/profile.php?ID_USUARIO=" . htmlspecialchars($entrenador['ID_USUARIO']) . "'>" . htmlspecialchars($entrenador['USUARIO']) . "</a></li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cierre de la conexión
$conn = null;
?>
