<?php
require_once('/xampp/htdocs/tutorial/conexion/conexion.php');

// Verificar si se recibió un parámetro ID_USUARIO en la URL
if (isset($_GET['ID_USUARIO'])) {
    $id_representante = $_GET['ID_USUARIO'];

    try {
        // Consulta SQL para obtener el perfil del representante
        $sql = "SELECT u.USUARIO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CELULAR_REPRE, r.CORREO_REPRE, r.DIRECCION_REPRE, r.CEDULA_REPRE
                FROM tab_usuarios u
                INNER JOIN tab_representantes r ON u.ID_USUARIO = r.ID_USUARIO
                WHERE u.ID_USUARIO = :id_representante";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró un representante con el ID dado
        if ($resultado) {
            // Mostrar información del perfil del representante
            echo "<h1>Perfil del Representante</h1>";
            echo "<p><strong>Usuario:</strong> " . htmlspecialchars($resultado['USUARIO']) . "</p>";
            echo "<p><strong>Nombre:</strong> " . htmlspecialchars($resultado['NOMBRE_REPRE']) . "</p>";
            echo "<p><strong>Apellido:</strong> " . htmlspecialchars($resultado['APELLIDO_REPRE']) . "</p>";
            echo "<p><strong>Celular:</strong> " . htmlspecialchars($resultado['CELULAR_REPRE']) . "</p>";
            echo "<p><strong>Correo:</strong> " . htmlspecialchars($resultado['CORREO_REPRE']) . "</p>";
            echo "<p><strong>Dirección:</strong> " . htmlspecialchars($resultado['DIRECCION_REPRE']) . "</p>";
            echo "<p><strong>Cédula:</strong> " . htmlspecialchars($resultado['CEDULA_REPRE']) . "</p>";

            // Consulta SQL para obtener los deportistas a cargo del representante
            $sql_deportistas = "SELECT d.ID_USUARIO, d.NOMBRE_DEPO, d.APELLIDO_DEPO
                                FROM tab_deportistas d
                                INNER JOIN tab_representantes_deportistas rd ON d.ID_USUARIO = rd.ID_DEPORTISTA
                                WHERE rd.ID_REPRESENTANTE = :id_representante";

            // Preparar la consulta
            $stmt_deportistas = $conn->prepare($sql_deportistas);
            $stmt_deportistas->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt_deportistas->execute();

            // Obtener el resultado
            $deportistas = $stmt_deportistas->fetchAll(PDO::FETCH_ASSOC);

            // Mostrar la lista de deportistas
            if ($deportistas) {
                echo "<h2>Deportistas a cargo</h2>";
                echo "<ul>";
                foreach ($deportistas as $deportista) {
                    echo "<li>" . htmlspecialchars($deportista['NOMBRE_DEPO']) . " " . htmlspecialchars($deportista['APELLIDO_DEPO']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No hay deportistas a cargo de este representante.</p>";
            }

        } else {
            echo "No se encontró un representante con ID " . $id_representante;
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cerrar conexión
    $conn = null;

} else {
    echo "No se proporcionó un ID_USUARIO válido en la URL.";
}
?>
