<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();


$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener el ID del usuario de la sesión
$id_usuario = $_SESSION['user_id'];

// Obtener y sanitizar el ID del deportista
$id_deportista = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : '';

// Consultar los detalles del deportista
try {
    $stmt = $conn->prepare("
        SELECT 
            tab_deportistas.NOMBRE_DEPO, 
            tab_deportistas.APELLIDO_DEPO, 
            tab_categorias.CATEGORIA 
        FROM 
            tab_deportistas
        INNER JOIN 
            tab_categorias ON tab_deportistas.ID_CATEGORIA = tab_categorias.ID_CATEGORIA
        WHERE 
            tab_deportistas.ID_DEPORTISTA = :id_deportista
    ");
    $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deportista) {
        die("Deportista no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al cargar los detalles del deportista: " . $e->getMessage());
}

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $altura = isset($_POST['altura']) ? filter_var($_POST['altura'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $peso = isset($_POST['peso']) ? filter_var($_POST['peso'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $numero_camisa = isset($_POST['numero_camisa']) ? filter_var($_POST['numero_camisa'], FILTER_SANITIZE_NUMBER_INT) : '';
    $fecha = date('Y-m-d'); // Obtener la fecha actual

    try {
        $stmt = $conn->prepare("
            INSERT INTO tab_detalles (ID_DEPORTISTA, ID_USUARIO, ALTURA, PESO, NUMERO_CAMISA, FECHA_INGRESO)
            VALUES (:id_deportista, :id_usuario, :altura, :peso, :numero_camisa, :fecha_ingreso)
        ");
        $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':altura', $altura, PDO::PARAM_STR);
        $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
        $stmt->bindParam(':numero_camisa', $numero_camisa, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_ingreso', $fecha, PDO::PARAM_STR);
        $stmt->execute();
        echo "Detalle ingresado exitosamente.";
    } catch (PDOException $e) {
        die("Error al ingresar el detalle: " . $e->getMessage());
    }
}

// Incluir el encabezado
include './includes/header.php';
?>

<main>
    <div class="container mt-5">
        <h2>Datos para <?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) ?> </h2>
        <p><?= htmlspecialchars($deportista['CATEGORIA']) ?></p>
        <form method="POST">
            <div class="mb-3">
                <label for="altura" class="form-label">Altura (cm)</label>
                <input type="number" step="0.01" class="form-control" id="altura" name="altura" required>
            </div>
            <div class="mb-3">
                <label for="peso" class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" class="form-control" id="peso" name="peso" required>
            </div>
            <div class="mb-3">
                <label for="numero_camisa" class="form-label">Número de Camisa</label>
                <input type="number" class="form-control" id="numero_camisa" name="numero_camisa" required>
            </div>
            <button type="submit" class="btn btn-primary">Ingresar Detalle</button>
        </form>
    </div>
</main>

<?php
// Incluir el pie de página
include './includes/footer.php';
?>
