<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

// Obtener el término de búsqueda si se ha enviado
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Lista de Representantes</h1>
        </div>        

        <!-- Tabla de Representantes -->
        <div class="card mb-4">
            <div class="card-header">Representantes</div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Celular</th>
                            <th>Acción</th> <!-- Nueva columna para el enlace al perfil -->
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Celular</th>
                            <th>Acción</th> <!-- Nueva columna para el enlace al perfil -->
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        try {
                            // Construir la consulta SQL con el término de búsqueda
                            $sql = "SELECT ID_REPRESENTANTE, NOMBRE_REPRE, APELLIDO_REPRE, CEDULA_REPRE, CELULAR_REPRE
                                    FROM tab_representantes";

                            if ($searchTerm) {
                                $sql .= " WHERE NOMBRE_REPRE LIKE :searchTerm OR CEDULA_REPRE LIKE :searchTerm";
                            }

                            $stmt = $conn->prepare($sql);

                            if ($searchTerm) {
                                $searchTerm = '%' . $searchTerm . '%';
                                $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
                            }

                            $stmt->execute();
                            $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Mostrar la lista de representantes
                            foreach ($representantes as $representante) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "</td>";
                                echo "<td>" . htmlspecialchars($representante['NOMBRE_REPRE']) . "</td>";
                                echo "<td>" . htmlspecialchars($representante['APELLIDO_REPRE']) . "</td>";
                                echo "<td>" . htmlspecialchars($representante['CEDULA_REPRE']) . "</td>";
                                echo "<td>" . htmlspecialchars($representante['CELULAR_REPRE']) . "</td>";
                                echo "<td><a href='../perfil/perfil_representante.php?ID_REPRESENTANTE=" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "'>Ver Perfil</a></td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
                        }

                        // Cierre de la conexión
                        $conn = null;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
