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
<!-- Formulario para subir archivos (oculto) -->
<form id="uploadBackupForm" action="/looneytunes/Uploads/uploadBackup.php" method="POST" enctype="multipart/form-data" style="display:none;">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ingresarModal = new bootstrap.Modal(document.getElementById('ingresarModal'));
    
    function handleIngresarClick(e) {
        e.preventDefault();
        var deportistaId = this.getAttribute('data-id');
        document.getElementById('deportistaId').value = deportistaId;
        
        // Establecer la fecha actual en el campo de fecha de ingreso
        var fechaActual = new Date().toISOString().split('T')[0];
        document.getElementById('fechaIngreso').value = fechaActual;
        
        ingresarModal.show();
    }

    document.querySelectorAll('.btn-ingresar').forEach(function(button) {
        button.addEventListener('click', handleIngresarClick);
    });

    document.querySelector('.btn-close').addEventListener('click', function() {
        ingresarModal.hide();
    });

    document.getElementById('ingresarModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('detallesForm').reset();
        document.body.classList.remove('modal-open');
        document.querySelector('.modal-backdrop')?.remove();
    });

    document.getElementById('guardarDetalles').addEventListener('click', function() {
        var form = document.getElementById('detallesForm');
        var formData = new FormData(form);

        fetch('guardar_detalles.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Detalles guardados con éxito');
                ingresarModal.hide();
            } else {
                alert('Error al guardar los detalles: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al guardar los detalles');
        })
        .finally(() => {
            ingresarModal.hide();
            document.body.classList.remove('modal-open');
            document.querySelector('.modal-backdrop')?.remove();
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ingresarModal = new bootstrap.Modal(document.getElementById('ingresarModal'));
    
    document.querySelectorAll('.btn-ingresar').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var deportistaId = this.getAttribute('data-id');
            document.getElementById('deportistaId').value = deportistaId;
            ingresarModal.show();
        });
    });

    document.getElementById('guardarDetalles').addEventListener('click', function() {
        var form = document.getElementById('detallesForm');
        var formData = new FormData(form);

        fetch('guardar_detalles.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Detalles guardados con éxito');
                ingresarModal.hide();
            } else {
                alert('Error al guardar los detalles: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al guardar los detalles');
        });
    });
});


</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el clic en el botón "Informes"
    document.querySelectorAll('.btn-informes').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const deportistaId = this.getAttribute('data-id');
            const representanteId = this.getAttribute('data-representante');
            document.getElementById('informeDeportistaId').value = deportistaId;
            document.getElementById('informeRepresentanteId').value = representanteId;
            new bootstrap.Modal(document.getElementById('informesModal')).show();
        });
    });

    // Manejar el envío del formulario de informes
    document.getElementById('enviarInforme').addEventListener('click', function() {
        const form = document.getElementById('informeForm');
        const formData = new FormData(form);

        fetch('enviar_informe.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                document.getElementById('informesModal').querySelector('.btn-close').click();
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al enviar el informe.');
        });
    });
});
</script>
</body>

</html>