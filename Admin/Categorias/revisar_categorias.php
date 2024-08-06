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
    // Consulta SQL para obtener las categorías y los nombres de deportistas y entrenadores en cada una
    $sqlCategorias = "SELECT c.ID_CATEGORIA, c.CATEGORIA, c.limite_deportistas,
                             GROUP_CONCAT(DISTINCT CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) SEPARATOR ', ') AS deportistas,
                             GROUP_CONCAT(DISTINCT CONCAT(e.NOMBRE_ENTRE, ' ', e.APELLIDO_ENTRE) SEPARATOR ', ') AS entrenadores,
                             COUNT(DISTINCT cd.ID_DEPORTISTA) AS num_deportistas,
                             COUNT(DISTINCT ec.ID_ENTRENADOR) AS num_entrenadores
                      FROM tab_categorias c
                      LEFT JOIN tab_categoria_deportista cd ON c.ID_CATEGORIA = cd.ID_CATEGORIA
                      LEFT JOIN tab_deportistas d ON cd.ID_DEPORTISTA = d.ID_DEPORTISTA
                      LEFT JOIN tab_entrenador_categoria ec ON c.ID_CATEGORIA = ec.ID_CATEGORIA
                      LEFT JOIN tab_entrenadores e ON ec.ID_ENTRENADOR = e.ID_ENTRENADOR
                      GROUP BY c.ID_CATEGORIA, c.CATEGORIA, c.limite_deportistas";
    $stmtCategorias = $conn->prepare($sqlCategorias);
    $stmtCategorias->execute();
    $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener todos los deportistas que no están en ninguna categoría
    $sqlTodosDeportistas = "SELECT ID_DEPORTISTA, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) AS nombre_completo
                            FROM tab_deportistas
                            WHERE ID_DEPORTISTA NOT IN (SELECT ID_DEPORTISTA FROM tab_categoria_deportista)";
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

