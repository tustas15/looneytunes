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

<!-- JavaScript -->
<!-- Asegúrate de que todas las rutas sean correctas -->
<script>
    $(document).ready(function() {
        $('.ver-info').click(function() {
            var deportista_id = $(this).data('deportista-id');
            $.ajax({
                type: 'POST',
                url: 'loadInfoDeportista.php',
                data: {
                    deportista_id: deportista_id,
                    csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>'
                },
                success: function(response) {
                    $('#info-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar la información del deportista:", status, error);
                    $('#info-container').html("Error al cargar la información del deportista.");
                }
            });
        });

        // Acción para ver rendimiento
        $('.ver-rendimiento').click(function() {
            var deportista_id = $(this).data('deportista-id');
            $.ajax({
                type: 'POST',
                url: 'loadRendimientoDeportista.php',
                data: {
                    deportista_id: deportista_id,
                    csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>'
                },
                success: function(response) {
                    $('#rendimiento-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar el rendimiento del deportista:", status, error);
                    $('#rendimiento-container').html("Error al cargar el rendimiento del deportista.");
                }
            });
        });
    });
</script>
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