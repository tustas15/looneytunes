<!DOCTYPE html>
<html lang="en">

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
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="/looneytunes/index.php">Dashboard</a>
        <!-- Navbar Search Input-->
        <form class="form-inline me-auto d-none d-lg-block me-3">
            <div class="input-group input-group-joined input-group-solid">
                <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                <div class="input-group-text"><i data-feather="search"></i></div>
            </div>
        </form>
        <!-- Navbar Items-->
        <ul class="navbar-nav align-items-center ms-auto">
            <!-- Documentation Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="fw-500">Documentation</div>
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
                    <a class="dropdown-item py-3" href="#" data-bs-toggle="modal" data-bs-target="#uploadBackupModal">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="upload"></i></div>
                        <div>
                            <div class="small text-gray-500">Subir Respaldo</div>
                            Haz click para subir el respaldo del sistema
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro/changelog" target="_blank">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="file-text"></i></div>
                        <div>
                            <div class="small text-gray-500">Changelog</div>
                            Updates and changes
                        </div>
                    </a>
                </div>
            </li>
            <!-- Formulario para subir archivos (oculto) -->
            <form id="uploadBackupForm" action="/looneytunes/Uploads/uploadBackup.php" method="POST" enctype="multipart/form-data" style="display:none;">
                <input type="file" id="backupFile" name="backupFile" required>
            </form>

            <!-- Navbar Search Dropdown-->
            <li class="nav-item dropdown no-caret me-3 d-lg-none">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="searchDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="search"></i></a>
                <!-- Dropdown - Search-->
                <div class="dropdown-menu dropdown-menu-end p-3 shadow animated--fade-in-up" aria-labelledby="searchDropdown">
                    <form class="form-inline me-auto w-100">
                        <div class="input-group input-group-joined input-group-solid">
                            <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                            <div class="input-group-text"><i data-feather="search"></i></div>
                        </div>
                    </form>
                </div>
            </li>
            <!-- User Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-md-block">
                <a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="d-inline-flex align-items-center">
                        <div class="me-2"><img class="img-fluid" src="/looneytunes/AssetsFree/img/logo.png" width="40" height="40" /></div>
                        <div class="fw-500 d-none d-md-block"><?php echo $_SESSION['usuario']['nombre']; ?></div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#registerCedulaModal">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="user"></i></div>
                        <div>
                            <div class="small text-gray-500">Registrar Cédula</div>
                            Agrega una nueva cédula
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="logout.php">
                        <div class="icon-stack bg-danger-soft text-danger me-4"><i data-feather="log-out"></i></div>
                        <div>
                            <div class="small text-gray-500">Cerrar Sesión</div>
                            Cierra tu sesión actual
                        </div>
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sidenav shadow-right sidenav-light">
                <div class="sidenav-menu">
                    <div class="nav accordion" id="accordionSidenav">
                        <div class="sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="/looneytunes/index.php">
                            <div class="nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sidenav-menu-heading">Funciones</div>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerCedulaModal"><i class="fas fa-id-card"></i> Registrar Cédula</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <!-- Aquí puedes colocar el contenido del dashboard -->
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Tu Empresa 2024</div>
                        <div>
                            <a href="#">Privacidad</a>
                            &middot;
                            <a href="#">Términos</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal para registrar cédula -->
    <div class="modal fade" id="registerCedulaModal" tabindex="-1" aria-labelledby="registerCedulaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerCedulaModalLabel">Registrar Cédula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerCedulaForm" action="registrar.php" method="POST">
                        <div class="mb-3">
                            <label for="cedulaInput" class="form-label">Ingrese la Cédula</label>
                            <input type="text" class="form-control" id="cedulaInput" name="cedula" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para subir respaldo -->
    <div class="modal fade" id="uploadBackupModal" tabindex="-1" aria-labelledby="uploadBackupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadBackupModalLabel">Subir Respaldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadBackupForm" action="/looneytunes/Uploads/uploadBackup.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Seleccionar archivo de respaldo</label>
                            <input type="file" class="form-control" id="backupFile" name="backupFile" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Subir Respaldo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>