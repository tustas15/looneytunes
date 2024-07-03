<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

// Obtener el ID_REPRESENTANTE desde la URL
if (!isset($_GET['ID_REPRESENTANTE'])) {
    echo "ID_REPRESENTANTE no definido.";
    exit();
}

$id_representante = intval($_GET['ID_REPRESENTANTE']);

// Obtener datos del representante
try {
    $stmt = $conn->prepare("
        SELECT ID_REPRESENTANTE, NOMBRE_REPRE, APELLIDO_REPRE, CEDULA_REPRE, CELULAR_REPRE, CORREO_REPRE, DIRECCION_REPRE
        FROM tab_representantes
        WHERE ID_REPRESENTANTE = :id_representante
    ");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $representante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$representante) {
        echo "Representante no encontrado.";
        exit();
    }

    $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
    $tipo_usuario = $_SESSION['tipo_usuario'];
    $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

    include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

    // Manejar el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_repre = $_POST['nombre_repre'];
        $apellido_repre = $_POST['apellido_repre'];
        $cedula_repre = $_POST['cedula_repre'];
        $celular_repre = $_POST['celular_repre'];
        $correo_repre = $_POST['correo_repre'];
        $direccion_repre = $_POST['direccion_repre'];

        try {
            $updateStmt = $conn->prepare("
                UPDATE tab_representantes
                SET NOMBRE_REPRE = :nombre_repre,
                    APELLIDO_REPRE = :apellido_repre,
                    CEDULA_REPRE = :cedula_repre,
                    CELULAR_REPRE = :celular_repre,
                    CORREO_REPRE = :correo_repre,
                    DIRECCION_REPRE = :direccion_repre
                WHERE ID_REPRESENTANTE = :id_representante
            ");
            $updateStmt->bindParam(':nombre_repre', $nombre_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':apellido_repre', $apellido_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':cedula_repre', $cedula_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':celular_repre', $celular_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':correo_repre', $correo_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':direccion_repre', $direccion_repre, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
            $updateStmt->execute();

            echo "<div class='alert alert-success' role='alert'>Perfil actualizado exitosamente.</div>";

            // Volver a obtener los datos del representante actualizados
            $stmt->execute();
            $representante = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
        }
    }

    // Obtener los deportistas asociados al representante
    $deportistasStmt = $conn->prepare("
        SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO
        FROM tab_deportistas d
        JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
        WHERE rd.ID_REPRESENTANTE = :id_representante
    ");
    $deportistasStmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $deportistasStmt->execute();
    $deportistas = $deportistasStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Perfil de Representante</h1>
        </div>

        <!-- Profile Details -->
        <div class="card mb-4">
            <div class="card-header">Detalles del Representante</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_repre">Nombre</label>
                                <input type="text" class="form-control" id="nombre_repre" name="nombre_repre" value="<?php echo htmlspecialchars($representante['NOMBRE_REPRE']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_repre">Apellido</label>
                                <input type="text" class="form-control" id="apellido_repre" name="apellido_repre" value="<?php echo htmlspecialchars($representante['APELLIDO_REPRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cedula_repre">Cédula</label>
                                <input type="text" class="form-control" id="cedula_repre" name="cedula_repre" value="<?php echo htmlspecialchars($representante['CEDULA_REPRE']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_repre">Celular</label>
                                <input type="text" class="form-control" id="celular_repre" name="celular_repre" value="<?php echo htmlspecialchars($representante['CELULAR_REPRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo_repre">Correo</label>
                                <input type="email" class="form-control" id="correo_repre" name="correo_repre" value="<?php echo htmlspecialchars($representante['CORREO_REPRE']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_repre">Dirección</label>
                                <input type="text" class="form-control" id="direccion_repre" name="direccion_repre" value="<?php echo htmlspecialchars($representante['DIRECCION_REPRE']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="/looneytunes/admin/configuracion/busqueda/indexrepresentante.php" class="btn btn-primary">Volver a la Lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Deportistas Asociados -->
        <div class="card mb-4">
            <div class="card-header">Deportistas Asociados</div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($deportistas as $deportista): ?>
                        <li class="list-group-item">
                            <a href="perfil_deportista.php?ID_DEPORTISTA=<?php echo $deportista['ID_DEPORTISTA']; ?>">
                                <?php echo htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php
    include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php');
} catch (PDOException $e) {
    echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
}

// Cierre de la conexión
$conn = null;
?>