// Procesar formularios
// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Depuración: Imprimir $_POST para verificar los datos enviados
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';

        if (isset($_POST['accion'])) {
            $accion = $_POST['accion'];

            switch ($accion) {
                case 'agregar_categoria':
                    $categoria = $_POST['categoria'];
                    $sql = "INSERT INTO tab_categorias (CATEGORIA) VALUES (:categoria)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':categoria', $categoria);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'eliminar_categoria':
                    $categoria_id = $_POST['categoria_id'];
                    $sql = "DELETE FROM tab_categorias WHERE ID_CATEGORIA = :categoria_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':categoria_id', $categoria_id);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'agregar_deportista':
                    $id_deportista = $_POST['id_deportista'];
                    $id_categoria = $_POST['id_categoria'];
                    $sql = "INSERT INTO tab_categoria_deportista (ID_DEPORTISTA, ID_CATEGORIA) VALUES (:id_deportista, :id_categoria)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_deportista', $id_deportista);
                    $stmt->bindParam(':id_categoria', $id_categoria);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'eliminar_deportista':
                    $deportista_id = $_POST['deportista_id'];
                    $sql = "DELETE FROM tab_categoria_deportista WHERE ID_DEPORTISTA = :deportista_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':deportista_id', $deportista_id);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'agregar_entrenador':
                    $id_entrenador = $_POST['id_entrenador'];
                    $id_categoria = $_POST['id_categoria'];
                    $sql = "INSERT INTO tab_entrenador_categoria (ID_ENTRENADOR, ID_CATEGORIA) VALUES (:id_entrenador, :id_categoria)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_entrenador', $id_entrenador);
                    $stmt->bindParam(':id_categoria', $id_categoria);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'eliminar_entrenador':
                    $entrenador_id = $_POST['entrenador_id'];
                    $sql = "DELETE FROM tab_entrenador_categoria WHERE ID_ENTRENADOR = :entrenador_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':entrenador_id', $entrenador_id);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'reasignar_deportista':
                    $id_deportista = $_POST['id_deportista'];
                    $nueva_categoria = $_POST['nueva_categoria'];
                    $sql = "UPDATE tab_categoria_deportista SET ID_CATEGORIA = :nueva_categoria WHERE ID_DEPORTISTA = :id_deportista";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_deportista', $id_deportista);
                    $stmt->bindParam(':nueva_categoria', $nueva_categoria);
                    $stmt->execute();
                    header("Location: revisar_categorias.php");
                    exit();

                case 'modificar_limite':
                    $categoria_id = $_POST['categoria_limite'];
                    $nuevo_limite = $_POST['nuevo_limite'];

                    // Actualizar el límite de deportistas en la base de datos
                    $sqlActualizarLimite = "UPDATE tab_categorias SET limite_deportistas = :nuevo_limite WHERE ID_CATEGORIA = :categoria_id";
                    $stmtActualizarLimite = $conn->prepare($sqlActualizarLimite);
                    $stmtActualizarLimite->bindParam(':nuevo_limite', $nuevo_limite, PDO::PARAM_INT);
                    $stmtActualizarLimite->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
                    $stmtActualizarLimite->execute();

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();

                default:
                    echo "Acción no válida.";
                    exit();
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet" />
    <link href="/looneytunes/Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>

<body class="nav-fixed">

    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
        <!-- Sidenav Toggle Button-->
        <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
        <!-- Navbar Brand-->
        <!-- * * Tip * * You can use text or an image for your navbar brand.-->
        <!-- * * * * * * When using an image, we recommend the SVG format.-->
        <!-- * * * * * * Dimensions: Maximum height: 32px, maximum width: 240px-->
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="/looneytunes/admin/indexad.php">Dashboard</a>
        <!-- Navbar Search Input-->
        <!-- * * Note: * * Visible only on and above the lg breakpoint-->
        <!--
        <form class="form-inline me-auto d-none d-lg-block me-3">
            <div class="input-group input-group-joined input-group-solid">
                <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                <div class="input-group-text"><i data-feather="search"></i></div>
            </div>
        </form>
        -->
        <!-- Navbar Items-->
        <ul class="navbar-nav align-items-center ms-auto">
            <!-- Documentation Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="fw-500">Base de datos</div>
                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0 me-sm-n15 me-lg-0 o-hidden animated--fade-in-up" aria-labelledby="navbarDropdownDocs">
                    <a class="dropdown-item py-3" href="/looneytunes/Admin/configuracion/respaldo/downloadFile.php">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="database"></i></div>
                        <div>
                            <div class="small text-gray-500">Generar Respaldo</div>
                            Haz click y descarga el respaldo del sistema
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="javascript:void(0);" data-toggle="modal" data-target="#uploadBackupModal">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i class="fas fa-upload"></i></div>
                        <div>
                            <div class="small text-gray-500">Subir Respaldo</div>
                            Haz click para subir el respaldo del sistema
                        </div>
                    </a>
                </div>
            </li>

            <!-- Navbar Search Dropdown-->
            <!-- * * Note: * * Visible only below the lg breakpoint-->
            <li class="nav-item dropdown no-caret me-3 d-lg-none">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="searchDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="search"></i></a>
                <!-- Dropdown - Search-->

            </li>
            <!-- Alerts Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownAlerts" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="bell"></i></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownAlerts">
                    <h6 class="dropdown-header dropdown-notifications-header">
                        <i class="me-2" data-feather="bell"></i>
                        Alerts Center
                    </h6>
                    <!-- Example Alert 1-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <div class="dropdown-notifications-item-icon bg-warning"><i data-feather="activity"></i></div>
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-details">December 29, 2021</div>
                            <div class="dropdown-notifications-item-content-text">This is an alert message. It's nothing serious, but it requires your attention.</div>
                        </div>
                    </a>
                    <!-- Example Alert 2-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <div class="dropdown-notifications-item-icon bg-info"><i data-feather="bar-chart"></i></div>
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-details">December 22, 2021</div>
                            <div class="dropdown-notifications-item-content-text">A new monthly report is ready. Click here to view!</div>
                        </div>
                    </a>
                    <!-- Example Alert 3-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <div class="dropdown-notifications-item-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-details">December 8, 2021</div>
                            <div class="dropdown-notifications-item-content-text">Critical system failure, systems shutting down.</div>
                        </div>
                    </a>
                    <!-- Example Alert 4-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <div class="dropdown-notifications-item-icon bg-success"><i data-feather="user-plus"></i></div>
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-details">December 2, 2021</div>
                            <div class="dropdown-notifications-item-content-text">New user request. Woody has requested access to the organization.</div>
                        </div>
                    </a>
                    <a class="dropdown-item dropdown-notifications-footer" href="#!">View All Alerts</a>
                </div>
            </li>
            <!-- Messages Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownMessages" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="mail"></i></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownMessages">
                    <h6 class="dropdown-header dropdown-notifications-header">
                        <i class="me-2" data-feather="mail"></i>
                        Message Center
                    </h6>
                    <!-- Example Message 1  -->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="/looneytunes//Assets/img/illustrations/profiles/profile-2.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Thomas Wilcox · 58m</div>
                        </div>
                    </a>
                    <!-- Example Message 2-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="/looneytunes/Assets/img/illustrations/profiles/profile-3.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Emily Fowler · 2d</div>
                        </div>
                    </a>
                    <!-- Example Message 3-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="/looneytunes/Assets/img/illustrations/profiles/profile-4.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Marshall Rosencrantz · 3d</div>
                        </div>
                    </a>
                    <!-- Example Message 4-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="/looneytunes/Assets/img/illustrations/profiles/profile-5.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Colby Newton · 3d</div>
                        </div>
                    </a>
                    <!-- Footer Link-->
                    <a class="dropdown-item dropdown-notifications-footer" href="#!">Read All Messages</a>
                </div>
            </li>
            <!-- User Dropdown-->
            <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="img-fluid" src="/looneytunes/Assets/img/illustrations/profiles/profile-1.png" /></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                    <h6 class="dropdown-header d-flex align-items-center">
                        <img class="dropdown-user-img" src="/looneytunes/Assets/img/illustrations/profiles/profile-1.png" />
                        <div class="dropdown-user-details">
                            <div class="dropdown-user-details-name"><?= $nombre ?></div>
                        </div>
                    </h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/looneytunes/Public/profile.php">
                        <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                        Cuenta
                    </a>
                    <a class="dropdown-item" href="/looneytunes/Public/logout.php">
                        <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sidenav shadow-right sidenav-dark">
                <div class="sidenav-menu">
                    <div class="nav accordion" id="accordionSidenav">
                        <!-- Sidenav Menu Heading (Account)-->
                        <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                        <div class="sidenav-menu-heading d-sm-none">Account</div>
                        <!-- Sidenav Link (Alerts)-->
                        <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                        <a class="nav-link d-sm-none" href="#!">
                            <div class="nav-link-icon"><i data-feather="bell"></i></div>
                            Alerts
                            <span class="badge bg-warning-soft text-warning ms-auto">4 New!</span>
                        </a>
                        <!-- Sidenav Link (Messages)-->
                        <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                        <a class="nav-link d-sm-none" href="#!">
                            <div class="nav-link-icon"><i data-feather="mail"></i></div>
                            Messages
                            <span class="badge bg-success-soft text-success ms-auto">2 New!</span>
                        </a>
                        <!-- Sidenav Menu Heading (Core)-->
                        <div class="sidenav-menu-heading">Core</div>
                        <!-- Sidenav Accordion (Dashboard)-->
                        <a class="nav-link" href=" /looneytunes/index.php">
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Dashboard
                        </a>

                        <!-- Sidenav Heading (App Views)-->
                        <div class="sidenav-menu-heading">Interfaz</div>

                        <!-- Sidenav Accordion (Pages)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="nav-link-icon"><i class="fas fa-fw fa-cog"></i></div>
                            Componentes
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" data-bs-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesMenu">
                                <!-- Nested Sidenav Accordion (Pages -> Account)-->
                                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAccount" aria-expanded="false" aria-controls="pagesCollapseAccount">
                                    Perfiles
                                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAccount" data-bs-parent="#accordionSidenavPagesMenu">
                                    <nav class="sidenav-menu-nested nav">
                                        <a class="nav-link" href="/looneytunes/admin/configuracion/busqueda/indexadministrador.php">Administradores</a>
                                        <a class="nav-link" href="/looneytunes/admin/configuracion/busqueda/indexentrenador.php">Entrenadores</a>
                                        <a class="nav-link" href="/looneytunes/admin/configuracion/busqueda/indexrepresentante.php">Representantes</a>
                                        <a class="nav-link" href="/looneytunes/admin/configuracion/busqueda/indexdeportista.php">Deportistas</a>
                                    </nav>
                                </div>
                                <a class="nav-link" href="/looneytunes/admin/configuracion/pagos/pagos.php">
                                    Pago
                                </a>
                                <a class="nav-link" href="/looneytunes/admin/configuracion/reportesp/reportes_pagos.php">
                                    Reportes de pagos
                                </a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlows" aria-expanded="false" aria-controls="collapseFlows">
                            <div class="nav-link-icon"><i class="fas fa-fw fa-wrench"></i></div>
                            Utilidades
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseFlows" data-bs-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavFlowsMenu">
                                <!-- Nested Sidenav Accordion (Flows -> Crear Usuarios)-->
                                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#flowsCollapseCrearUsuarios" aria-expanded="false" aria-controls="flowsCollapseCrearUsuarios">
                                    Crear Usuarios
                                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="flowsCollapseCrearUsuarios" data-bs-parent="#accordionSidenavFlowsMenu">
                                    <nav class="sidenav-menu-nested nav">
                                        <a class="nav-link" href="/looneytunes/admin/usuarios/crear_usuarios/cradmin.php">Administrador</a>
                                        <a class="nav-link" href="/looneytunes/admin/usuarios/crear_usuarios/crentrenador.php">Entrenador</a>
                                        <a class="nav-link" href="/looneytunes/admin/usuarios/crear_usuarios/crrepresentante.php">Representante</a>
                                        <a class="nav-link" href="/looneytunes/admin/usuarios/crear_usuarios/crdeportista.php">Deportista</a>
                                    </nav>
                                </div>
                                <!-- Añadido Categorías-->
                                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#flowsCollapseCategorias" aria-expanded="false" aria-controls="flowsCollapseCategorias">
                                    Categorías
                                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="flowsCollapseCategorias" data-bs-parent="#accordionSidenavFlowsMenu">
                                    <nav class="sidenav-menu-nested nav">
                                        <a class="nav-link" href="/looneytunes/admin/categorias/categorias.php">Crear/Eliminar Categorías</a>
                                        <a class="nav-link" href="/looneytunes/admin/categorias/revisar_categorias.php">Lista Categorías</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>

                        <!-- Sidenav Heading (UI Toolkit)-->
                        <div class="sidenav-menu-heading">INVENTARIO</div>

                        <!-- Sidenav Accordion (Categorías)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseLayoutsCategories" aria-expanded="false" aria-controls="collapseLayoutsCategories">
                            <div class="nav-link-icon"><i data-feather="layout"></i></div>
                            Categorías
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayoutsCategories" data-bs-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavLayoutCategories">
                                <!-- Nested Sidenav Accordion (Categorías -> Nueva)-->
                                <a class="nav-link collapsed" href="/looneytunes/admin/inventario/index.php?vista=category_new">
                                    Nueva
                                </a>
                                <!-- Nested Sidenav Accordion (Categorías -> Lista)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/categoria/category_list.php">
                                    Lista
                                </a>
                                <!-- Nested Sidenav Accordion (Categorías -> Buscar)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/categoria/category_search.php">
                                    Buscar
                                </a>
                            </nav>
                        </div>

                        <!-- Sidenav Accordion (Productos)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseLayoutsProducts" aria-expanded="false" aria-controls="collapseLayoutsProducts">
                            <div class="nav-link-icon"><i data-feather="layout"></i></div>
                            Productos
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayoutsProducts" data-bs-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavLayoutProducts">
                                <!-- Nested Sidenav Accordion (Productos -> Nuevo)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=product_new">
                                    Nuevo
                                </a>
                                <!-- Nested Sidenav Accordion (Productos -> Lista)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=product_list">
                                    Lista
                                </a>
                                <!-- Nested Sidenav Accordion (Productos -> Por categorías)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=product_category">
                                    Por categorías
                                </a>
                                <!-- Nested Sidenav Accordion (Productos -> Buscar)-->
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=product_search">
                                    Buscar
                                </a>
                            </nav>
                        </div>


                    </div>
                </div>

                <!-- Sidenav Footer -->
                <div class="sidenav-footer">
                    <div class="sidenav-footer-content">
                        <div class="sidenav-footer-subtitle">Logged in as:</div>
                        <div class="sidenav-footer-title"><?php echo htmlspecialchars($nombre); ?></div>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
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
                                                        <i class="text-gray-500" data-feather="edit"></i>
                                                    </div>
                                                    Añadir Deportista
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addEntrenadorModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i class="text-gray-500" data-feather="edit"></i>
                                                    </div>
                                                    Añadir Entrenador
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i class="text-gray-500" data-feather="trash-2"></i>
                                                    </div>
                                                    Eliminar
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEstablecerLimite<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i class="text-gray-500" data-feather="trash-2"></i>
                                                    </div>
                                                    Establecer límite
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalReasignar<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" data-categoria-id="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i class="text-gray-500" data-feather="trash-2"></i>
                                                    </div>
                                                    Cambiar deportista
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Límite de Deportistas:</strong> <?php echo htmlspecialchars($categoria['limite_deportistas']); ?></p>
                                        <p><strong>Deportistas:</strong> <?php echo htmlspecialchars($categoria['deportistas']); ?></p>
                                        <p><strong>Entrenadores:</strong> <?php echo htmlspecialchars($categoria['entrenadores']); ?></p>
                                        <p><strong>Total Deportistas:</strong> <?php echo htmlspecialchars($categoria['num_deportistas']); ?></p>
                                        <p><strong>Total Entrenadores:</strong> <?php echo htmlspecialchars($categoria['num_entrenadores']); ?></p>

                                        <?php
                                        $numDeportistas = $categoria['num_deportistas'];
                                        $limiteDeportistas = $categoria['limite_deportistas'];
                                        $porcentaje = ($limiteDeportistas > 0) ? ($numDeportistas / $limiteDeportistas) * 100 : 0;
                                        ?>

                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo htmlspecialchars($porcentaje); ?>%;" aria-valuenow="<?php echo htmlspecialchars($porcentaje); ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?php echo htmlspecialchars(number_format($porcentaje, 2)); ?>%
                                            </div>
                                        </div>

                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                            <input type="hidden" name="categoria_limite" value="<?php echo $categoria['ID_CATEGORIA']; ?>">
                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php endforeach;
                    } else {
                        echo "No se encontraron categorías.";
                    }
                    ?>
                </div>

                <!-- Modal para reasignar deportista -->
                <?php foreach ($categorias as $categoria) : ?>
                    <div class="modal fade" id="modalReasignar<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" tabindex="-1" aria-labelledby="modalReasignarLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalReasignarLabel">Reasignar Deportista</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="accion" value="reasignar_deportista">
                                        <input type="hidden" name="categoria_id" value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="mb-3">
                                            <label for="deportista" class="form-label">Selecciona Deportista</label>
                                            <select name="id_deportista" id="deportista" class="form-select" required>
                                                <?php foreach ($todosDeportistas as $deportista) : ?>
                                                    <option value="<?php echo htmlspecialchars($deportista['ID_DEPORTISTA']); ?>">
                                                        <?php echo htmlspecialchars($deportista['nombre_completo']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nueva_categoria" class="form-label">Nueva Categoría</label>
                                            <select name="nueva_categoria" id="nueva_categoria" class="form-select" required>
                                                <?php foreach ($categorias as $cat) : ?>
                                                    <option value="<?php echo htmlspecialchars($cat['ID_CATEGORIA']); ?>">
                                                        <?php echo htmlspecialchars($cat['CATEGORIA']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Reasignar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Modal para establecer límite de deportistas -->
                <?php foreach ($categorias as $categoria) : ?>
                    <div class="modal fade" id="modalEstablecerLimite<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>" tabindex="-1" aria-labelledby="modalEstablecerLimiteLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEstablecerLimiteLabel">Establecer Límite de Deportistas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                        <input type="hidden" name="accion" value="modificar_limite">
                                        <input type="hidden" name="categoria_limite" value="<?php echo htmlspecialchars($categoria['ID_CATEGORIA']); ?>">
                                        <div class="mb-3">
                                            <label for="nuevo_limite" class="form-label">Nuevo Límite de Deportistas</label>
                                            <input type="number" class="form-control" id="nuevo_limite" name="nuevo_limite" min="0" value="<?php echo htmlspecialchars($categoria['limite_deportistas']); ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>



                <!-- Modal para agregar deportista -->
                <div class="modal fade" id="addDeportistaModal" tabindex="-1" aria-labelledby="addDeportistaModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDeportistaModalLabel">Agregar Deportista a la Categoría</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <input type="hidden" name="id_categoria" id="addDeportistaModalCategoriaId">
                                    <div class="mb-3">
                                        <label for="id_deportista" class="form-label">Seleccionar Deportista</label>
                                        <select class="form-select" name="id_deportista" required>
                                            <option value="">Seleccione un deportista</option>
                                            <?php foreach ($todosDeportistas as $deportista) : ?>
                                                <option value="<?php echo htmlspecialchars($deportista['ID_DEPORTISTA']); ?>">
                                                    <?php echo htmlspecialchars($deportista['nombre_completo']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="accion" value="agregar_deportista" class="btn btn-primary">Agregar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para agregar entrenador -->
                <div class="modal fade" id="addEntrenadorModal" tabindex="-1" aria-labelledby="addEntrenadorModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addEntrenadorModalLabel">Agregar Entrenador a la Categoría</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <input type="hidden" name="id_categoria" id="addEntrenadorModalCategoriaId">
                                    <div class="mb-3">
                                        <label for="id_entrenador" class="form-label">Seleccionar Entrenador</label>
                                        <select class="form-select" name="id_entrenador" required>
                                            <option value="">Seleccione un entrenador</option>
                                            <?php foreach ($todosEntrenadores as $entrenador) : ?>
                                                <option value="<?php echo htmlspecialchars($entrenador['ID_ENTRENADOR']); ?>">
                                                    <?php echo htmlspecialchars($entrenador['nombre_completo']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="accion" value="agregar_entrenador" class="btn btn-primary">Agregar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para eliminar -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <input type="hidden" name="categoria_id" id="deleteModalCategoriaId">
                                    <p>¿Está seguro de que desea eliminar esta categoría?</p>
                                    <button type="submit" name="accion" value="eliminar_categoria" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scripts para manejar los modales -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var addDeportistaModal = document.getElementById('addDeportistaModal');
                        addDeportistaModal.addEventListener('show.bs.modal', function(event) {
                            var button = event.relatedTarget;
                            var categoriaId = button.getAttribute('data-categoria-id');
                            var inputCategoriaId = addDeportistaModal.querySelector('#addDeportistaModalCategoriaId');
                            inputCategoriaId.value = categoriaId;
                        });

                        var addEntrenadorModal = document.getElementById('addEntrenadorModal');
                        addEntrenadorModal.addEventListener('show.bs.modal', function(event) {
                            var button = event.relatedTarget;
                            var categoriaId = button.getAttribute('data-categoria-id');
                            var inputCategoriaId = addEntrenadorModal.querySelector('#addEntrenadorModalCategoriaId');
                            inputCategoriaId.value = categoriaId;
                        });

                        var deleteModal = document.getElementById('deleteModal');
                        deleteModal.addEventListener('show.bs.modal', function(event) {
                            var button = event.relatedTarget;
                            var categoriaId = button.getAttribute('data-categoria-id');
                            var inputCategoriaId = deleteModal.querySelector('#deleteModalCategoriaId');
                            inputCategoriaId.value = categoriaId;
                        });
                    });
                </script>

                <?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>