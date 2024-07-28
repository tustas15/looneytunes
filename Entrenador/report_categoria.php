<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener y sanitizar el nombre de la categoría
$nombre_categoria = isset($_GET['categoria']) ? filter_var($_GET['categoria'], FILTER_SANITIZE_STRING) : '';

if (empty($nombre_categoria)) {
    die("No se especificó una categoría válida.");
}

try {
    $stmt = $conn->prepare("
        SELECT 
            tab_deportistas.*,
            tab_representantes.*
        FROM 
            tab_deportistas
        INNER JOIN 
            tab_categorias ON tab_deportistas.ID_CATEGORIA = tab_categorias.ID_CATEGORIA
        LEFT JOIN 
            tab_representantes_deportistas ON tab_deportistas.ID_DEPORTISTA = tab_representantes_deportistas.ID_DEPORTISTA
        LEFT JOIN 
            tab_representantes ON tab_representantes_deportistas.ID_REPRESENTANTE = tab_representantes.ID_REPRESENTANTE
        WHERE 
            tab_categorias.CATEGORIA = :categoria
    ");
    $stmt->bindParam(':categoria', $nombre_categoria, PDO::PARAM_STR);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar los jugadores: " . $e->getMessage());
}

// Incluir el encabezado
include './includes/header.php';
?>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-white">Deportistas de la categoría: <?= htmlspecialchars($nombre_categoria) ?></h1>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <?php if (!empty($jugadores)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Deportista</th>
                                        <th>Representante</th>
                                        <th>Datos</th>
                                        <th>Ingresar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jugadores as $jugador): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($jugador['NOMBRE_DEPO'] . ' ' .  $jugador['APELLIDO_DEPO']) ?></td>
                                            <td><?= htmlspecialchars($jugador['NOMBRE_REPRE'] . ' ' . $jugador['APELLIDO_REPRE']) ?></td>
                                            <td>
                                                <a href="detalle_deportista.php?id=<?= $jugador['ID_DEPORTISTA'] ?>" class="btn btn-primary">Datos</a>
                                            </td>
                                            <td>
                                                <a href="ingresar_detalle.php?id=<?= $jugador['ID_DEPORTISTA'] ?>" class="btn btn-success">Ingresar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No hay jugadores en esta categoría.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php
// Incluir el pie de página
include './includes/footer.php';
?>
