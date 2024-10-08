<?php
session_start();
require_once('../../admin/configuracion/conexion.php');
// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    die("No autorizado");
}

$user_id = $_SESSION['user_id'];
// Procesar formulario de creación de categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    try {
        $nuevaCategoria = $_POST['nueva_categoria'];
        $sql = "INSERT INTO tab_categorias (CATEGORIA) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevaCategoria]);

        // Obtener la IP
        $ip = $_SERVER['REMOTE_ADDR'];

        // Registrar en tab_logs
        $sqlLog = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, 'nueva_categoria_producto_creado')";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([$user_id, "Nueva categoría: $nuevaCategoria", $ip]);

        $message = "Categoría creada exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al crear la categoría: " . $e->getMessage();
    }
    header("Location: categorias.php?message=" . urlencode($message));
    exit();
}

// Procesar formulario de eliminación de categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_categoria'])) {
    try {
        $idCategoria = $_POST['id_categoria'];
        
        // Obtener el nombre de la categoría antes de eliminarla
        $sqlSelect = "SELECT CATEGORIA FROM tab_categorias WHERE ID_CATEGORIA = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->execute([$idCategoria]);
        $categoria = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        $nombreCategoria = $categoria['CATEGORIA'];
        
        // Eliminar categoría
        $sql = "DELETE FROM tab_categorias WHERE ID_CATEGORIA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        
        // Obtener la IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Registrar en tab_logs
        $sqlLog = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, 'categoria_deportista_eliminado')";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([$user_id, "Categoría eliminada: $nombreCategoria", $ip]);

        $message = "Categoría eliminada exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al eliminar la categoría: " . $e->getMessage();
    }
    header("Location: categorias.php?message=" . urlencode($message));
    exit();
}

// Obtener la lista de categorías para el formulario de eliminación
$sql = "SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias";
$stmt = $conn->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logs
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Gestionar Categorías</title>
    <link href="../../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <!-- Crear Categoría -->
                        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11 mt-4">
                            <div class="card text-center h-100">
                                <div class="card-body px-5 pt-5 d-flex flex-column">
                                    <div>
                                        <div class="h3 text-primary">Crear Categoría</div>
                                        <p class="text-muted mb-4">Ingrese el nombre de la nueva categoría</p>
                                    </div>
                                    <div class="icons-org-create align-items-center mx-auto mt-auto">
                                        <i class="icon-users" data-feather="users"></i>
                                        <i class="icon-plus fas fa-plus"></i>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent px-5 py-4">
                                    <div class="small text-center">
                                        <form action="categorias.php" method="post">
                                            <!-- Form Group (nueva categoría)-->
                                            <div class="mb-3">
                                                <label class="small mb-1" for="nueva_categoria">Nombre de la Categoría</label>
                                                <input class="form-control" id="nueva_categoria" name="nueva_categoria" type="text" placeholder="Ingrese el nombre de la categoría" required />
                                            </div>
                                            <!-- Form Group (create category submit)-->
                                            <button class="btn btn-primary btn-block" type="submit" name="crear_categoria">Crear Categoría</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Eliminar Categoría -->
                        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11 mt-4">
                            <div class="card text-center h-100">
                                <div class="card-body px-5 pt-5 d-flex flex-column align-items-between">
                                    <div>
                                        <div class="h3 text-secondary">Eliminar Categoría</div>
                                        <p class="text-muted mb-4">Seleccione la categoría que desea eliminar</p>
                                    </div>
                                    <div class="icons-org-join align-items-center mx-auto">
                                        <i class="icon-user" data-feather="user"></i>
                                        <i class="icon-arrow fas fa-long-arrow-alt-right"></i>
                                        <i class="icon-users" data-feather="users"></i>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent px-5 py-4">
                                    <div class="small text-center">
                                        <div class="mb-3">
                                            <label class="small mb-1" for="id_categoria">Seleccionar Categoría</label>
                                            <select class="form-control" id="id_categoria" name="id_categoria" required>
                                                <option value="">Seleccione una categoría</option>
                                                <?php foreach ($categorias as $categoria) : ?>
                                                    <option value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" data-nombre="<?php echo htmlspecialchars($categoria['CATEGORIA']); ?>">
                                                        <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-danger btn-block" type="button" onclick="confirmDelete()">Eliminar Categoría</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de volver -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8 text-center">
                            <a class="btn btn-secondary" href="../indexAd.php">Volver</a> <!-- Cambia la ruta según sea necesario -->
                        </div>
                    </div>
                </div>
            </main>
            <!-- Modal de confirmación de eliminación -->
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Está seguro de que desea eliminar la categoría "<span id="categoryName"></span>"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form id="deleteCategoryForm" method="POST" action="categorias.php">
                                <input type="hidden" name="id_categoria" id="deleteCategoryId">
                                <button type="submit" name="eliminar_categoria" class="btn btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function confirmDelete() {
                    var select = document.getElementById('id_categoria');
                    var option = select.options[select.selectedIndex];
                    var categoryName = option.getAttribute('data-nombre');
                    var categoryId = option.value;

                    if (!categoryId) {
                        alert("Seleccione una categoría para eliminar.");
                        return;
                    }

                    document.getElementById('categoryName').textContent = categoryName;
                    document.getElementById('deleteCategoryId').value = categoryId;
                    var confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                    confirmDeleteModal.show();
                }
            </script>
        </div>
        <?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
    </div>