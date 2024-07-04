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
</div>
</div>
<script>
    feather.replace();
</script>

<!-- JavaScript para manejar el clic en el enlace -->
<script>
    document.querySelector('#registerCedulaLink').addEventListener('click', function(event) {
        event.preventDefault();
        var myModal = new bootstrap.Modal(document.getElementById('registerCedulaModal'));
        myModal.show();
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
</body>

</html>