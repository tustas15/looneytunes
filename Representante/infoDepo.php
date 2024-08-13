<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];

// Obtener información del representante
$id_usuario = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM tab_representantes WHERE ID_USUARIO = ?");
$stmt->execute([$id_usuario]);
$representante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$representante) {
    die("Error: No se encontró información del representante.");
}

$nombre_representante = $representante['NOMBRE_REPRE'] . ' ' . $representante['APELLIDO_REPRE'];

// Obtener deportistas asociados al representante
$stmt = $conn->prepare("
    SELECT d.* 
    FROM tab_deportistas d
    INNER JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.id_deportista
    WHERE rd.id_representante = ?
");
$stmt->execute([$representante['ID_REPRESENTANTE']]);
$deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
include './Includes/header.php'
?>

<body class="nav-fixed">
    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
        <!-- Sidenav Toggle Button-->
        <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
        <!-- Navbar Brand-->
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="/looneytunes/index.php">Dashboard</a>
        <!-- Navbar Search Input-->
        <form class="form-inline me-auto d-none d-lg-block me-3">
            <div class="input-group input-group-joined input-group-solid">
                <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                <div class="input-group-text"><i data-feather="search"></i></div>
            </div>
        </form>
        <!-- Navbar Right -->
        <ul class="navbar-nav align-items-center ms-auto">
            <li class="nav-item">
            </li>
            <!-- Other navbar items -->
        </ul>
    </nav>

    <main>
        <div class="container mt-4">
            <h2>Deportistas Asociados</h2>

            <?php if (!empty($deportistas)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportistas as $deportista): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deportista['NOMBRE_DEPO']); ?></td>
                                <td><?php echo htmlspecialchars($deportista['APELLIDO_DEPO']); ?></td>
                                <td><?php echo htmlspecialchars($deportista['CEDULA_DEPO']); ?></td>
                                <td>
                                    <a href="loadInfoDeportista.php?id=<?php echo $deportista['ID_DEPORTISTA']; ?>" class="btn btn-primary">Información</a>
                                    <a href="loadRendimientoDeportista.php?id=<?php echo $deportista['ID_DEPORTISTA']; ?>" class="btn btn-primary">Rendimiento</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron deportistas asociados a este representante.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include './Includes/footer.php'; ?>
</body>

</html>
