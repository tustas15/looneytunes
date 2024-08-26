<footer class="footer-admin mt-auto footer-light">
    <div class="container-xl px-4">
        <div class="row">
            <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
            <div class="col-md-6 text-md-end small">
                <a href="../public/Privacy_Policy.php">Privacy Policy</a>
                &middot;
                <a href="../public/terms_condition.php">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<!-- Modal para Subir Hoja de Vida -->
<div class="modal fade" id="ObservacionesModal" tabindex="-1" aria-labelledby="ObservacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <h6 class="dropdown-header dropdown-notifications-header"> 
                        Mensajes de Observaciones
                    </h6>
        <?php if (!empty($informes)): ?>
                        <?php foreach ($informes as $informe): ?>
                            <a class="dropdown-item dropdown-notification-item" href="#">
                                <div class="dropdown-notification-item-content">
                                    <div class="dropdown-notification-item-title"><?= htmlspecialchars($informe['informe']); ?></div>
                                    <div class="dropdown-notification-item-time"><?= htmlspecialchars($informe['fecha_creacion']); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="dropdown-item-text">No hay informes disponibles</p>
                    <?php endif; ?>
        </div>
    </div>
</div>

</div>
</div>
<script>
    feather.replace();
</script>

<!-- JavaScript para manejar el clic en el enlace -->

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


    
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../Assets/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
<script src="../Assets/demo/chart-area-demo.js"></script>
<script src="../Assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="../Assets/js/datatables/datatables-simple-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js" crossorigin="anonymous"></script>
<script src="../Assets/js/litepicker.js"></script>



<script>
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


</body>


</html>