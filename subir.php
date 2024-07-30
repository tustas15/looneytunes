<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejo del formulario de transferencia
    $banco = $_POST['banco_transferencia'];
    $numero_factura = $_POST['numero_factura'];
    $cuenta_origen = $_POST['cuenta_origen'];
    $cuenta_destino = $_POST['cuenta_destino'];
    $fecha_transferencia = $_POST['fecha_transferencia'];
    $monto = $_POST['monto_transferencia'];
    $motivo = $_POST['motivo_transferencia'];

    // Manejo del archivo
    if (isset($_FILES['comprobante_transferencia'])) {
        $file = $_FILES['comprobante_transferencia'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'pdf');

        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) { // Limite de tamaño de 5MB
                    $fileNameNew = uniqid('', true) . "." . $fileExt;
                    $fileDestination = 'uploads/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                } else {
                    echo "El archivo es demasiado grande.";
                    exit();
                }
            } else {
                echo "Hubo un error al subir el archivo.";
                exit();
            }
        } else {
            echo "Tipo de archivo no permitido.";
            exit();
        }
    }

    // Inserción de datos en la base de datos
    $sql = "INSERT INTO pagos_transferencias (banco, numero_factura, cuenta_origen, cuenta_destino, fecha_transferencia, monto, motivo, comprobante) 
            VALUES (:banco, :numero_factura, :cuenta_origen, :cuenta_destino, :fecha_transferencia, :monto, :motivo, :comprobante)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':banco' => $banco,
        ':numero_factura' => $numero_factura,
        ':cuenta_origen' => $cuenta_origen,
        ':cuenta_destino' => $cuenta_destino,
        ':fecha_transferencia' => $fecha_transferencia,
        ':monto' => $monto,
        ':motivo' => $motivo,
        ':comprobante' => $fileNameNew ?? null
    ]);

    echo "Pago registrado exitosamente.";
}
?>
