<?php
session_start();
include '../conexion/conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deportista_id']) && isset($_POST['csrf_token'])) {
        // Verificar el token CSRF
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "Token CSRF inválido";
            exit;
        }

        $deportista_id = $_POST['deportista_id'];

        // Consulta para obtener la información del deportista
        $query = "SELECT NOMBRE_DEPO, APELLIDO_DEPO, FECHA_NACIMIENTO, CEDULA_DEPO, NUMERO_CELULAR, GENERO
                  FROM tab_deportistas
                  WHERE ID_DEPORTISTA = ?";
        
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->execute([$deportista_id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($info) {
                // Mostrar la información del deportista
                echo "<table class='table'>";
                echo "<tr><th>Nombre</th><td>" . htmlspecialchars($info['NOMBRE_DEPO'], ENT_QUOTES) . "</td></tr>";
                echo "<tr><th>Apellido</th><td>" . htmlspecialchars($info['APELLIDO_DEPO'], ENT_QUOTES) . "</td></tr>";
                echo "<tr><th>Fecha de Nacimiento</th><td>" . htmlspecialchars($info['FECHA_NACIMIENTO'], ENT_QUOTES) . "</td></tr>";
                echo "<tr><th>Cédula</th><td>" . htmlspecialchars($info['CEDULA_DEPO'], ENT_QUOTES) . "</td></tr>";
                echo "<tr><th>Celular</th><td>" . htmlspecialchars($info['NUMERO_CELULAR'], ENT_QUOTES) . "</td></tr>";
                echo "<tr><th>Género</th><td>" . htmlspecialchars($info['GENERO'], ENT_QUOTES) . "</td></tr>";
                echo "</table>";
            } else {
                echo "No se encontró información del deportista.";
            }
        } else {
            echo "Error en la preparación de la consulta.";
        }
    } else {
        echo "Método de solicitud no permitido o parámetros faltantes.";
    }
} catch (Exception $e) {
    echo "Ocurrió un error: " . $e->getMessage();
}
?>
