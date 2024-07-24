<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

// Obtener el ID del usuario logueado
$loggedUserId = $_SESSION['user_id'];

// Verificar si se ha solicitado eliminar un administrador
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['ID_ADMINISTRADOR'])) {
    $idAdministrador = $_GET['ID_ADMINISTRADOR'];

    try {
        // Comprobar si el usuario logueado está eliminando su propio perfil
        if ($idAdministrador == $loggedUserId) {
            // Cerrar la sesión del usuario
            session_destroy();
            header("Location: ../Public/login.php?mensaje=perfil_eliminado");
            exit();
        }

        // Proceder con la eliminación del perfil
        $sql = "DELETE FROM tab_administradores WHERE ID_ADMINISTRADOR = :idAdministrador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idAdministrador', $idAdministrador, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: indexadministrador.php?mensaje=eliminado");
            exit();
        } else {
            echo "Error al eliminar el administrador.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Obtener el término de búsqueda si se ha enviado
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Lista de Administradores</h1>
        </div>

        <!-- Example DataTable for Dashboard Demo-->
        <div class="card mb-4">
            <div class="card-header">Administradores</div>
            <div class="card-body">
                <!-- Formulario de búsqueda -->


                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Celular</th>
                            <th>Acción</th> <!-- Nueva columna para el enlace al perfil -->
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Celular</th>
                            <th>Acción</th> <!-- Nueva columna para el enlace al perfil -->
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        try {
                            // Construir la consulta SQL con el término de búsqueda
                            $sql = "SELECT ID_ADMINISTRADOR, NOMBRE_ADMIN, APELLIDO_ADMIN, CELULAR_ADMIN FROM tab_administradores";

                            if ($searchTerm) {
                                $sql .= " WHERE NOMBRE_ADMIN LIKE :searchTerm OR APELLIDO_ADMIN LIKE :searchTerm OR CELULAR_ADMIN LIKE :searchTerm";
                            }

                            $stmt = $conn->prepare($sql);

                            if ($searchTerm) {
                                $searchTerm = '%' . $searchTerm . '%';
                                $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
                            }

                            $stmt->execute();
                            $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Mostrar la lista de administradores
                            foreach ($administradores as $administrador) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($administrador['ID_ADMINISTRADOR']) . "</td>";
                                echo "<td>" . htmlspecialchars($administrador['NOMBRE_ADMIN']) . "</td>";
                                echo "<td>" . htmlspecialchars($administrador['APELLIDO_ADMIN']) . "</td>";
                                echo "<td>" . htmlspecialchars($administrador['CELULAR_ADMIN']) . "</td>";
                                echo "<td>
                                        <a href='../perfil/perfil_administrador.php?ID_ADMINISTRADOR=" . htmlspecialchars($administrador['ID_ADMINISTRADOR']) . "'>Ver Perfil</a> | 
                                        <a href='indexadministrador.php?action=delete&ID_ADMINISTRADOR=" . htmlspecialchars($administrador['ID_ADMINISTRADOR']) . "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este administrador?');\">Eliminar</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
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
