<?php
require('../conexion.php');
//error_log("POST data en get_options.php: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    $sql = "";
    if ($tipo == 'categoria') {
        $sql = "SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias";
    } elseif ($tipo == 'deportista') {
        $sql = "SELECT ID_DEPORTISTA, concat( NOMBRE_DEPO, '  ',APELLIDO_DEPO,'') FROM tab_deportistas";
    } elseif ($tipo == 'representante') {
        $sql = "SELECT ID_REPRESENTANTE, concat(NOMBRE_REPRE,'  ',APELLIDO_REPRE,'') FROM tab_representantes";
    } else {
        echo "<option value=''>Tipo de informe no válido</option>";
        exit;
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $options = "<option value=''>Seleccione una opción</option>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row[array_keys($row)[0]];
            $nombre = $row[array_keys($row)[1]];
            $options .= "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($nombre) . "</option>";
        }

        echo $options;
    } catch (PDOException $e) {
        echo "<option value=''>Error: " . $e->getMessage() . "</option>";
    }
} else {
    echo "<option value=''>Método de solicitud no válido</option>";
}
?>