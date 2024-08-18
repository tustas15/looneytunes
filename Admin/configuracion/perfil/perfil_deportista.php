<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener el ID_DEPORTISTA desde la URL
if (!isset($_GET['ID_DEPORTISTA']) || empty($_GET['ID_DEPORTISTA'])) {
    echo "ID_DEPORTISTA no definido.";
    exit();
}

$id_deportista = intval($_GET['ID_DEPORTISTA']);

// Obtener datos del deportista y las categorías disponibles
try {
    // Obtener datos del deportista
    $stmt = $conn->prepare("
        SELECT d.ID_DEPORTISTA, d.ID_USUARIO, u.USUARIO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
               d.FECHA_NACIMIENTO, d.CEDULA_DEPO, d.NUMERO_CELULAR, d.GENERO,
               cd.ID_CATEGORIA, c.CATEGORIA AS NOMBRE_CATEGORIA, 
               r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CEDULA_REPRE, r.CELULAR_REPRE
        FROM tab_deportistas d
        INNER JOIN tab_usuarios u ON d.ID_USUARIO = u.ID_USUARIO
        LEFT JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
        LEFT JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        LEFT JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
        LEFT JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
        WHERE d.ID_DEPORTISTA = :id_deportista
    ");
    $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deportista) {
        echo "Deportista no encontrado.";
        exit();
    }

    // Obtener las categorías disponibles
    $categoriasStmt = $conn->query("SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias");
    $categorias = $categoriasStmt->fetchAll(PDO::FETCH_ASSOC);

    include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

    // Manejar el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_depo = $_POST['nombre_depo'];
        $apellido_depo = $_POST['apellido_depo'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $cedula_depo = $_POST['cedula_depo'];
        $numero_celular = $_POST['numero_celular'];
        $genero = $_POST['genero'];
        $id_categoria = $_POST['id_categoria']; // Nuevo campo para la categoría

        try {
            // Iniciar transacción
            $conn->beginTransaction();

            // Actualizar los datos del deportista
            $updateStmt = $conn->prepare("
                UPDATE tab_deportistas
                SET NOMBRE_DEPO = :nombre_depo,
                    APELLIDO_DEPO = :apellido_depo,
                    FECHA_NACIMIENTO = :fecha_nacimiento,
                    CEDULA_DEPO = :cedula_depo,
                    NUMERO_CELULAR = :numero_celular,
                    GENERO = :genero
                WHERE ID_DEPORTISTA = :id_deportista
            ");
            $updateStmt->bindParam(':nombre_depo', $nombre_depo, PDO::PARAM_STR);
            $updateStmt->bindParam(':apellido_depo', $apellido_depo, PDO::PARAM_STR);
            $updateStmt->bindParam(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR);
            $updateStmt->bindParam(':cedula_depo', $cedula_depo, PDO::PARAM_STR);
            $updateStmt->bindParam(':numero_celular', $numero_celular, PDO::PARAM_STR);
            $updateStmt->bindParam(':genero', $genero, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
            $updateStmt->execute();

            // Actualizar la categoría del deportista
            $updateCategoriaStmt = $conn->prepare("
                UPDATE tab_categoria_deportista
                SET ID_CATEGORIA = :id_categoria
                WHERE ID_DEPORTISTA = :id_deportista
            ");
            $updateCategoriaStmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $updateCategoriaStmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
            $updateCategoriaStmt->execute();

            // Registrar el evento en la tabla tab_logs
            $logStmt = $conn->prepare("
                INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO)
                VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)
            ");
            $logStmt->bindParam(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
            $logStmt->bindParam(':evento', $evento, PDO::PARAM_STR);
            $logStmt->bindParam(':hora_log', $hora_log, PDO::PARAM_STR);
            $logStmt->bindParam(':dia_log', $dia_log, PDO::PARAM_STR);
            $logStmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $logStmt->bindParam(':tipo_evento', $tipo_evento, PDO::PARAM_STR);

            // Datos del log
            $evento = "Actualización de información del deportista: " . $nombre_depo . " " . $apellido_depo;
            $hora_log = date("H:i:s");
            $dia_log = date("Y-m-d");
            $ip = $_SERVER['REMOTE_ADDR'];
            $tipo_evento = "actualizacion_perfil"; // Tipo de evento adecuado

            $logStmt->execute();

            // Confirmar la transacción
            $conn->commit();

            echo "<div class='alert alert-success' role='alert'>Perfil actualizado exitosamente.</div>";

            // Volver a obtener los datos del deportista actualizados
            $stmt->execute();
            $deportista = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $conn->rollBack();
            echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
        }
    }
?>

    <main>
        <div class="container-xl px-4 mt-4">
            <!-- Page title -->
            <div class="page-title">
                <h1>Perfil del Deportista</h1>
            </div>

            <!-- Profile Details -->
            <div class="card mb-4">
                <div class="card-header">Detalles del Deportista</div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_depo">Nombre</label>
                                    <input type="text" class="form-control" id="nombre_depo" name="nombre_depo" value="<?php echo htmlspecialchars($deportista['NOMBRE_DEPO']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="apellido_depo">Apellido</label>
                                    <input type="text" class="form-control" id="apellido_depo" name="apellido_depo" value="<?php echo htmlspecialchars($deportista['APELLIDO_DEPO']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($deportista['FECHA_NACIMIENTO']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cedula_depo">Cédula</label>
                                    <input type="text" class="form-control" id="cedula_depo" name="cedula_depo" value="<?php echo htmlspecialchars($deportista['CEDULA_DEPO']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_celular">Número Celular</label>
                                    <input type="text" class="form-control" id="numero_celular" name="numero_celular" value="<?php echo htmlspecialchars($deportista['NUMERO_CELULAR']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="genero">Género</label>
                                    <select class="form-control" id="genero" name="genero" required>
                                        <option value="Masculino" <?php echo ($deportista['GENERO'] === 'M') ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="Femenino" <?php echo ($deportista['GENERO'] === 'F') ? 'selected' : ''; ?>>Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_categoria">Categoría</label>
                                    <select class="form-control" id="id_categoria" name="id_categoria" required>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['ID_CATEGORIA']; ?>" <?php echo ($deportista['ID_CATEGORIA'] == $categoria['ID_CATEGORIA']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria['CATEGORIA']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php
    include '/xampp/htdocs/looneytunes/admin/includespro/footer.php';
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>