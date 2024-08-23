<?php
require_once('../admin/configuracion/conexion.php');
if (isset($_POST['filtro'])) {
    $filtro = $_POST['filtro'];

    if ($filtro == 'categoria') {
        // Consulta para obtener categorías desde la base de datos
        $query = "SELECT ID_CATEGORIA, CATEGORIA FROM tab_categorias"; // Asegúrate de tener esta tabla en tu base de datos
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['ID_CATEGORIA'] . '">' . $row['CATEGORIA'] . '</option>';
        }
    } else if ($filtro == 'deportista') {
        // Consulta para obtener deportistas desde la base de datos
        $query = "SELECT ID_DEPORTISTA, NOMBRE_DEPO FROM tab_deportistas"; // Asegúrate de tener esta tabla en tu base de datos
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id_deportista'] . '">' . $row['nombre_depo'] . '</option>';
        }
    }
}
?>
