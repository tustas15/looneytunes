<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Dashboard page" />
    <meta name="author" content="Your Name" />
    <title>Dashboard</title>
    <link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>

<body class="nav-fixed">
    <!-- Logout Link -->
    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
        Cerrar sesión
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "Cerrar sesión" a continuación si está listo para finalizar su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../login/logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Backup Link -->
    <div class="dropdown-divider m-0"></div>
    <a id="uploadBackupLink" class="dropdown-item py-3" href="javascript:void(0);" data-toggle="modal" data-target="#uploadBackupModal">
        <div class="icon-stack bg-primary-soft text-primary me-4"><i class="fas fa-upload"></i></div>
        <div>
            <div class="small text-gray-500">Subir Respaldo</div>
            Haz click para subir el respaldo del sistema
        </div>
    </a>

    <!-- Upload Backup Modal -->
    <div class="modal fade" id="uploadBackupModal" tabindex="-1" role="dialog" aria-labelledby="uploadBackupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="uploadBackupForm" action="/looneytunes/Uploads/uploadBackup.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadBackupModalLabel">Subir Respaldo</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Selecciona el archivo de respaldo</label>
                            <input type="file" class="form-control" id="backupFile" name="backupFile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Subir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-admin mt-auto footer-light">
        <div class="container-xl px-4">
            <div class="row">
                <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
                <div class="col-md-6 text-md-end small">
                    <a href="/looneytunes/Public/Privacy_Policy.php">Privacy Policy</a>
                    &middot;
                    <a href="/looneytunes/Public/terms_condition.php">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentYear = new Date().getFullYear();
            document.getElementById('currentYear').textContent = currentYear;
        });

        document.getElementById('uploadBackupLink').addEventListener('click', function() {
            document.getElementById('backupFile').click();
        });

        document.getElementById('backupFile').addEventListener('change', function() {
            document.getElementById('uploadBackupForm').submit();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/looneytunes/Assets/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
    <script src="/looneytunes/Assets/demo/chart-area-demo.js"></script>
    <script src="/looneytunes/Assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="/looneytunes/Assets/js/datatables/datatables-simple-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js" crossorigin="anonymous"></script>
    <script src="/looneytunes/Assets/js/litepicker.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>