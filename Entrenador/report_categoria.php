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

// Obtener y sanitizar el nombre de la categoría
$nombre_categoria = isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria'], ENT_QUOTES, 'UTF-8') : '';

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
                                        <th>Informes</th>
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
                                                <a href="#" class="btn btn-success btn-ingresar" data-id="<?= $jugador['ID_DEPORTISTA'] ?>">Ingresar</a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-info btn-informes" data-id="<?= $jugador['ID_DEPORTISTA'] ?>" data-representante="<?= $jugador['ID_REPRESENTANTE'] ?>">Informes</a>
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

    <!-- Modal para ingresar detalles -->
    <div class="modal fade" id="ingresarModal" tabindex="-1" aria-labelledby="ingresarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ingresarModalLabel">Ingresar Detalles de <?= htmlspecialchars($jugador['NOMBRE_DEPO'] . ' ' .  $jugador['APELLIDO_DEPO']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="detallesForm">
                        <input type="hidden" id="deportistaId" name="deportistaId">
                        <div class="mb-3">
                            <label for="numeroCamisa" class="form-label">Número de Camisa</label>
                            <input type="text" class="form-control" id="numeroCamisa" name="numeroCamisa" required>
                        </div>
                        <div class="mb-3">
                            <label for="altura" class="form-label">Altura</label>
                            <input type="text" class="form-control" id="altura" name="altura" required>
                        </div>
                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso</label>
                            <input type="text" class="form-control" id="peso" name="peso" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaIngreso" class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" id="fechaIngreso" name="fechaIngreso" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="guardarDetalles">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ingresar informes -->
    <div class="modal fade" id="informesModal" tabindex="-1" aria-labelledby="informesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="informesModalLabel">Ingresar Informe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="informeForm">
                        <input type="hidden" id="informeDeportistaId" name="deportistaId">
                        <input type="hidden" id="informeRepresentanteId" name="representanteId">
                        <div class="mb-3">
                            <label for="informe" class="form-label">Informe</label>
                            <textarea class="form-control" id="informe" name="informe" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="enviarInforme">Enviar Informe</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Incluir el pie de página
include './includes/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el clic en el botón "Informes"
    document.querySelectorAll('.btn-informes').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const deportistaId = this.getAttribute('data-id');
            const representanteId = this.getAttribute('data-representante');
            document.getElementById('informeDeportistaId').value = deportistaId;
            document.getElementById('informeRepresentanteId').value = representanteId;
            new bootstrap.Modal(document.getElementById('informesModal')).show();
        });
    });

    // Manejar el envío del formulario de informes
    document.getElementById('enviarInforme').addEventListener('click', function() {
        const form = document.getElementById('informeForm');
        const formData = new FormData(form);

        fetch('enviar_informe.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                document.getElementById('informesModal').querySelector('.btn-close').click();
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al enviar el informe.');
        });
    });
});
</script>