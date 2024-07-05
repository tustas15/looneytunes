<?php 
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

$id_usuario = $_SESSION['user_id']; 

// Consulta para obtener datos de tab_temp_deportistas y tab_detalles
$sql = "WITH Ordenados AS (
    SELECT td.ID_TEMP_DEPORTISTA, td.NOMBRE_DEPO, td.APELLIDO_DEPO, td.CEDULA_DEPO, 
           td.FECHA_NACIMIENTO, td.NUMERO_CELULAR, td.GENERO,
           d.NUMERO_CAMISA, d.ALTURA, d.PESO, d.FECHA_INGRESO,
           ROW_NUMBER() OVER (PARTITION BY td.ID_DEPORTISTA ORDER BY d.FECHA_INGRESO DESC) AS fila
    FROM tab_temp_deportistas td 
    LEFT JOIN tab_detalles d ON td.ID_DEPORTISTA = d.ID_DEPORTISTA
    WHERE td.ID_USUARIO = :id_usuario
)
SELECT ID_TEMP_DEPORTISTA, NOMBRE_DEPO, APELLIDO_DEPO, CEDULA_DEPO, 
       FECHA_NACIMIENTO, NUMERO_CELULAR, GENERO,
       NUMERO_CAMISA, ALTURA, PESO, FECHA_INGRESO
FROM Ordenados
WHERE fila = 1";

$stmt = $conn->prepare($sql); 
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); 
$stmt->execute(); 

$deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?> 

<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Detalles de Deportistas</title> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <link rel="icon" type="image/png" href="../img/logo.png"> 
    <style> 
        .table-responsive { max-height: 500px; overflow-y: auto; }
        .regresar-btn {
    position: absolute;
    top: 10px; 
    right: 10px; 
    padding: 8px 16px;
    background-color: blue;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.regresar-btn:hover {
    background-color: darkblue;
}
    </style> 
</head> 
<body>
    <a href="../entrenador/indexentrenador.php" class="regresar-btn">Regresar</a>
    <div class="container my-5"> 
        <h1>Detalles de Deportistas</h1> 
        <div class="table-responsive"> 
            <table class="table table-striped table-hover"> 
                <thead> 
                    <tr> 
                        <th>Nombre</th> 
                        <th>Apellido</th> 
                        <th>Cédula</th> 
                        <th>Fecha de Nacimiento</th> 
                        <th>Número de Celular</th> 
                        <th>Género</th> 
                        <th>Número de Camiseta</th>
                        <th>Altura</th>
                        <th>Peso</th>
                        <th>Fecha de Ingreso</th>
                    </tr> 
                </thead> 
                <tbody> 
                    <?php foreach ($deportistas as $deportista): ?> 
                        <tr> 
                            <td><?= htmlspecialchars($deportista['NOMBRE_DEPO']) ?></td> 
                            <td><?= htmlspecialchars($deportista['APELLIDO_DEPO']) ?></td> 
                            <td><?= htmlspecialchars($deportista['CEDULA_DEPO']) ?></td> 
                            <td><?= htmlspecialchars($deportista['FECHA_NACIMIENTO']) ?></td> 
                            <td><?= htmlspecialchars($deportista['NUMERO_CELULAR']) ?></td> 
                            <td><?= htmlspecialchars($deportista['GENERO']) ?></td> 
                            <td><?= htmlspecialchars($deportista['NUMERO_CAMISA'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($deportista['ALTURA'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($deportista['PESO'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($deportista['FECHA_INGRESO'] ?? 'N/A') ?></td>
                        </tr> 
                    <?php endforeach; ?> 
                </tbody> 
            </table> 
        </div>

        <h2 class="mt-5">Comparar Detalles</h2>
        <div class="form-group">
            <label for="select-alumno">Seleccione un Deportista:</label>
            <select class="form-control" id="select-alumno">
                <option value="">Seleccione un Deportista</option>
                <?php foreach ($deportistas as $deportista): ?>
                <option value="<?= $deportista['ID_TEMP_DEPORTISTA'] ?>">
                    <?= htmlspecialchars($deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO'] . ' - ' . $deportista['CEDULA_DEPO']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="compare-details" class="mt-3"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-alumno').on('change', function() {
                var selectedId = $(this).val();
                if (selectedId) {
                    $.ajax({
                        url: 'compare_details.php',
                        type: 'POST',
                        data: {id_temp_deportista: selectedId},
                        success: function(response) {
                            $('#compare-details').html(response);
                        },
                        error: function() {
                            alert('Error al cargar los detalles de comparación.');
                        }
                    });
                } else {
                    $('#compare-details').html('');
                }
            });
        });
    </script>
</body> 
</html>