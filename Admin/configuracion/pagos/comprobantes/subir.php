<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Asegúrate de que esta ruta sea correcta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['comprobante'])) {
    $archivo = $_FILES['comprobante'];
    $nombreArchivo = $archivo['name'];
    $rutaTemporal = $archivo['tmp_name'];
    $rutaDestino = 'C:/xampp/htdocs/Looneytunes/Admin/configuracion/pagos/comprobantes' . $nombreArchivo; // Ajusta la ruta según tu estructura

   // Mueve el archivo a la carpeta de destino
   if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=looneytunes', 'usuario', 'contraseña'); // Ajusta según tu configuración
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inserta la ruta del archivo en la base de datos
    $sql = "INSERT INTO tab_pagos (nombre_archivo) VALUES (:nombre_archivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre_archivo' => $rutaDestino]);

    // Redirecciona con un mensaje en la URL
    header('Location: confirmacion.php?mensaje=Pago registrado correctamente');
    exit();
} else {
    // Redirecciona con un mensaje de error
    header('Location: confirmacion.php?mensaje=Error al mover el archivo');
    exit();
}
} else {
header('Location: confirmacion.php?mensaje=No se ha enviado ningún archivo');
exit();
}
?>
