<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener el ID_USUARIO desde la URL
if (!isset($_GET['ID_USUARIO'])) {
    echo "ID_USUARIO no definido.";
    exit();
}

$id_usuario = intval($_GET['ID_USUARIO']);

try {
    // Obtener datos del entrenador y las categorías asociadas
    $stmt = $conn->prepare("
        SELECT e.ID_ENTRENADOR, e.ID_USUARIO, u.USUARIO, e.NOMBRE_ENTRE, e.APELLIDO_ENTRE, e.EXPERIENCIA_ENTRE, e.CELULAR_ENTRE, e.CORREO_ENTRE, GROUP_CONCAT(c.ID_CATEGORIA) AS categorias
        FROM tab_entrenadores e
        INNER JOIN tab_usuarios u ON e.ID_USUARIO = u.ID_USUARIO
        LEFT JOIN tab_entrenador_categoria ec ON e.ID_ENTRENADOR = ec.ID_ENTRENADOR
        LEFT JOIN tab_categorias c ON ec.ID_CATEGORIA = c.ID_CATEGORIA
        WHERE e.ID_USUARIO = :id_usuario
        GROUP BY e.ID_ENTRENADOR
    ");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $entrenador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entrenador) {
        echo "Entrenador no encontrado.";
        exit();
    }

    // Obtener datos del usuario de la sesión
    $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
    $tipo_usuario = $_SESSION['tipo_usuario'];
    $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

    // Obtener todas las categorías disponibles
    $categoriesStmt = $conn->query("SELECT * FROM tab_categorias");
    $categorias = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

    include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

    // Manejar el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_entre = $_POST['nombre_entre'];
        $apellido_entre = $_POST['apellido_entre'];
        $experiencia_entre = $_POST['experiencia_entre'];
        $celular_entre = $_POST['celular_entre'];
        $correo_entre = $_POST['correo_entre'];
        $categorias_seleccionadas = $_POST['categorias'] ?? [];

        try {
            $updateStmt = $conn->prepare("
                UPDATE tab_entrenadores
                SET NOMBRE_ENTRE = :nombre_entre,
                    APELLIDO_ENTRE = :apellido_entre,
                    EXPERIENCIA_ENTRE = :experiencia_entre,
                    CELULAR_ENTRE = :celular_entre,
                    CORREO_ENTRE = :correo_entre
                WHERE ID_USUARIO = :id_usuario
            ");
            $updateStmt->bindParam(':nombre_entre', $nombre_entre, PDO::PARAM_STR);
            $updateStmt->bindParam(':apellido_entre', $apellido_entre, PDO::PARAM_STR);
            $updateStmt->bindParam(':experiencia_entre', $experiencia_entre, PDO::PARAM_STR);
            $updateStmt->bindParam(':celular_entre', $celular_entre, PDO::PARAM_STR);
            $updateStmt->bindParam(':correo_entre', $correo_entre, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $updateStmt->execute();

            // Actualizar categorías
            $deleteCategoriesStmt = $conn->prepare("DELETE FROM tab_entrenador_categoria WHERE ID_ENTRENADOR = :id_entrenador");
            $deleteCategoriesStmt->bindParam(':id_entrenador', $entrenador['ID_ENTRENADOR'], PDO::PARAM_INT);
            $deleteCategoriesStmt->execute();

            $insertCategoryStmt = $conn->prepare("INSERT INTO tab_entrenador_categoria (ID_ENTRENADOR, ID_CATEGORIA) VALUES (:id_entrenador, :id_categoria)");

            foreach ($categorias_seleccionadas as $id_categoria) {
                $insertCategoryStmt->bindParam(':id_entrenador', $entrenador['ID_ENTRENADOR'], PDO::PARAM_INT);
                $insertCategoryStmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $insertCategoryStmt->execute();
            }

            echo "<div class='alert alert-success' role='alert'>Perfil actualizado exitosamente.</div>";

            // Volver a obtener los datos del entrenador actualizados
            $stmt->execute();
            $entrenador = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
        }
    }
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Perfil de Entrenador</h1>
        </div>

        <!-- Profile Details -->
        <div class="card mb-4">
            <div class="card-header">Detalles del Entrenador</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_entre">Nombre</label>
                                <input type="text" class="form-control" id="nombre_entre" name="nombre_entre" value="<?php echo htmlspecialchars($entrenador['NOMBRE_ENTRE']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_entre">Apellido</label>
                                <input type="text" class="form-control" id="apellido_entre" name="apellido_entre" value="<?php echo htmlspecialchars($entrenador['APELLIDO_ENTRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="experiencia_entre">Experiencia</label>
                                <textarea class="form-control" id="experiencia_entre" name="experiencia_entre" rows="3" required><?php echo htmlspecialchars($entrenador['EXPERIENCIA_ENTRE']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_entre">Celular</label>
                                <input type="text" class="form-control" id="celular_entre" name="celular_entre" value="<?php echo htmlspecialchars($entrenador['CELULAR_ENTRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="correo_entre">Correo</label>
                                <input type="email" class="form-control" id="correo_entre" name="correo_entre" value="<?php echo htmlspecialchars($entrenador['CORREO_ENTRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="categorias">Categorías</label>
                                <select multiple class="form-control" id="categorias" name="categorias[]">
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['ID_CATEGORIA']; ?>"
                                            <?php if (in_array($categoria['ID_CATEGORIA'], explode(',', $entrenador['categorias']))) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="/looneytunes/admin/configuracion/busqueda/indexentrenador.php" class="btn btn-primary">Volver a la Lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php
    include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php');
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cierre de la conexión
$conn = null;
?>
