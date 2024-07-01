<?php
// Conexión a la base de datos
require_once('../Admin/configuracion/conexion.php');
// Inicio de sesión
session_start();
// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}
// Comprobamos si el usuario es entrenador
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}
// Comprobamos si el usuario entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
// Comprobamos si el usuario es entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];

try {
    // Consulta SQL
    $sql = "SELECT
            (SELECT COUNT(*) FROM tab_administradores) AS administradores,
            (SELECT COUNT(*) FROM tab_entrenadores) AS entrenadores,
            (SELECT COUNT(*) FROM tab_representantes) AS representantes,
            (SELECT COUNT(*) FROM tab_deportistas) AS deportistas";
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    // Ejecutar la consulta
    $stmt->execute();
    // Obtener los resultados como un array asociativo
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}

// Cargar deportistas desde la base de datos si la variable de sesión está vacía
try {
    $id_usuario = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM TAB_TEMP_DEPORTISTAS WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['deportistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar los deportistas: " . $e->getMessage();
    header('Location: error.php'); // Redirigir a una página de error
    exit;
}

// Manejar la eliminación de deportistas
if (isset($_POST['delete'])) {
    include './configuracion/eliminar_deportista.php'; // Mover la lógica de eliminación a un archivo separado
}
// Obtener la lista de deportistas de la sesión
$deportistas = $_SESSION['deportistas'] ?? [];

include './includes/header.php';
?>


<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bienvenido, Entrenador <?= $nombre ?></h1>
    <a href="../respaldo/downloadFile.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generar Respaldo</a>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Administradores Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Administradores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number" data-role="administradores"><?php echo $result['administradores']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entrenadores Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Entrenadores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number" data-role="entrenadores"><?php echo $result['entrenadores']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Representantes Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Representantes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number" data-role="representantes"><?php echo $result['representantes']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deportistas Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Deportistas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 number" data-role="deportistas"><?php echo $result['deportistas']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-running fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <section class="seccion">
        <h2 class="titulo">Lista de Deportistas</h2>
        <table class="tabla">
            <thead>
                <tr class="tri">
                    <th class="miniti">Nombre</th>
                    <th class="miniti">Apellido</th>
                    <th class="miniti">Cédula</th>
                    <th class="miniti">Detalles</th>
                    <th class="miniti">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deportistas as $deportista) : ?>
                    <tr class="tri">
                        <td class="depo"><?= htmlspecialchars($deportista['NOMBRE_DEPO'], ENT_QUOTES) ?></td>
                        <td class="depo"><?= htmlspecialchars($deportista['APELLIDO_DEPO'], ENT_QUOTES) ?></td>
                        <td class="depo"><?= htmlspecialchars($deportista['CEDULA_DEPO'], ENT_QUOTES) ?></td>
                        <td class="depo">
                            <form method="post" action="tab_detalles.php">
                                <input type="hidden" name="cedula_depo" value="<?= htmlspecialchars($deportista['CEDULA_DEPO'], ENT_QUOTES) ?>">
                                <input type="submit" name="detalles" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" value="Detalles">
                            </form>
                        </td>
                        <td class="depo">
                            <form method="post" action="">
                                <input type="hidden" name="cedula" value="<?= htmlspecialchars($deportista['CEDULA_DEPO'], ENT_QUOTES) ?>">
                                <input type="submit" name="delete" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" value="Eliminar">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</div>



<?php
include '../includes/footer.php';
?>