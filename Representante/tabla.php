<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

require_once('../admin/configuracion/conexion.php');

date_default_timezone_set('America/Guayaquil');

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Comprobamos si el usuario es entrenador o representante
$id_usuario = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT ID_TIPO FROM tab_usu_tipo WHERE ID_USUARIO = :id_usuario");
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$tipo_usuario = $stmt->fetchColumn();

if ($tipo_usuario != '3') {
    echo "Acceso denegado. No eres un representante.";
    exit();
}

// Ejemplo de una consulta específica para este archivo
try {
    // Aquí podrías hacer una consulta a la base de datos para obtener datos que desees mostrar en una tabla.
    $stmt = $conn->prepare("SELECT * FROM tab_deportistas WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit();
}

// Incluir el encabezado (header)
include './Includes/header.php';
?>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-primary">Tabla de Pagos</h1>
                <p class="text-gray-700 mb-0">Aquí puedes ver la información detallada de los deportistas asociados.</p>
            </div>
        </div>
    </header>
    
    <!-- Contenido principal de la página -->
    <div class="container-xl px-4 mt-n10">
        <!-- Ejemplo de tabla para mostrar datos -->
        <div class="card mb-4">
            <div class="card-header">Lista de Deportistas</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Edad</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportistas as $deportista): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deportista['NOMBRE_DEPO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($deportista['APELLIDO_DEPO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($deportista['EDAD'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($deportista['CATEGORIA'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="./perfil.php?id_deportista=<?php echo htmlspecialchars($deportista['ID_DEPORTISTA'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-sm">Ver perfil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
// Incluir el pie de página (footer)
include './Includes/footer.php';
?>
