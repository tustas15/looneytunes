<?php
// Conexión a la base de datos
require_once('../admin/configuracion/conexion.php');

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Inicio de sesión
session_start();

// Comprobamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Comprobamos si el usuario es deportista
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'];
$id_usuario = $_SESSION['user_id'];

// Obtenemos el ID_DEPORTISTA del usuario actual
$query_deportista = "SELECT ID_DEPORTISTA FROM tab_deportistas WHERE ID_USUARIO = ?";
$stmt_deportista = $conn->prepare($query_deportista);
$stmt_deportista->execute([$id_usuario]);
$deportista = $stmt_deportista->fetch(PDO::FETCH_ASSOC);

if ($deportista) {
    $id_deportista = $deportista['ID_DEPORTISTA'];

    // Obtenemos los informes del deportista
    $query_informes = "SELECT * FROM tab_informes WHERE id_deportista = ? ORDER BY fecha_creacion DESC LIMIT 5";
    $stmt_informes = $conn->prepare($query_informes);
    $stmt_informes->execute([$id_deportista]);
    $informes = $stmt_informes->fetchAll(PDO::FETCH_ASSOC);
} else {
    $informes = [];
}

// Obtener el nombre del entrenador
$query_entrenador = "SELECT tab_entrenadores.nombre_entre,tab_entrenadores.apellido_entre, tab_categorias.categoria
                             FROM tab_entrenadores 
                             LEFT JOIN tab_entrenador_categoria ON tab_entrenadores.ID_ENTRENADOR = tab_entrenador_categoria.ID_ENTRENADOR
                             LEFT JOIN tab_categorias ON tab_entrenador_categoria.ID_CATEGORIA = tab_categorias.id_categoria
                             LEFT JOIN tab_categoria_deportista ON tab_categorias.ID_CATEGORIA = tab_categoria_deportista.ID_CATEGORIA 
                             LEFT JOIN tab_deportistas ON tab_categoria_deportista.ID_DEPORTISTA = tab_deportistas.ID_DEPORTISTA
                             WHERE tab_deportistas.ID_USUARIO = ?";
$stmt_entrenador = $conn->prepare($query_entrenador);
$stmt_entrenador->execute([$id_usuario]);
$entrenadores = $stmt_entrenador->fetch(PDO::FETCH_ASSOC);
if($entrenadores){
    $nombre_entrenador = $entrenadores['nombre_entre'].' '.$entrenadores['apellido_entre'];
}
if($entrenadores){
    $categoria = $entrenadores['categoria'];
}

include './includes/header.php';
?>
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-xxl-4 col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-body h-100 p-5">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-xxl-12">
                                <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                    <h2 class="text-primary">Bienvenido, deportista <?= $nombre ?>.</h2>
                                    
                                    <p class="text-gray-700 mb-0">CATEGORIA: <?=$categoria?></p>
                                    <p>Entrenador: 
                                <a href="./configuracion/download.php "><?= $nombre_entrenador ?>
                                </a>
                            </p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="../Assets/img/illustrations/at-work.svg" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CARDS 1 -->
        <div class="row">
            <!-- Código HTML para mostrar las tarjetas -->
            <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                <h3 class="text-primary">Tabla de Datos</h3>
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php
                // Obtener el ID del deportista que ha iniciado sesión
                $id_usuario = $_SESSION['user_id'];

                // Consulta SQL para obtener los detalles del deportista
                $sql = "SELECT d.* FROM tab_detalles d
        INNER JOIN tab_deportistas dep ON d.ID_DEPORTISTA = dep.ID_DEPORTISTA
        WHERE dep.ID_USUARIO = :id_usuario
        ORDER BY d.FECHA_INGRESO DESC";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();

                // Verificar si se encontraron resultados
                if ($stmt->rowCount() > 0) {
                    echo '<div class="card mb-4">';
                    echo '<div class="card-body">';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                    echo '<thead><tr><th>Fecha de Ingreso</th><th>Número de Camisa</th><th>Altura</th><th>Peso</th><th>IMC</th></tr></thead>';
                    echo '<tbody>';

                    $imc_data = array();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $altura = floatval($row['ALTURA']) / 100; // Convertir cm a m
                        $peso = floatval($row['PESO']);
                        $imc = $peso / ($altura * $altura);
                        $imc = round($imc, 2);

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['FECHA_INGRESO']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['NUMERO_CAMISA']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['ALTURA']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['PESO']) . '</td>';
                        echo '<td>' . $imc . '</td>';
                        echo '</tr>';

                        $imc_data[] = array(
                            'fecha' => $row['FECHA_INGRESO'],
                            'imc' => $imc
                        );
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                    // Modal para la gráfica
                    echo '<div class="modal fade" id="imcModal" tabindex="-1" aria-labelledby="imcModalLabel" aria-hidden="true">';
                    echo '<div class="modal-dialog modal-lg">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="imcModalLabel">Gráfica de IMC</h5>';
                    echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                    echo '</div>';
                    echo '<div class="modal-body">';
                    echo '<canvas id="imcChart"></canvas>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">No se encontraron detalles</div>';
                }
                ?>
            </div>
        </div>

    </div>
</main>
<?php
include './includes/footer.php';
?>
