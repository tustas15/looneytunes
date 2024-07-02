<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener el nombre y el tipo de usuario de la sesión
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

// Incluir el encabezado de la página
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';

// Obtener el término de búsqueda si se ha enviado
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<main>
    <div class="container-xl px-4 mt-4">
        <!-- Page title -->
        <div class="page-title">
            <h1>Lista de Deportistas</h1>
        </div>

        <!-- Example DataTable for Dashboard Demo-->
        <div class="card mb-4">
            <div class="card-header">Deportistas</div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>ID Deportista</th>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Cédula</th>
                            <th>Número de Celular</th>
                            <th>Género</th>
                            <th>Acciones</th> <!-- Columna para el botón de ver perfil -->
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>ID Deportista</th>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Cédula</th>
                            <th>Número de Celular</th>
                            <th>Género</th>
                            <th>Acciones</th> <!-- Columna para el botón de ver perfil -->
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        try {
                            // Construir la consulta SQL con el término de búsqueda
                            $sql = "SELECT d.ID_DEPORTISTA, u.ID_USUARIO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.FECHA_NACIMIENTO, d.CEDULA_DEPO, d.NUMERO_CELULAR, d.GENERO
                                    FROM tab_deportistas d
                                    INNER JOIN tab_usuarios u ON d.ID_USUARIO = u.ID_USUARIO
                                    INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
                                    INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO
                                    WHERE t.ID_TIPO = 4";
                            
                            if ($searchTerm) {
                                $sql .= " AND (d.NOMBRE_DEPO LIKE :searchTerm OR d.APELLIDO_DEPO LIKE :searchTerm OR d.CEDULA_DEPO LIKE :searchTerm OR d.NUMERO_CELULAR LIKE :searchTerm)";
                            }

                            $stmt = $conn->prepare($sql);

                            if ($searchTerm) {
                                $searchTerm = '%' . $searchTerm . '%';
                                $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
                            }

                            $stmt->execute();
                            $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Mostrar la lista de deportistas
                            foreach ($deportistas as $deportista) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($deportista['ID_DEPORTISTA']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['ID_USUARIO']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['NOMBRE_DEPO']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['APELLIDO_DEPO']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['FECHA_NACIMIENTO']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['CEDULA_DEPO']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['NUMERO_CELULAR']) . "</td>";
                                echo "<td>" . htmlspecialchars($deportista['GENERO']) . "</td>";
                                echo "<td>
                                        <a href='../perfil/perfil_deportista.php?ID_DEPORTISTA=" . htmlspecialchars($deportista['ID_DEPORTISTA']) . "'>Ver Perfil</a></td>";
                                echo "</tr>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='9'>Error: " . $e->getMessage() . "</td></tr>";
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
