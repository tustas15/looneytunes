<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/tutorial/conexion/conexion.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Representantes</title>
</head>

<body>
    <?php
    try {
        // Consulta para obtener todos los representantes
        $stmt = $conn->prepare("SELECT u.ID_USUARIO, u.USUARIO
                                FROM tab_usuarios u
                                INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
                                INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO
                                WHERE t.ID_TIPO = 3");
        $stmt->execute();
        $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mostrar la lista de representantes
        echo "<h1>Lista de Representantes</h1>";
        echo "<ul>";
        foreach ($representantes as $representante) {
            echo "<li><a href='perfilrepresentante.php?ID_USUARIO=" . htmlspecialchars($representante['ID_USUARIO']) . "'>" . htmlspecialchars($representante['USUARIO']) . "</a></li>";
        }
        echo "</ul>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cierre de la conexión
    $conn = null;
    ?>
</body>

</html>