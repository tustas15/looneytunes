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

// Obtener y sanitizar el ID del deportista
$id_deportista = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_deportista === 0) {
    die("No se especificó un deportista válido.");
}

try {
    // Obtener información del deportista
    $stmt = $conn->prepare("SELECT NOMBRE_DEPO, APELLIDO_DEPO FROM tab_deportistas WHERE ID_DEPORTISTA = :id");
    $stmt->bindParam(':id', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deportista) {
        die("Deportista no encontrado.");
    }

    // Obtener observaciones del deportista
    $stmt = $conn->prepare("
        SELECT tab_informes.*, tab_representantes.NOMBRE_REPRE, tab_representantes.APELLIDO_REPRE
        FROM tab_informes
        JOIN tab_representantes ON tab_informes.id_representante = tab_representantes.ID_REPRESENTANTE
        WHERE tab_informes.id_deportista = :id
        ORDER BY tab_informes.fecha_creacion DESC
    ");
    $stmt->bindParam(':id', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $observaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar la información: " . $e->getMessage());
}

// Incluir el encabezado
include './includes/header.php';
?>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-white">Observaciones de <?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']) ?></h1>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <?php if (!empty($observaciones)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Observación</th>
                                            <th>Representante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($observaciones as $observacion): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($observacion['fecha_creacion']))) ?></td>
                                                <td><?= nl2br(htmlspecialchars($observacion['informe'])) ?></td>
                                                <td><?= htmlspecialchars($observacion['NOMBRE_REPRE'] . ' ' . $observacion['APELLIDO_REPRE']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No hay observaciones registradas para este deportista.</p>
                        <?php endif; ?>

                        <!-- Botón para regresar -->
                        <div class="text-start mt-3">
                            <a href="javascript:history.back()" class="btn btn-secondary">Regresar</a>
                        </div>
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