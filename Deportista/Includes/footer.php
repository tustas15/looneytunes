<footer class="footer-admin mt-auto footer-light">
    <div class="container-xl px-4">
        <div class="row">
            <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
            <div class="col-md-6 text-md-end small">
                <a href="/Public/Privacy_Policy.php">Privacy Policy</a>
                &middot;
                <a href="/Public/terms_condition.php">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
</div>
</div>
<script>
    feather.replace();
</script>
<!-- Formulario para subir archivos (oculto) -->
<form id="uploadBackupForm" action="/Uploads/uploadBackup.php" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="file" id="backupFile" name="backupFile" required>
</form>

<!-- JavaScript para manejar el clic en el enlace -->
<script>
    document.getElementById('uploadBackupLink').addEventListener('click', function() {
        document.getElementById('backupFile').click();
    });

    document.getElementById('backupFile').addEventListener('change', function() {
        document.getElementById('uploadBackupForm').submit();
    });
</script>
<script>
    $(document).ready(function() {
        $('#datatablesSimple').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.3/i18n/es-ES.json"
            },
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "autoWidth": false
        });
    });
</script>
<script>
    // JavaScript para actualizar el año actual en el footer
    document.addEventListener('DOMContentLoaded', function() {
        var currentYear = new Date().getFullYear();
        document.getElementById('currentYear').textContent = currentYear;
    });
</script>
<script>
    document.getElementById('uploadBackupLink').addEventListener('click', function() {
        document.getElementById('backupFile').click();
    });

    document.getElementById('backupFile').addEventListener('change', function() {
        document.getElementById('uploadBackupForm').submit();
    });


    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('imcChart').getContext('2d');
        var imcData = <?php echo json_encode($imc_data); ?>;

        var labels = imcData.map(function(item) {
            return item.fecha;
        });
        var data = imcData.map(function(item) {
            return item.imc;
        });

        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'IMC',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="/Assets/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
<script src="/Assets/demo/chart-area-demo.js"></script>
<script src="/Assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="/Assets/js/datatables/datatables-simple-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js" crossorigin="anonymous"></script>
<script src="/Assets/js/litepicker.js"></script>
</body>

</html>