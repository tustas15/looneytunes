<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Función para obtener todos los bancos
function obtenerBancos($conn) {
    $query = "SELECT * FROM tab_bancos ORDER BY nombre";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Manejar las acciones del CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $nombre = $conn->real_escape_string($_POST['nombre']);
                $conn->query("INSERT INTO tab_bancos (nombre) VALUES ('$nombre')");
                break;
            case 'editar':
                $id = $conn->real_escape_string($_POST['id']);
                $nombre = $conn->real_escape_string($_POST['nombre']);
                $conn->query("UPDATE tab_bancos SET nombre = '$nombre' WHERE id = $id");
                break;
            case 'cambiar_estado':
                $id = $conn->real_escape_string($_POST['id']);
                $estado = $conn->real_escape_string($_POST['estado']);
                $conn->query("UPDATE tab_bancos SET estado = '$estado' WHERE id = $id");
                break;
        }
    }
}

$bancos = obtenerBancos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Bancos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Administración de Bancos</h2>
        
        <!-- Formulario para agregar banco -->
        <form method="POST" class="mb-4">
            <input type="hidden" name="accion" value="agregar">
            <div class="input-group">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre del banco" required>
                <button type="submit" class="btn btn-primary">Agregar Banco</button>
            </div>
        </form>

        <!-- Tabla de bancos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bancos as $banco): ?>
                <tr>
                    <td><?= $banco['id'] ?></td>
                    <td>
                        <span class="banco-nombre"><?= $banco['nombre'] ?></span>
                        <form method="POST" class="d-none banco-editar-form">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?= $banco['id'] ?>">
                            <div class="input-group">
                                <input type="text" name="nombre" class="form-control" value="<?= $banco['nombre'] ?>" required>
                                <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                                <button type="button" class="btn btn-secondary btn-sm cancelar-edicion">Cancelar</button>
                            </div>
                        </form>
                    </td>
                    <td><?= $banco['estado'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm editar-banco">Editar</button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="cambiar_estado">
                            <input type="hidden" name="id" value="<?= $banco['id'] ?>">
                            <input type="hidden" name="estado" value="<?= $banco['estado'] == 'activo' ? 'inactivo' : 'activo' ?>">
                            <button type="submit" class="btn btn-<?= $banco['estado'] == 'activo' ? 'danger' : 'success' ?> btn-sm">
                                <?= $banco['estado'] == 'activo' ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.editar-banco').click(function() {
                var row = $(this).closest('tr');
                row.find('.banco-nombre').hide();
                row.find('.banco-editar-form').removeClass('d-none');
            });

            $('.cancelar-edicion').click(function() {
                var row = $(this).closest('tr');
                row.find('.banco-nombre').show();
                row.find('.banco-editar-form').addClass('d-none');
            });
        });
    </script>
</body>
</html>