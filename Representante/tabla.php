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

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['user_id'];

try {
    // Obtener el ID_REPRESENTANTE correspondiente al ID_USUARIO
    $stmt = $conn->prepare("SELECT ID_REPRESENTANTE FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $id_representante = $stmt->fetchColumn();

    // Verificar si el ID_REPRESENTANTE fue encontrado
    if (!$id_representante) {
        echo "No se encontró el representante para este usuario.";
        exit();
    }

    // Paso 2: Obtener la lista de deportistas asociados al representante
    $sql_deportistas = "
        SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO 
        FROM tab_deportistas d
        INNER JOIN tab_representantes_deportistas rd 
            ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
        WHERE rd.ID_REPRESENTANTE = :id_representante
    ";

    $stmt = $conn->prepare($sql_deportistas);
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se obtuvieron deportistas
    if (empty($deportistas)) {
        echo "No hay deportistas asociados a este representante.";
        exit();
    }

    // Obtener los pagos asociados al representante
    $stmt = $conn->prepare("
        SELECT p.ID_PAGO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, p.FECHA_PAGO, p.MONTO, p.MOTIVO, p.METODO_PAGO
        FROM tab_pagos p
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
        ORDER BY d.NOMBRE_DEPO ASC, d.APELLIDO_DEPO ASC
    ");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit();
}

// Incluir el encabezado (header)
include './Includes/header.php';
?>

<main>
    <header class="page-header bg-white pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <h1 class="text-dark">Tabla de Pagos</h1>
                <p class="text-muted mb-0">Aquí puedes ver la información detallada de los pagos realizados.</p>
            </div>
        </div>
    </header>
    
    <!-- Contenido principal de la página -->
    <div class="container-xl px-4 mt-n10">
        <!-- Ejemplo de tabla para mostrar pagos -->
        <div class="card mb-4">
            <div class="card-header">Lista de Pagos</div>
            <div class="card-body">

                <!-- Botón para generar PDF -->
                <div class="mb-3">
                    <a href="./generar_pdf.php" class="btn btn-success">Generar PDF</a>
                </div>
                
                <!-- Campo de búsqueda -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar...">
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Deportista</th>
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pago['FECHA_PAGO'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars('$' . number_format($pago['MONTO'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pago['METODO_PAGO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pago['MOTIVO'], ENT_QUOTES, 'UTF-8'); ?></td>
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
