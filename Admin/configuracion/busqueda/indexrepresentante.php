<?php
// Conexión a la base de datos
require_once('../conexion.php');
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
$usuario = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Usuario'; // Cambiado para usar el ID de usuario de la sesión

// Obtener el término de búsqueda si se ha enviado
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Verificar si se ha solicitado activar o desactivar un representante
if (isset($_GET['action']) && isset($_GET['ID_REPRESENTANTE'])) {
    $idRepresentante = $_GET['ID_REPRESENTANTE'];
    $newStatus = $_GET['action'] === 'activate' ? 'activo' : 'inactivo';

    try {
        // Obtener el ID_USUARIO relacionado
        $sql = "SELECT ID_USUARIO FROM tab_representantes WHERE ID_REPRESENTANTE = :idRepresentante";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idRepresentante', $idRepresentante, PDO::PARAM_INT);
        $stmt->execute();
        $idUsuario = $stmt->fetchColumn();

        if (!$idUsuario) {
            echo "ID de usuario no encontrado.";
            exit();
        }

        // Obtener el nombre del representante
        $sql = "SELECT NOMBRE_REPRE, APELLIDO_REPRE FROM tab_representantes WHERE ID_REPRESENTANTE = :idRepresentante";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idRepresentante', $idRepresentante, PDO::PARAM_INT);
        $stmt->execute();
        $representanteData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$representanteData) {
            echo "Representante no encontrado.";
            exit();
        }

        $nombreRepresentante = $representanteData['NOMBRE_REPRE'] . ' ' . $representanteData['APELLIDO_REPRE'];

        // Actualizar el estado del representante en `tab_representantes`
        $sql = "UPDATE tab_representantes SET status = :newStatus WHERE ID_REPRESENTANTE = :idRepresentante";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':idRepresentante', $idRepresentante, PDO::PARAM_INT);
        $stmt->execute();

        // Actualizar el estado del usuario en `tab_usuarios`
        $sql = "UPDATE tab_usuarios SET status = :newStatus WHERE ID_USUARIO = :idUsuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        // Registrar el cambio en la tabla `tab_logs`
        $sql = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:idUsuario, :evento, CURRENT_TIME(), CURRENT_DATE(), :ip, :tipoEvento)";
        $stmt = $conn->prepare($sql);
        $evento = $newStatus === 'activo' ? "Representante $nombreRepresentante activado" : "Representante $nombreRepresentante desactivado";
        $ip = $_SERVER['REMOTE_ADDR'];
        $tipoEvento = $newStatus === 'activo' ? 'usuario_activo' : 'usuario_inactivo';

        $stmt->bindParam(':idUsuario', $usuario, PDO::PARAM_INT);
        $stmt->bindParam(':evento', $evento, PDO::PARAM_STR);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':tipoEvento', $tipoEvento, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: indexrepresentante.php?mensaje=estado_actualizado");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
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
                <div class="table-responsive"> <!-- Clase para hacer la tabla responsive -->
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>ID Representante</th>
                                <th>ID Usuario</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Celular</th>
                                <th>Perfil</th>
                                <th>Acciones</th> <!-- Columna para activar/desactivar -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID Representante</th>
                                <th>ID Usuario</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Celular</th>
                                <th>Perfil</th>
                                <th>Acciones</th> <!-- Columna para activar/desactivar -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            try {
                                // Construir la consulta SQL con el término de búsqueda
                                $sql = "SELECT r.ID_REPRESENTANTE, u.ID_USUARIO, u.USUARIO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CEDULA_REPRE, r.CELULAR_REPRE, r.status
                                        FROM tab_representantes r
                                        INNER JOIN tab_usuarios u ON r.ID_USUARIO = u.ID_USUARIO";

                                if ($searchTerm) {
                                    $sql .= " WHERE r.NOMBRE_REPRE LIKE :searchTerm OR r.CEDULA_REPRE LIKE :searchTerm";
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
                                    $status = htmlspecialchars($representante['status']);
                                    $actionLink = $status === 'activo' ? 
                                        "<a href='indexrepresentante.php?action=deactivate&ID_REPRESENTANTE=" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "'>Desactivar</a>" : 
                                        "<a href='indexrepresentante.php?action=activate&ID_REPRESENTANTE=" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "'>Activar</a>";

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($representante['ID_USUARIO']) . "</td>";
                                    echo "<td>" . htmlspecialchars($representante['NOMBRE_REPRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($representante['APELLIDO_REPRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($representante['CEDULA_REPRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($representante['CELULAR_REPRE']) . "</td>";
                                    echo "<td><a href='../perfil/perfil_representante.php?ID_REPRESENTANTE=" . htmlspecialchars($representante['ID_REPRESENTANTE']) . "'>Ver Perfil</a></td>";
                                    echo "<td>$actionLink</td>";
                                    echo "</tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='8'>Error: " . $e->getMessage() . "</td></tr>";
                            }

                            // Cierre de la conexión
                            $conn = null;
                            ?>
                        </tbody>
                    </table>
                </div> <!-- Fin del div table-responsive -->
            </div>
        </div>
    </div>
</main>

<?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
