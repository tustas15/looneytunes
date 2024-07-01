
</div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Looneytunes <span id="currentYear"></span></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
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
                    <a class="btn btn-primary" href="../Public/logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="../AssetsFree/vendor/jquery/jquery.min.js"></script>
    <script src="../AssetsFree//vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../AssetsFree//vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../AssetsFree/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../AssetsFree/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../AssetsFree/js/demo/chart-area-demo.js"></script>
    <script src="../AssetsFree/js/demo/chart-pie-demo.js"></script>

</body>

</html>