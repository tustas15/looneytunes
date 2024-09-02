<footer class="footer-admin mt-auto footer-light">
    <div class="container-xl px-4">
        <div class="row">
            <div class="col-md-6 small">Copyright &copy; Looneytunes <span id="currentYear"></span></div>
            <div class="col-md-6 text-md-end small">
                <a href="/public/Privacy_Policy.php">Privacy Policy</a>
                &middot;
                <a href="/public/terms_condition.php">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<!-- Modal para Subir Hoja de Vida -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Subir Hoja de Vida (PDF)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" action="./configuracion/upload.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cvFile" class="form-label">Selecciona el archivo PDF</label>
                        <input type="file" class="form-control" id="cvFile" name="cvFile" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>


    document.addEventListener('DOMContentLoaded', function() {

// Manejador para el botón de Observacion
$('.btn-informes').click(function(e) {
    console.log('Botón informes clickeado');
    e.preventDefault();
    var deportistaId = $(this).data('id');
    var representanteId = $(this).data('representante');
    var nombreDeportista = $(this).data('nombre');
    console.log('Deportista ID:', deportistaId, 'Representante ID:', representanteId, 'Nombre:', nombreDeportista);
    $('#informeDeportistaId').val(deportistaId);
    $('#informeRepresentanteId').val(representanteId);
    $('#nombreDeportista').text(nombreDeportista);
    $('#informesModal').modal('show');
});

// Manejador para enviar el Observacion
$('#enviarInforme').click(function() {
var deportistaId = $('#informeDeportistaId').val();
var representanteId = $('#informeRepresentanteId').val();
var informe = $('#informe').val();

$.ajax({
    url: 'guardar_informe.php',
    method: 'POST',
    data: {
        deportistaId: deportistaId,
        representanteId: representanteId,
        informe: informe
    },
    success: function(response) {
            $('#informesModal').modal('hide');
            $('#informe').val('');
    },
    error: function() {
        console.error('Error AJAX:', textStatus, errorThrown);
    }
});
});

// Manejador para el botón de ingresar detalles
$('.btn-ingresar').on('click', function(e) {
    e.preventDefault();
    var deportistaId = $(this).data('id');
    var nombreDeportista = $(this).data('nombre');
    $('#deportistaId').val(deportistaId);
    $('#nombreDeportistaIngreso').text(nombreDeportista);
    $('#ingresarModal').modal('show');
});

// Manejador para guardar los detalles
$('#guardarDetalles').click(function() {
    var deportistaId = $('#deportistaId').val();
    var numeroCamisa = $('#numeroCamisa').val();
    var altura = $('#altura').val();
    var peso = $('#peso').val();
    var fechaIngreso = $('#fechaIngreso').val();

    $.ajax({
        url: 'guardar_detalles.php',
        method: 'POST',
        data: {
            deportistaId: deportistaId,
            numeroCamisa: numeroCamisa,
            altura: altura,
            peso: peso,
            fechaIngreso: fechaIngreso
        },
        success: function(response) {
            $('#ingresarModal').modal('hide');
            // Limpiar los campos del formulario
            $('#detallesForm')[0].reset();
        },
        error: function() {
            alert('Error al guardar los detalles');
        }
    });
});
});


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

    
});

document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    });



</script>
</body>
</html>