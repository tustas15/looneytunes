<?php
// Conexión a la base de datos
require_once('../conexion.php');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// ID del usuario actual
$user_id = $_SESSION['user_id'];

// Consulta para obtener la foto del usuario
$sql = "
    SELECT f.FOTO 
    FROM tab_fotos_usuario f
    JOIN tab_usu_tipo ut ON ut.ID_TIPO = f.ID_TIPO
    WHERE ut.ID_USUARIO = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$foto = $stmt->fetchColumn();

// Codificar la foto en base64
$foto_src = $foto ? 'data:image/jpeg;base64,' . base64_encode($foto) : '/looneytunes/Assets/img/illustrations/profiles/profile-1.png';

// Obtener el nombre y el tipo de usuario de la sesión
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario = $_SESSION['user_id']; // Cambiado para usar el ID de usuario de la sesión

// Obtener el término de búsqueda si se ha enviado
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Verificar si se ha solicitado activar o desactivar un deportista
if (isset($_GET['action']) && isset($_GET['ID_DEPORTISTA'])) {
    $idDeportista = $_GET['ID_DEPORTISTA'];
    $newStatus = $_GET['action'] === 'activate' ? 'activo' : 'inactivo';

    try {
        // Obtener el ID_USUARIO relacionado
        $sql = "SELECT ID_USUARIO FROM tab_deportistas WHERE ID_DEPORTISTA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idDeportista]);
        $idUsuario = $stmt->fetchColumn();

        if (!$idUsuario) {
            echo "ID de usuario no encontrado.";
            exit();
        }

        // Obtener el nombre del deportista
        $sql = "SELECT NOMBRE_DEPO, APELLIDO_DEPO FROM tab_deportistas WHERE ID_DEPORTISTA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idDeportista]);
        $deportistaData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$deportistaData) {
            echo "Deportista no encontrado.";
            exit();
        }

        $nombreDeportista = $deportistaData['NOMBRE_DEPO'] . ' ' . $deportistaData['APELLIDO_DEPO'];

        // Actualizar el estado del deportista en `tab_deportistas`
        $sql = "UPDATE tab_deportistas SET status = ? WHERE ID_DEPORTISTA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newStatus, $idDeportista]);

        // Actualizar el estado del usuario en `tab_usuarios`
        $sql = "UPDATE tab_usuarios SET status = ? WHERE ID_USUARIO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newStatus, $idUsuario]);

        // Registrar el cambio en la tabla `tab_logs`
        $sql = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $evento = $newStatus === 'activo' ? "Deportista $nombreDeportista activado" : "Deportista $nombreDeportista desactivado";
        $ip = $_SERVER['REMOTE_ADDR'];
        $tipoEvento = $newStatus === 'activo' ? 'usuario_activo' : 'usuario_inactivo';

        // Asegúrate de que el siguiente código se ejecute correctamente
        $stmt->execute([$usuario, $evento, $ip, $tipoEvento]);

        header("Location: indexdeportista.php?mensaje=estado_actualizado");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Incluir el encabezado de la página
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
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
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table">
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
                                <th>Acciones</th>
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
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            try {
                                // Construir la consulta SQL con el término de búsqueda
                                $sql = "SELECT d.ID_DEPORTISTA, u.ID_USUARIO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.FECHA_NACIMIENTO, d.CEDULA_DEPO, d.NUMERO_CELULAR, d.GENERO, d.status
                                        FROM tab_deportistas d
                                        INNER JOIN tab_usuarios u ON d.ID_USUARIO = u.ID_USUARIO
                                        INNER JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO
                                        INNER JOIN tab_tipo_usuario t ON ut.ID_TIPO = t.ID_TIPO
                                        WHERE t.ID_TIPO = 4";

                                if ($searchTerm) {
                                    $sql .= " AND (d.NOMBRE_DEPO LIKE ? OR d.APELLIDO_DEPO LIKE ? OR d.CEDULA_DEPO LIKE ? OR d.NUMERO_CELULAR LIKE ?)";
                                }

                                $stmt = $conn->prepare($sql);

                                if ($searchTerm) {
                                    $searchTerm = '%' . $searchTerm . '%';
                                    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                                } else {
                                    $stmt->execute();
                                }

                                $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Mostrar la lista de deportistas
                                foreach ($deportistas as $deportista) {
                                    $status = htmlspecialchars($deportista['status']);
                                    $actionLink = $status === 'activo' ? 
                                        "<a href='indexdeportista.php?action=deactivate&ID_DEPORTISTA=" . htmlspecialchars($deportista['ID_DEPORTISTA']) . "'>Desactivar</a>" : 
                                        "<a href='indexdeportista.php?action=activate&ID_DEPORTISTA=" . htmlspecialchars($deportista['ID_DEPORTISTA']) . "'>Activar</a>";

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
                                            <a href='../perfil/perfil_deportista.php?ID_DEPORTISTA=" . htmlspecialchars($deportista['ID_DEPORTISTA']) . "'>Ver Perfil</a> | 
                                            $actionLink
                                          </td>";
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
                </div> <!-- Fin de table-responsive -->
            </div>
        </div>
    </div>
</main>

<?php include_once('/xampp/htdocs/looneytunes/admin/includespro/footer.php'); ?>
