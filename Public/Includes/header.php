<?php
// Conexión a la base de datos
require_once('../Admin/configuracion/conexion.php');

// Verificación de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8') : 'Usuario';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Account Settings - Profile - SB Admin Pro</title>
    <link href="../Assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>

<body class="nav-fixed">
    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
        <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="../Admin/index.php">SB Admin Pro</a>
        <ul class="navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="img-fluid" src="../Assets/img/illustrations/profiles/profile-1.png" /></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                    <h6 class="dropdown-header d-flex align-items-center">
                        <img class="dropdown-user-img" src="../Assets/img/illustrations/profiles/profile-1.png" />
                        <div class="dropdown-user-details">
                            <div class="dropdown-user-details-name"><?= $nombre ?></div>
                        </div>
                    </h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../Public/profile.php">
                        <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                        Cuenta
                    </a>
                    <a class="dropdown-item" href="../Public/logout.php">
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
                        <div class="sidenav-menu-heading">Core</div>
                        <a class="nav-link" href=" ../index.php">
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Dashboard
                        </a>
                        <?php if ($tipo_usuario === 'admin'): ?>
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                            <div class="nav-link-icon"><i data-feather="settings"></i></div>
                            Admin Panel
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <?php endif; ?>
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
