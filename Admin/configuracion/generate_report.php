<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['report_type'])) {
        $reportType = $_POST['report_type'];

        try {
            switch ($reportType) {
                case 'administradores':
                    $sql = "SELECT * FROM tab_administradores";
                    break;
                case 'entrenadores':
                    $sql = "SELECT * FROM tab_entrenadores";
                    break;
                case 'representantes':
                    $sql = "SELECT * FROM tab_representantes";
                    break;
                case 'deportistas':
                    $sql = "SELECT * FROM tab_deportistas";
                    break;
                default:
                    throw new Exception("Tipo de informe no válido");
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Generar el informe (esto puede ser exportado a CSV, PDF, etc.)
            // Por ahora, vamos a mostrar los resultados como una tabla en la misma página

            echo "<h2>Informe de $reportType</h2>";
            echo "<table class='table'>";
            echo "<thead><tr>";

            if (!empty($results)) {
                foreach ($results[0] as $key => $value) {
                    echo "<th>$key</th>";
                }

                echo "</tr></thead><tbody>";

                foreach ($results as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No se encontraron registros para el tipo de informe seleccionado.</p>";
            }
        } catch (Exception $e) {
            echo "Error al generar el informe: " . $e->getMessage();
        }

        $conn = null;
    } else {
        echo "Por favor, seleccione un tipo de informe.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
