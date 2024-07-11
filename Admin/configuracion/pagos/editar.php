<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verificar si se ha proporcionado un ID_PAGO válido
if (isset($_GET['id'])) {
    $idPago = $_GET['id'];

    // Consultar el pago específico por ID_PAGO
    $sql = "SELECT p.ID_PAGO, p.FECHA, p.TIPO_PAGO, p.MONTO, p.MOTIVO, p.BANCO, r.nombre_repre, d.nombre_depo
            FROM tab_pagos p
            INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
            INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
            WHERE p.ID_PAGO = :idPago";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idPago', $idPago, PDO::PARAM_INT);
    $stmt->execute();
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el pago
    if ($pago) {
        include '../../Includespro/header.php';
?>

<!-- Contenido del formulario -->
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Editar Pago</h1>
    <form action="actualizar.php" method="POST">
        <input type="hidden" name="idPago" value="<?php echo $pago['ID_PAGO']; ?>">
        <div class="form-group">
            <label for="nombre_repre">Representante:</label>
            <input type="text" class="form-control" id="nombre_repre" name="nombre_repre"
                value="<?php echo htmlspecialchars($pago['nombre_repre']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="nombre_depo">Deportista:</label>
            <input type="text" class="form-control" id="nombre_depo" name="nombre_depo"
                value="<?php echo htmlspecialchars($pago['nombre_depo']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" class="form-control" id="fecha" name="fecha"
                value="<?php echo htmlspecialchars($pago['FECHA']); ?>" required>
        </div>
        <div class="form-group">
            <label for="tipo_pago">Tipo de Pago:</label>
            <select class="form-control" id="tipo_pago" name="tipo_pago" required onchange="toggleBancoField()">
                <option value="Efectivo" <?php echo $pago['TIPO_PAGO'] == 'Efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                <option value="Transferencia" <?php echo $pago['TIPO_PAGO'] == 'Transferencia' ? 'selected' : ''; ?>>Transferencia</option>
            </select>
        </div>
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input type="number" step="0.01" class="form-control" id="monto" name="monto"
                value="<?php echo htmlspecialchars($pago['MONTO']); ?>" required>
        </div>
        <div class="form-group">
            <label for="motivo">Motivo:</label>
            <input type="text" class="form-control" id="motivo" name="motivo"
                value="<?php echo htmlspecialchars($pago['MOTIVO']); ?>" required>
        </div>
        <div class="form-group" id="bancoField" style="display: <?php echo $pago['TIPO_PAGO'] == 'Transferencia' ? 'block' : 'none'; ?>">
            <label for="banco">Banco:</label>
            <select class="form-control" id="banco" name="banco">
        <option value="">Seleccione un banco...</option>
        <option value="Pichincha" <?php echo $pago['BANCO'] == 'Pichincha' ? 'selected' : ''; ?>>Pichincha</option>
        <option value="Austro" <?php echo $pago['BANCO'] == 'Austro' ? 'selected' : ''; ?>>Austro</option>
        <option value="Pacifico" <?php echo $pago['BANCO'] == 'Pacifico' ? 'selected' : ''; ?>>Pacífico</option>
        <option value="Produbanco" <?php echo $pago['BANCO'] == 'Produbanco' ? 'selected' : ''; ?>>Produbanco</option>
    </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Pago</button>
    </form>
</div>

<script>
function toggleBancoField() {
    var tipoPago = document.getElementById('tipo_pago').value;
    var bancoField = document.getElementById('bancoField');
    if (tipoPago === 'Transferencia') {
        bancoField.style.display = 'block';
        document.getElementById('banco').required = true;
    } else {
        bancoField.style.display = 'none';
        document.getElementById('banco').required = false;
    }
}

// Llamar a la función al cargar la página para establecer el estado inicial
toggleBancoField();
</script>

<?php
        include '../../Includespro/footer.php';
    } else {
        echo "Pago no encontrado.";
    }
} else {
    echo "ID de pago no especificado.";
}
?>