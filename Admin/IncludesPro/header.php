<?php
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
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
    <link rel="icon" type="image/x-icon" href="/looneytunes/Assets/img/logo.png" />
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
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="../admin/indexAd.php">Dashboard</a>
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
                    <a class="dropdown-item py-3" href="../Admin/configuracion/respaldo/downloadFile.php">
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
                        <img class="dropdown-notifications-item-img" src="../Assets/img/illustrations/profiles/profile-2.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Thomas Wilcox · 58m</div>
                        </div>
                    </a>
                    <!-- Example Message 2-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="../Assets/img/illustrations/profiles/profile-3.png" />
                        <div class="dropdown-notifications-item-content">
                            <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                            <div class="dropdown-notifications-item-content-details">Emily Fowler · 2d</div>
                        </div>
                    </a>
                    <!-- Example Message 3-->
                    <a class="dropdown-item dropdown-notifications-item" href="#!">
                        <img class="dropdown-notifications-item-img" src="../Assets/img/illustrations/profiles/profile-4.png" />
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
                    <a class="dropdown-item" href="/looneytunes/public/profile.php">
                        <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                        Cuenta
                    </a>
                    <a class="dropdown-item" href="/looneytunes/public/logs.php">
                        <div class="dropdown-item-icon"><i data-feather="file-text"></i></div>
                        Registro de Actividades
                    </a>
                    <a class="dropdown-item" href="/looneytunes/public/logout.php">
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
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=category_list">
                                    Lista
                                </a>
                                <!-- Nested Sidenav Accordion (Categorías -> Buscar)
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=category_search">
                                    Buscar
                                </a>-->
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
                                <!-- Nested Sidenav Accordion (Productos -> Buscar)
                                <a class="nav-link collapsed" href="/looneytunes/Admin/inventario/index.php?vista=product_search">
                                    Buscar
                                </a>-->
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