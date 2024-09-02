<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (!isset($_SESSION['user_id'])) {
    die("ID de usuario no disponible en la sesión.");
}

$id_usuario = $_SESSION['user_id'];

try {
    $sql_representante = "SELECT ID_REPRESENTANTE FROM tab_representantes WHERE ID_USUARIO = :id_usuario";
    $stmt = $conn->prepare($sql_representante);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $representante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$representante) {
        echo "<tr><td colspan='6'>No se encontró el representante para el usuario actual.</td></tr>";
        exit;
    }

    $id_representante = $representante['ID_REPRESENTANTE'];

    $sql = "
        SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
               p.FECHA_PAGO, p.METODO_PAGO, p.MONTO, p.MOTIVO
        FROM tab_pagos p
        INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
        AND p.REGISTRADO_POR NOT IN ('ADMIN', 'DEPO')
        ORDER BY p.FECHA_PAGO DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_representante' => $id_representante]);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($pagos)) {
        foreach ($pagos as $pago) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO']) . "</td>";
            echo "<td>" . htmlspecialchars($pago['NOMBRE_REPRE'] . ' ' . $pago['APELLIDO_REPRE']) . "</td>";
            echo "<td>" . htmlspecialchars($pago['METODO_PAGO']) . "</td>";
            echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($pago['FECHA_PAGO']))) . "</td>";
            echo "<td>" . htmlspecialchars($pago['MOTIVO']) . "</td>";
            echo "<td>" . htmlspecialchars('$' . number_format($pago['MONTO'], 2)) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Error al obtener los pagos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";

    include './includes/header.php';

}
?>




<div class="table-responsive">
    <table id="datatablesSimple" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Deportista</th>
                <th>Representante</th>
                <th>Tipo de Pago</th>
                <th>Fecha</th>
                <th>Motivo</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Deportista</th>
                <th>Representante</th>
                <th>Tipo de Pago</th>
                <th>Fecha</th>
                <th>Motivo</th>
                <th>Monto</th>
            </tr>
        </tfoot>
        <tbody>
            <?php include('tabla.php'); ?>
        </tbody>
    </table>
</div>
