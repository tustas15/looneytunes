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

// Obtener el ID_ADMINISTRADOR desde la URL
if (!isset($_GET['ID_ADMINISTRADOR'])) {
    echo "ID_ADMINISTRADOR no definido.";
    exit();
}

$id_administrador = intval($_GET['ID_ADMINISTRADOR']);

// Obtener datos del administrador
try {
    $stmt = $conn->prepare("
        SELECT ID_ADMINISTRADOR, NOMBRE_ADMIN, APELLIDO_ADMIN, CELULAR_ADMIN
        FROM tab_administradores
        WHERE ID_ADMINISTRADOR = :id_administrador
    ");
    $stmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
    $stmt->execute();
    $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$administrador) {
        echo "Administrador no encontrado.";
        exit();
    }

    $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
    $tipo_usuario = $_SESSION['tipo_usuario'];
    $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

    include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

    // Manejar el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_admin = $_POST['nombre_admin'];
        $apellido_admin = $_POST['apellido_admin'];
        $celular_admin = $_POST['celular_admin'];

        try {
            $updateStmt = $conn->prepare("
                UPDATE tab_administradores
                SET NOMBRE_ADMIN = :nombre_admin,
                    APELLIDO_ADMIN = :apellido_admin,
                    CELULAR_ADMIN = :celular_admin
                WHERE ID_ADMINISTRADOR = :id_administrador
            ");
            $updateStmt->bindParam(':nombre_admin', $nombre_admin, PDO::PARAM_STR);
            $updateStmt->bindParam(':apellido_admin', $apellido_admin, PDO::PARAM_STR);
            $updateStmt->bindParam(':celular_admin', $celular_admin, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
            $updateStmt->execute();

            echo "<div class='alert alert-success' role='alert'>Perfil actualizado exitosamente.</div>";

            // Volver a obtener los datos del administrador actualizados
            $stmt->execute();
            $administrador = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
        }
    }
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Perfil de Administrador</h1>
        </div>

        <!-- Profile Details -->
        <div class="card mb-4">
            <div class="card-header">Detalles del Administrador</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_admin">Nombre</label>
                                <input type="text" class="form-control" id="nombre_admin" name="nombre_admin" value="<?php echo htmlspecialchars($administrador['NOMBRE_ADMIN']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_admin">Apellido</label>
                                <input type="text" class="form-control" id="apellido_admin" name="apellido_admin" value="<?php echo htmlspecialchars($administrador['APELLIDO_ADMIN']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_admin">Celular</label>
                                <input type="text" class="form-control" id="celular_admin" name="celular_admin" value="<?php echo htmlspecialchars($administrador['CELULAR_ADMIN']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="/looneytunes/Admin/configuracion/busqueda/indexadministrador.php" class="btn btn-primary">Volver a la Lista</a>
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
