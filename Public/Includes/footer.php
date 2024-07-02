<footer class="footer-admin mt-auto footer-light">
        <div class="container-xl px-4">
            <div class="row">
            <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
                <div class="col-md-6 text-md-end small">
                    <a href="../Public/Privacy_Policy.php">Privacy Policy</a>
                    &middot;
                    <a href="../Public/terms_condition.php">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </footer>
</div>
<script>
    // JavaScript para actualizar el año actual en el footer
    document.addEventListener('DOMContentLoaded', function () {
        var currentYear = new Date().getFullYear();
        document.getElementById('currentYear').textContent = currentYear;
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../Assets/js/scripts.js"></script>
</body>
</html>
