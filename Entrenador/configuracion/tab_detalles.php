<?php
// Verificar si el usuario ha iniciado sesión
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit();
}

$cedula_depo = $_POST['cedula_depo'] ?? null;

// Obtener información del deportista
$stmt = $conn->prepare("SELECT ID_DEPORTISTA, NOMBRE_DEPO, APELLIDO_DEPO FROM tab_deportistas WHERE CEDULA_DEPO = :cedula_depo");
$stmt->bindParam(':cedula_depo', $cedula_depo, PDO::PARAM_STR);
$stmt->execute();
$deportista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deportista) {
    echo "No se encontró el deportista con la cédula proporcionada.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_detalles'])) {
    $numero_camisa = $_POST['numero_camisa'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $fecha_ingreso = $_POST['fecha_ingreso'];

    // Obtener el ID_USUARIO correspondiente al deportista
    $stmt = $conn->prepare("SELECT ID_USUARIO, ID_DEPORTISTA FROM tab_deportistas WHERE CEDULA_DEPO = :cedula_depo");
    $stmt->bindParam(':cedula_depo', $cedula_depo, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $id_usuario = $resultado['ID_USUARIO'];
        $id_deportista = $resultado['ID_DEPORTISTA'];

        // Preparar y ejecutar la consulta SQL INSERT
        $stmt = $conn->prepare("INSERT INTO tab_detalles (ID_USUARIO, ID_DEPORTISTA, NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO) VALUES (:id_usuario, :id_deportista, :numero_camisa, :altura, :peso, :fecha_ingreso)");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt->bindParam(':numero_camisa', $numero_camisa, PDO::PARAM_STR);
        $stmt->bindParam(':altura', $altura, PDO::PARAM_STR);
        $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Los detalles se han guardado correctamente.";
        } else {
            echo "Error al guardar los detalles: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "No se encontró el deportista con la cédula proporcionada.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Deportista</title>
    <link rel="stylesheet" href="detalles.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <script>
        window.onload = function() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById('fecha_ingreso').value = today;
        }
    </script>
</head>
<body>
    <a href="../entrenador/indexentrenador.php" class="regresar-btn">Regresar</a>
    <h1>Detalles del Deportista</h1>
    
    <h2>Detalles para: <?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) ?></h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="numero_camisa">Número de Camiseta:</label>
        <input type="text" id="numero_camisa" name="numero_camisa" required><br><br>

        <label for="altura">Altura:</label>
        <input type="text" id="altura" name="altura" required><br><br>

        <label for="peso">Peso:</label>
        <input type="text" id="peso" name="peso" required><br><br>

        <label for="fecha_ingreso">Fecha de Ingreso:</label>
        <input type="date" id="fecha_ingreso" name="fecha_ingreso" required><br><br>

        <input type="hidden" name="cedula_depo" value="<?= htmlspecialchars($cedula_depo) ?>">
        <input type="submit" name="guardar_detalles" value="Guardar Detalles">
    </form>
</body>
</html>