<?php
// Conexión a la base de datos
$host = '127.0.0.1';
$db = 'looneytunes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Obtener los representantes
$representantes = $pdo->query('SELECT ID_REPRESENTANTE, NOMBRE_REPRE, APELLIDO_REPRE FROM tab_representantes')->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Pagos</title>
</head>
<body>
    <h1>Registro de Pagos</h1>
    <form action="procesar_pago.php" method="post">
        <label for="representante">Seleccione Representante:</label>
        <select name="representante" id="representante">
            <option value="">Seleccione un representante</option> <!-- Opcional: mensaje predeterminado -->
            <?php foreach ($representantes as $representante): ?>
                <option value="<?= $representante['ID_REPRESENTANTE'] ?>"><?= $representante['NOMBRE_REPRE'] ?> <?= $representante['APELLIDO_REPRE'] ?></option>
            <?php endforeach; ?>
        </select>
        
        <div id="deportistas">
            <!-- Aquí se cargarán los deportistas asociados al representante seleccionado -->
            <p>Selecciona un representante para ver los deportistas asociados.</p> <!-- Mensaje predeterminado -->
        </div>
        
        <label for="tipo_pago">Tipo de Pago:</label>
        <input type="text" id="tipo_pago" name="tipo_pago" required>
        
        <label for="comprobante">Comprobante:</label>
        <input type="text" id="comprobante" name="comprobante" required>
        
        <input type="submit" value="Registrar Pago">
    </form>

    <script>
        document.getElementById('representante').addEventListener('change', function() {
            var representanteId = this.value;
            console.log("ID del Representante seleccionado:", representanteId); // Depuración
            
            if (representanteId) {
                fetch('obtener_deportistas.php?id=' + representanteId)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Datos de deportistas:", data); // Depuración
                        var deportistas = document.getElementById('deportistas');
                        deportistas.innerHTML = '<label for="deportista">Seleccione Deportista:</label><select name="deportista" id="deportista">';
                        if (data.length > 0) {
                            data.forEach(deportista => {
                                deportistas.innerHTML += '<option value="' + deportista.ID_usuario + '">' + deportista.NOMBRE_DEPO + ' ' + deportista.APELLIDO_DEPO + '</option>';
                            });
                        } else {
                            deportistas.innerHTML = '<p>No hay deportistas asociados a este representante.</p>';
                        }
                        deportistas.innerHTML += '</select>';
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('deportistas').innerHTML = '<p>Selecciona un representante para ver los deportistas asociados.</p>';
            }
        });
    </script>
</body>
</html>
