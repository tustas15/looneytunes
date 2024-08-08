<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (isset($_GET['id'])) {
    $id_pago = $_GET['id'];

    try {
        $sql = "SELECT * FROM tab_pagos WHERE ID_PAGO = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_pago]);
        $pago = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pago) {
            ?>
            <form id="editPaymentForm">
                <input type="hidden" name="id_pago" value="<?php echo $pago['ID_PAGO']; ?>">
                <div class="form-group">
                    <label for="monto">Monto:</label>
                    <input type="number" id="monto" name="monto" value="<?php echo $pago['MONTO']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $pago['FECHA_PAGO']; ?>" required>
                </div>
                <!-- Agrega más campos según sea necesario -->
                <button type="submit">Guardar Cambios</button>
            </form>
            <script>
                $('#editPaymentForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'actualizar.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert('Pago actualizado con éxito');
                                $('#editFormContainer').empty(); // Limpiar el contenedor
                                // Opcionalmente, recargar la tabla o realizar otras acciones
                                // Ejemplo para recargar la tabla:
                                // table.ajax.reload();
                            } else {
                                alert('Error al actualizar el pago: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error al procesar la solicitud');
                        }
                    });
                });
            </script>
            <?php
        } else {
            echo "Pago no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de pago no proporcionado.";
}
?>
