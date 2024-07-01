<?php
include '../Admin/configuracion/conexion.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si se recibió la cédula
if (isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];

    // Consulta para obtener los datos del deportista y sus detalles
    $query = "SELECT d.NOMBRE_DEPO, d.APELLIDO_DEPO, det.DETALLE
              FROM tab_deportistas d
              LEFT JOIN tab_detalles det ON d.ID_DEPORTISTA = det.ID_DEPORTISTA
              WHERE d.CEDULA_DEPO = ?";
    
    // Preparar la consulta
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$cedula]);
        
        // Mostrar resultados en una tabla
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<h4>Datos personales</h4>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Nombre</th><th>Apellido</th></tr></thead>';
        echo '<tbody>';
        
        // Mostrar datos personales
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr><td>' . htmlspecialchars($row['NOMBRE_DEPO'], ENT_QUOTES) . '</td><td>' . htmlspecialchars($row['APELLIDO_DEPO'], ENT_QUOTES) . '</td></tr>';
        }
        
        echo '</tbody></table></div>';
        
        // Mostrar detalles
        echo '<div class="col-md-6">';
        echo '<h4>Datos generales</h4>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Detalle</th></tr></thead>';
        echo '<tbody>';
        
        // Mostrar detalles del deportista
        $stmt->execute([$cedula]); // Ejecutar de nuevo para asegurar que el cursor está al principio
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr><td>' . htmlspecialchars($row['DETALLE'], ENT_QUOTES) . '</td></tr>';
        }
        
        echo '</tbody></table></div></div>';
        
        // Cerrar la consulta
        $stmt = null;
    } catch (PDOException $e) {
        echo "Error en la preparación de la consulta: " . $e->getMessage();
    }
} else {
    echo "No se recibió la cédula del deportista";
}

$conn = null; // Cerrar la conexión al finalizar

