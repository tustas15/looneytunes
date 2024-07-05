<?php
session_start();
include '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deportista_id']) && isset($_POST['csrf_token'])) {
    // Verificar el token CSRF
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Token CSRF inválido";
        exit;
    }

    $deportista_id = $_POST['deportista_id'];

    // Consulta para obtener el rendimiento del deportista
    $query = "SELECT NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO
              FROM tab_detalles
              WHERE ID_USUARIO = ?";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->execute([$deportista_id]);
        $rendimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rendimiento) {
            // Mostrar el rendimiento del deportista
            echo "<table class='table'>";
            echo "<tr><th>Número de Camisa</th><td>" . htmlspecialchars($rendimiento['NUMERO_CAMISA'], ENT_QUOTES) . "</td></tr>";
            echo "<tr><th>Altura</th><td>" . htmlspecialchars($rendimiento['ALTURA'], ENT_QUOTES) . "</td></tr>";
            echo "<tr><th>Peso</th><td>" . htmlspecialchars($rendimiento['PESO'], ENT_QUOTES) . "</td></tr>";
            echo "<tr><th>Fecha de Ingreso</th><td>" . htmlspecialchars($rendimiento['FECHA_INGRESO'], ENT_QUOTES) . "</td></tr>";
            echo "</table>";
        } else {
            echo "No se encontró rendimiento del deportista.";
        }
    } else {
        echo "Error en la preparación de la consulta.";
    }
}

