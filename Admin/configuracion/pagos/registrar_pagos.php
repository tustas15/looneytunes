<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=looneytunes', 'usuario', 'contraseña');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $tipo_pago = $_POST['tipo_pago'];
        $fecha_pago = $_POST['fecha_pago'];
        $monto = $_POST['monto'];
        $banco_destino = isset($_POST['banco_destino']) ? $_POST['banco_destino'] : null;
        $entidad_financiera = isset($_POST['entidad_financiera']) ? $_POST['entidad_financiera'] : null;
        $numero_factura = isset($_POST['numero_factura']) ? $_POST['numero_factura'] : null;
        $nombre_archivo = isset($_FILES['comprobante']) ? $_FILES['comprobante']['name'] : null;

        $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, TIPO_PAGO, MONTO, FECHA, NOMBRE_ARCHIVO, BANCO_DESTINO, ENTIDAD_FINANCIERA, NUMERO_FACTURA) VALUES (:id_representante, :id_deportista, :tipo_pago, :monto, :fecha_pago, :nombre_archivo, :banco_destino, :entidad_financiera, :numero_factura)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id_representante' => $_POST['id_representante'],
            ':id_deportista' => $_POST['id_deportista'],
            ':tipo_pago' => $tipo_pago,
            ':monto' => $monto,
            ':fecha_pago' => $fecha_pago,
            ':nombre_archivo' => $nombre_archivo,
            ':banco_destino' => $banco_destino,
            ':entidad_financiera' => $entidad_financiera,
            ':numero_factura' => $numero_factura
        ]);

        if ($nombre_archivo) {
            move_uploaded_file($_FILES['comprobante']['tmp_name'], 'uploads/' . $nombre_archivo);
        }

        header('Location: pagos.php?mensaje=' . urlencode('Pago registrado correctamente.'));
        exit();
    } catch (PDOException $e) {
        header('Location: pagos.php?mensaje=' . urlencode('Error al registrar el pago: ' . $e->getMessage()));
        exit();
    }
}
?>
