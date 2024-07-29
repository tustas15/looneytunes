<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

date_default_timezone_set('America/Guayaquil'); // Ajusta a tu zona horaria

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Verifica si el tipo de usuario está definido
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

try {
    // Consulta SQL para obtener las categorías
    $sqlCategorias = "SELECT * FROM tab_categorias";
    $stmtCategorias = $conn->prepare($sqlCategorias);
    $stmtCategorias->execute();
    $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener los deportistas por categoría
    $sqlDeportistas = "SELECT d.ID_DEPORTISTA, d.ID_CATEGORIA, d.nombre_depo AS deportista_nombre, d.apellido_depo AS deportista_apellido, c.CATEGORIA AS categoria_nombre
                       FROM tab_deportistas d
                       JOIN tab_categorias c ON d.ID_CATEGORIA = c.ID_CATEGORIA";
    $stmtDeportistas = $conn->prepare($sqlDeportistas);
    $stmtDeportistas->execute();
    $deportistas = $stmtDeportistas->fetchAll(PDO::FETCH_ASSOC);

    // Organizar los deportistas por categoría
    $deportistasPorCategoria = [];
    foreach ($deportistas as $deportista) {
        $deportistasPorCategoria[$deportista['ID_CATEGORIA']][] = [
            'ID_DEPORTISTA' => $deportista['ID_DEPORTISTA'],
            'deportista_nombre' => $deportista['deportista_nombre'],
            'deportista_apellido' => $deportista['deportista_apellido']
        ];
    }
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}

// Cierra la conexión a la base de datos
$conn = null;

// Incluye el encabezado
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            $accion = $_POST['accion'];

            if ($accion === 'agregar_categoria') {
                $categoria = $_POST['categoria'];
                $sql = "INSERT INTO tab_categorias (CATEGORIA) VALUES (:categoria)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':categoria', $categoria);
                $stmt->execute();
                header("Location: categorias_deportistas.php");
                exit();

                // logs
            }

            if ($accion === 'eliminar_categoria') {
                $categoria_id = $_POST['categoria_id'];
                $sql = "DELETE FROM tab_categorias WHERE ID_CATEGORIA = :categoria_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':categoria_id', $categoria_id);
                $stmt->execute();
                header("Location: categorias_deportistas.php");
                exit();
                //logs
            }

            if ($accion === 'agregar_deportista') {
                $nombre_depo = $_POST['nombre_depo'];
                $apellido_depo = $_POST['apellido_depo'];
                $id_categoria = $_POST['id_categoria'];
                $sql = "INSERT INTO tab_deportistas (nombre_depo, apellido_depo, ID_CATEGORIA) VALUES (:nombre_depo, :apellido_depo, :id_categoria)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre_depo', $nombre_depo);
                $stmt->bindParam(':apellido_depo', $apellido_depo);
                $stmt->bindParam(':id_categoria', $id_categoria);
                $stmt->execute();
                header("Location: categorias_deportistas.php");
                exit();
                //logs
            }

            if ($accion === 'eliminar_deportista') {
                $deportista_id = $_POST['deportista_id'];
                $sql = "DELETE FROM tab_deportistas WHERE ID_DEPORTISTA = :deportista_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':deportista_id', $deportista_id);
                $stmt->execute();
                header("Location: categorias_deportistas.php");
                exit();
                //logs
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h1 class="mt-4 mb-4">Categorías y Deportistas</h1>

    <!-- Botones para abrir los modales -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Agregar Categoría</button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">Eliminar Categoría</button>
    </div>

    <!-- Modales -->
    <!-- Modal para Agregar Categoría -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Agregar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="categorias_deportistas.php" method="post">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="categoria" name="categoria" required>
                        </div>
                        <input type="hidden" name="accion" value="agregar_categoria">
                        <button type="submit" class="btn btn-primary">Agregar Categoría</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Categoría -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Eliminar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="categorias_deportistas.php" method="post">
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Seleccionar Categoría para Eliminar</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <?php foreach ($categorias as $categoria) : ?>
                                    <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="accion" value="eliminar_categoria">
                        <button type="submit" class="btn btn-danger">Eliminar Categoría</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mostrar Categorías y Deportistas -->
    <div class="row">
        <?php foreach ($categorias as $categoria) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                        <!-- Botones para abrir modales -->
                        <div class="btn-group float-end" role="group" aria-label="Acciones">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAthleteModal_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">Agregar Deportista</button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAthleteModal_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">Eliminar Deportista</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($deportistasPorCategoria[$categoria['ID_CATEGORIA']])) : ?>
                            <ul class="list-group">
                                <?php foreach ($deportistasPorCategoria[$categoria['ID_CATEGORIA']] as $deportista) : ?>
                                    <li class="list-group-item">
                                        <?php echo htmlspecialchars($deportista['deportista_nombre']) . ' ' . htmlspecialchars($deportista['deportista_apellido']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p>No hay deportistas en esta categoría.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal para Agregar Deportista -->
            <div class="modal fade" id="addAthleteModal_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" tabindex="-1" aria-labelledby="addAthleteModalLabel_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAthleteModalLabel_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">Agregar Deportista a <?php echo htmlspecialchars($categoria['CATEGORIA']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="categorias_deportistas.php" method="post">
                                <div class="mb-3">
                                    <label for="nombre_depo_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" class="form-label">Nombre del Deportista</label>
                                    <input type="text" class="form-control" id="nombre_depo_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" name="nombre_depo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido_depo_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" class="form-label">Apellido del Deportista</label>
                                    <input type="text" class="form-control" id="apellido_depo_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" name="apellido_depo" required>
                                </div>
                                <input type="hidden" name="id_categoria" value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                <input type="hidden" name="accion" value="agregar_deportista">
                                <button type="submit" class="btn btn-primary">Agregar Deportista</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para Eliminar Deportista -->
            <div class="modal fade" id="deleteAthleteModal_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" tabindex="-1" aria-labelledby="deleteAthleteModalLabel_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteAthleteModalLabel_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">Eliminar Deportista de <?php echo htmlspecialchars($categoria['CATEGORIA']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="categorias_deportistas.php" method="post">
                                <div class="mb-3">
                                    <label for="deportista_id_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" class="form-label">Seleccionar Deportista para Eliminar</label>
                                    <select class="form-select" id="deportista_id_<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" name="deportista_id" required>
                                        <?php if (isset($deportistasPorCategoria[$categoria['ID_CATEGORIA']])) : ?>
                                            <?php foreach ($deportistasPorCategoria[$categoria['ID_CATEGORIA']] as $deportista) : ?>
                                                <option value="<?php echo htmlspecialchars($deportista['ID_DEPORTISTA']); ?>">
                                                    <?php echo htmlspecialchars($deportista['deportista_nombre']) . ' ' . htmlspecialchars($deportista['deportista_apellido']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <option value="">No hay deportistas para eliminar</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <input type="hidden" name="accion" value="eliminar_deportista">
                                <button type="submit" class="btn btn-danger">Eliminar Deportista</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>
