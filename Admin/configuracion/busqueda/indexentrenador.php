<?php
// Conexión a la base de datos
require_once('../conexion.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../Public/login.php");
    exit();
}

// Verificar el tipo de usuario
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Usuario'; // Cambiado para usar el ID de usuario de la sesión

// Verificar si se ha solicitado activar o desactivar un entrenador
if (isset($_GET['action']) && isset($_GET['ID_ENTRENADOR'])) {
    $idEntrenador = $_GET['ID_ENTRENADOR'];
    $newStatus = $_GET['action'] === 'activate' ? 'activo' : 'inactivo';

    try {
        // Obtener el ID_USUARIO relacionado
        $sql = "SELECT ID_USUARIO FROM tab_entrenadores WHERE ID_ENTRENADOR = :idEntrenador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idEntrenador', $idEntrenador, PDO::PARAM_INT);
        $stmt->execute();
        $idUsuario = $stmt->fetchColumn();

        if (!$idUsuario) {
            echo "ID de usuario no encontrado.";
            exit();
        }

        // Obtener el nombre del entrenador
        $sql = "SELECT NOMBRE_ENTRE, APELLIDO_ENTRE FROM tab_entrenadores WHERE ID_ENTRENADOR = :idEntrenador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idEntrenador', $idEntrenador, PDO::PARAM_INT);
        $stmt->execute();
        $entrenadorData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entrenadorData) {
            echo "Entrenador no encontrado.";
            exit();
        }

        $nombreEntrenador = $entrenadorData['NOMBRE_ENTRE'] . ' ' . $entrenadorData['APELLIDO_ENTRE'];

        // Actualizar el estado del entrenador en `tab_entrenadores`
        $sql = "UPDATE tab_entrenadores SET status = :newStatus WHERE ID_ENTRENADOR = :idEntrenador";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':idEntrenador', $idEntrenador, PDO::PARAM_INT);
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
        $evento = $newStatus === 'activo' ? "Entrenador $nombreEntrenador activado" : "Entrenador $nombreEntrenador desactivado";
        $ip = $_SERVER['REMOTE_ADDR'];
        $tipoEvento = $newStatus === 'activo' ? 'usuario_activo' : 'usuario_inactivo';

        $stmt->bindParam(':idUsuario', $usuario, PDO::PARAM_INT);
        $stmt->bindParam(':evento', $evento, PDO::PARAM_STR);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':tipoEvento', $tipoEvento, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: indexentrenador.php?mensaje=estado_actualizado");
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
            <h1>Lista de Entrenadores</h1>
        </div>

        <!-- Example DataTable for Dashboard Demo -->
        <div class="card mb-4">
            <div class="card-header">Entrenadores</div>
            <div class="card-body">
                <div class="table-responsive"> <!-- Aquí añadimos el contenedor table-responsive -->
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>ID Entrenador</th>
                                <th>ID Usuario</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Experiencia</th>
                                <th>Celular</th>
                                <th>Correo</th>
                                <th>Perfil</th>
                                <th>Acciones</th> <!-- Columna para activar/desactivar -->
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID Entrenador</th>
                                <th>ID Usuario</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Experiencia</th>
                                <th>Celular</th>
                                <th>Correo</th>
                                <th>Perfil</th>
                                <th>Acciones</th> <!-- Columna para activar/desactivar -->
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            try {
                                // Consulta para obtener todos los entrenadores con sus detalles
                                $stmt = $conn->prepare("
                                    SELECT e.ID_ENTRENADOR, e.ID_USUARIO, u.USUARIO, e.NOMBRE_ENTRE, e.APELLIDO_ENTRE, e.EXPERIENCIA_ENTRE, e.CELULAR_ENTRE, e.CORREO_ENTRE, e.status
                                    FROM tab_entrenadores e
                                    INNER JOIN tab_usuarios u ON e.ID_USUARIO = u.ID_USUARIO
                                    WHERE e.ID_USUARIO IN (
                                        SELECT ID_USUARIO FROM tab_usu_tipo WHERE ID_TIPO = :tipo_entrenador
                                    )
                                ");
                                $tipo_entrenador = 2;  // ID_TIPO para entrenadores
                                $stmt->bindParam(':tipo_entrenador', $tipo_entrenador, PDO::PARAM_INT);
                                $stmt->execute();
                                $entrenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Mostrar la lista de entrenadores
                                foreach ($entrenadores as $entrenador) {
                                    $status = htmlspecialchars($entrenador['status']);
                                    $actionLink = $status === 'activo' ? 
                                        "<a href='indexentrenador.php?action=deactivate&ID_ENTRENADOR=" . htmlspecialchars($entrenador['ID_ENTRENADOR']) . "'>Desactivar</a>" : 
                                        "<a href='indexentrenador.php?action=activate&ID_ENTRENADOR=" . htmlspecialchars($entrenador['ID_ENTRENADOR']) . "'>Activar</a>";

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($entrenador['ID_ENTRENADOR']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['ID_USUARIO']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['NOMBRE_ENTRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['APELLIDO_ENTRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['EXPERIENCIA_ENTRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['CELULAR_ENTRE']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entrenador['CORREO_ENTRE']) . "</td>";
                                    echo "<td><a href='../perfil/perfil_entrenador.php?ID_USUARIO=" . htmlspecialchars($entrenador['ID_USUARIO']) . "'>Ver Perfil</a></td>";
                                    echo "<td>$actionLink</td>";
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
                </div> <!-- Fin del contenedor table-responsive -->
            </div>
        </div>
    </div>
</main>

<?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
