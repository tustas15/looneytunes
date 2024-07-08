<?php
// Conexión a la base de datos
require_once('../Admin/configuracion/conexion.php');
// Inicio de sesión
session_start();
// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}
// Comprobamos si el usuario es entrenador
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}
// Comprobamos si el usuario entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
// Comprobamos si el usuario es entrenador
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
include './includes/header.php';

?>

<!-- Page Heading -->
<div class="container-xl px-4 mt-n10">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bienvenido, Representante <?= $nombre ?></h1>
        <a href="../respaldo/downloadFile.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generar Respaldo</a>
    </div>
</div>

<?php
include './includes/footer.php';
?>