<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php'); // Asegúrate de que esta ruta sea correcta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['nombre_archivo'])) {
    $archivo = $_FILES['nombre_archivos'];
    $nombreArchivo = $archivo['name'];
    $rutaTemporal = $archivo['tmp_name'];
    $rutaDestino = 'C:/xampp/htdocs/looneytunes/Admin/configuracion/pagos/comprobantes' . $nombreArchivo; // Ajusta la ruta según tu estructura

   // Mueve el archivo a la carpeta de destino
   
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=looneytunes', 'root', ''); // Ajusta según tu configuración
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
 

