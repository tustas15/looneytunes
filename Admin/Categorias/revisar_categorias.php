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
    // Consulta SQL para obtener las categorías y el número de deportistas y entrenadores en cada una
    $sqlCategorias = "SELECT c.ID_CATEGORIA, c.CATEGORIA, 
                             COUNT(DISTINCT cd.ID_DEPORTISTA) AS num_deportistas,
                             (SELECT CONCAT(e.NOMBRE_ENTRE, ' ', e.APELLIDO_ENTRE) 
                              FROM tab_entrenador_categoria ec
                              JOIN tab_entrenadores e ON ec.ID_ENTRENADOR = e.ID_ENTRENADOR
                              WHERE ec.ID_CATEGORIA = c.ID_CATEGORIA
                              LIMIT 1) AS entrenador
                      FROM tab_categorias c
                      LEFT JOIN tab_categoria_deportista cd ON c.ID_CATEGORIA = cd.ID_CATEGORIA
                      GROUP BY c.ID_CATEGORIA, c.CATEGORIA";
    $stmtCategorias = $conn->prepare($sqlCategorias);
    $stmtCategorias->execute();
    $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener todos los deportistas
    $sqlTodosDeportistas = "SELECT ID_DEPORTISTA, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) AS nombre_completo FROM tab_deportistas";
    $stmtTodosDeportistas = $conn->prepare($sqlTodosDeportistas);
    $stmtTodosDeportistas->execute();
    $todosDeportistas = $stmtTodosDeportistas->fetchAll(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener todos los entrenadores
    $sqlTodosEntrenadores = "SELECT ID_ENTRENADOR, CONCAT(NOMBRE_ENTRE, ' ', APELLIDO_ENTRE) AS nombre_completo FROM tab_entrenadores";
    $stmtTodosEntrenadores = $conn->prepare($sqlTodosEntrenadores);
    $stmtTodosEntrenadores->execute();
    $todosEntrenadores = $stmtTodosEntrenadores->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
    exit();
}

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
                header("Location: revisar_categorias.php");
                exit();
            }

            if ($accion === 'eliminar_categoria') {
                $categoria_id = $_POST['categoria_id'];
                $sql = "DELETE FROM tab_categorias WHERE ID_CATEGORIA = :categoria_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':categoria_id', $categoria_id);
                $stmt->execute();
                header("Location: revisar_categorias.php");
                exit();
            }

            if ($accion === 'agregar_deportista') {
                $id_deportista = $_POST['id_deportista'];
                $id_categoria = $_POST['id_categoria'];

                // Insertar en la tabla intermedia
                $sql = "INSERT INTO tab_categoria_deportista (ID_DEPORTISTA, ID_CATEGORIA) VALUES (:id_deportista, :id_categoria)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_deportista', $id_deportista);
                $stmt->bindParam(':id_categoria', $id_categoria);
                $stmt->execute();

                header("Location: revisar_categorias.php");
                exit();
            }

            if ($accion === 'eliminar_deportista') {
                $deportista_id = $_POST['deportista_id'];

                // Eliminar de la tabla intermedia
                $sql = "DELETE FROM tab_categoria_deportista WHERE ID_DEPORTISTA = :deportista_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':deportista_id', $deportista_id);
                $stmt->execute();

                header("Location: revisar_categorias.php");
                exit();
            }

            if ($accion === 'agregar_entrenador') {
                $id_entrenador = $_POST['id_entrenador'];
                $id_categoria = $_POST['id_categoria'];

                // Insertar en la tabla intermedia
                $sql = "INSERT INTO tab_entrenador_categoria (ID_ENTRENADOR, ID_CATEGORIA) VALUES (:id_entrenador, :id_categoria)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_entrenador', $id_entrenador);
                $stmt->bindParam(':id_categoria', $id_categoria);
                $stmt->execute();

                header("Location: revisar_categorias.php");
                exit();
            }

            if ($accion === 'eliminar_entrenador') {
                $entrenador_id = $_POST['entrenador_id'];

                // Eliminar de la tabla intermedia
                $sql = "DELETE FROM tab_entrenador_categoria WHERE ID_ENTRENADOR = :entrenador_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':entrenador_id', $entrenador_id);
                $stmt->execute();

                header("Location: revisar_categorias.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-start">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#crearCategoriaModal">Agregar Categoría</button>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarCategoriaModal">Eliminar Categoría</button>
        </div>
    </div>

    <div class="row">
        <?php
        if (isset($categorias) && is_array($categorias) && !empty($categorias)) {
            foreach ($categorias as $categoria) : ?>
                <div class="col-xxl-4 col-xl-6 mb-4">
                    <div class="card card-header-actions h-100">
                        <div class="card-header">
                            <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                            <div class="dropdown no-caret">
                                <button class="btn btn-transparent-dark btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="text-gray-500" data-feather="more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end animated--fade-in-up" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addDeportistaModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="dropdown-item-icon">
                                            <i class="text-gray-500" data-feather="plus-circle"></i>
                                        </div>
                                        Agregar Deportista
                                    </a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteDeportistaModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="dropdown-item-icon">
                                            <i class="text-gray-500" data-feather="minus-circle"></i>
                                        </div>
                                        Eliminar Deportista
                                    </a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addEntrenadorModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="dropdown-item-icon">
                                            <i class="text-gray-500" data-feather="plus-circle"></i>
                                        </div>
                                        Agregar Entrenador
                                    </a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteEntrenadorModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="dropdown-item-icon">
                                            <i class="text-gray-500" data-feather="minus-circle"></i>
                                        </div>
                                        Eliminar Entrenador
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="small">
                                Entrenador encargado:
                                <span class=""><?php echo htmlspecialchars($categoria['entrenador']); ?> </span>
                            </h4>
                            <h4 class="small">
                                Número de Deportistas:
                                <span class="float-end fw-bold"><?php echo $categoria['num_deportistas']; ?> / 20</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar 
                                <?php
                                $percentage = ($categoria['num_deportistas'] / 20) * 100;
                                if ($percentage <= 25) {
                                    echo 'bg-danger'; // Rojo para <= 25%
                                } elseif ($percentage <= 50) {
                                    echo 'bg-warning'; // Amarillo para <= 50%
                                } elseif ($percentage <= 75) {
                                    echo 'bg-info'; // Azul para <= 75%
                                } else {
                                    echo 'bg-success'; // Verde para > 75%
                                }
                                ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $categoria['num_deportistas']; ?>" aria-valuemin="0" aria-valuemax="20">
                                </div>
                            </div>
                            <ul class="list-group">
                                <?php
                                $sqlDeportistasCategoria = "SELECT d.ID_DEPORTISTA, CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS nombre_completo
                                                            FROM tab_deportistas d
                                                            JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
                                                            WHERE cd.ID_CATEGORIA = :categoria_id";
                                $stmtDeportistasCategoria = $conn->prepare($sqlDeportistasCategoria);
                                $stmtDeportistasCategoria->bindParam(':categoria_id', $categoria['ID_CATEGORIA']);
                                $stmtDeportistasCategoria->execute();
                                $deportistasCategoria = $stmtDeportistasCategoria->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($deportistasCategoria)) {
                                    foreach ($deportistasCategoria as $deportista) {
                                        echo '<li class="list-group-item">' . htmlspecialchars($deportista['nombre_completo']) . '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item">No hay deportistas en esta categoría.</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php } else { ?>
            <p>No hay categorías disponibles.</p>
        <?php } ?>
    </div>
</div>

<!-- Modal para agregar categoría -->
<div class="modal fade" id="crearCategoriaModal" tabindex="-1" aria-labelledby="crearCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearCategoriaModalLabel">Agregar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="agregar_categoria">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar categoría -->
<div class="modal fade" id="eliminarCategoriaModal" tabindex="-1" aria-labelledby="eliminarCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eliminarCategoriaModalLabel">Eliminar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Seleccionar Categoría</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="eliminar_categoria">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar deportista -->
<div class="modal fade" id="addDeportistaModal" tabindex="-1" aria-labelledby="addDeportistaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDeportistaModalLabel">Agregar Deportista a Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_deportista" class="form-label">Deportista</label>
                        <select class="form-select" id="id_deportista" name="id_deportista" required>
                            <?php foreach ($todosDeportistas as $deportista) : ?>
                                <option value="<?php echo htmlspecialchars($deportista['ID_DEPORTISTA']); ?>">
                                    <?php echo htmlspecialchars($deportista['nombre_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="agregar_deportista">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar deportista -->
<div class="modal fade" id="deleteDeportistaModal" tabindex="-1" aria-labelledby="deleteDeportistaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDeportistaModalLabel">Eliminar Deportista de Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria_id_deportista" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria_id_deportista" name="categoria_id_deportista" required>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deportista_id" class="form-label">Deportista</label>
                        <select class="form-select" id="deportista_id" name="deportista_id" required>
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="eliminar_deportista">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar entrenador -->
<div class="modal fade" id="addEntrenadorModal" tabindex="-1" aria-labelledby="addEntrenadorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEntrenadorModalLabel">Agregar Entrenador a Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_categoria_entrenador" class="form-label">Categoría</label>
                        <select class="form-select" id="id_categoria_entrenador" name="id_categoria" required>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_entrenador" class="form-label">Entrenador</label>
                        <select class="form-select" id="id_entrenador" name="id_entrenador" required>
                            <?php foreach ($todosEntrenadores as $entrenador) : ?>
                                <option value="<?php echo htmlspecialchars($entrenador['ID_ENTRENADOR']); ?>">
                                    <?php echo htmlspecialchars($entrenador['nombre_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="agregar_entrenador">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar entrenador -->
<div class="modal fade" id="deleteEntrenadorModal" tabindex="-1" aria-labelledby="deleteEntrenadorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEntrenadorModalLabel">Eliminar Entrenador de Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria_id_entrenador" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria_id_entrenador" name="categoria_id_entrenador" required>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                    <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="entrenador_id" class="form-label">Entrenador</label>
                        <select class="form-select" id="entrenador_id" name="entrenador_id" required>
                        <?php foreach ($todosEntrenadores as $entrenador) : ?>
                                    <option value="<?php echo htmlspecialchars($entrenador['ID_ENTRENADOR']); ?>"><?php echo htmlspecialchars($entrenador['nombre_completo']); ?></option>
                                <?php endforeach; ?>
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="accion" value="eliminar_entrenador">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addDeportistaModal = document.getElementById('addDeportistaModal');
    const deleteDeportistaModal = document.getElementById('deleteDeportistaModal');
    const addEntrenadorModal = document.getElementById('addEntrenadorModal');
    const deleteEntrenadorModal = document.getElementById('deleteEntrenadorModal');

    addDeportistaModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoriaId = button.getAttribute('data-categoria-id');
        const selectCategoria = document.getElementById('id_categoria');
        selectCategoria.value = categoriaId;
    });

    deleteDeportistaModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoriaId = button.getAttribute('data-categoria-id');
        const selectCategoria = document.getElementById('categoria_id_deportista');
        selectCategoria.value = categoriaId;
        
        const selectDeportista = document.getElementById('deportista_id');
        fetch(`/get_deportistas_categoria.php?categoria_id=${categoriaId}`)
            .then(response => response.json())
            .then(deportistas => {
                selectDeportista.innerHTML = '';
                deportistas.forEach(deportista => {
                    const option = document.createElement('option');
                    option.value = deportista.ID_DEPORTISTA;
                    option.textContent = deportista.nombre_completo;
                    selectDeportista.appendChild(option);
                });
            });
    });

    addEntrenadorModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoriaId = button.getAttribute('data-categoria-id');
        const selectCategoria = document.getElementById('id_categoria_entrenador');
        selectCategoria.value = categoriaId;
    });

    deleteEntrenadorModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoriaId = button.getAttribute('data-categoria-id');
        const selectCategoria = document.getElementById('categoria_id_entrenador');
        selectCategoria.value = categoriaId;
        
        const selectEntrenador = document.getElementById('entrenador_id');
        fetch(`/get_entrenadores_categoria.php?categoria_id=${categoriaId}`)
            .then(response => response.json())
            .then(entrenadores => {
                selectEntrenador.innerHTML = '';
                entrenadores.forEach(entrenador => {
                    const option = document.createElement('option');
                    option.value = entrenador.ID_ENTRENADOR;
                    option.textContent = entrenador.nombre_completo;
                    selectEntrenador.appendChild(option);
                });
            });
    });
});
</script>

<?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>
