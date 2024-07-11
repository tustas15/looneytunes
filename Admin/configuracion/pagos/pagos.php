<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

include '../../Includespro/header.php';
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
?>

<main>
    <!-- Mostrar Mensajes -->
    <?php
    if (isset($_SESSION['message'])) {
        $message_type = $_SESSION['message_type'] ?? 'info';
        echo '<div class="container mt-3">';
        echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['message'] . '</div>';
        echo '</div>';
        // Borrar el mensaje de la sesión después de mostrarlo
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <!--<div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </h1>
                        <div class="page-header-subtitle">Descripción general del panel y resumen de contenido</div>
                    </div>
                </div>-->
            </div>
        </div>
    </header>
    <!-- Page Heading -->
    <div class="container-xl px-4 mt-n10">
        <div class="row align-items-center justify-content-between mb-4">
            <div class="col-auto">
                <h1 class="h3 mb-0 text-gray-800">Gestión de Pagos</h1>
            </div>
            
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Registrar Nuevo Pago</h6>
            </div>
            <div class="card-body">
                <form id="formulario-pago" method="POST">
                    <div class="form-group">
                        <label for="cedula">Cédula del Representante:</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre_representante">Nombre del Representante:</label>
                        <input type="text" class="form-control" id="nombre_representante" name="nombre_representante" required>
                    </div>

                    <div class="form-group">
                        <label for="tipo_pago">Método de Pago:</label>
                        <select class="form-control" id="tipo_pago" name="tipo_pago" onchange="toggleFields(this.value)" required>
                            <option value="">Seleccione...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div id="efectivoFields" style="display:none;">
                        <div class="form-group">
                            <label for="nombre_deportista_efectivo">Nombre del Deportista:</label>
                            <input type="text" class="form-control" id="nombre_deportista_efectivo" name="nombre_deportista_efectivo">
                        </div>

                        <div class="form-group">
                            <label for="motivo_efectivo">Motivo:</label>
                            <input type="text" class="form-control" id="motivo_efectivo" name="motivo_efectivo">
                        </div>

                        <div class="form-group">
                            <label for="monto_efectivo">Monto:</label>
                            <input type="number" step="0.01" class="form-control" id="monto_efectivo" name="monto_efectivo">
                        </div>

                        <div class="form-group">
                            <label for="fecha_efectivo">Fecha:</label>
                            <input type="date" class="form-control fecha-actual" id="fecha_efectivo" name="fecha_efectivo">
                        </div>
                    </div>

                    <div id="transferenciaFields" style="display:none;">
                        <div class="form-group">
                            <label for="nombre_deportista_transferencia">Nombre del Deportista:</label>
                            <input type="text" class="form-control" id="nombre_deportista_transferencia" name="nombre_deportista_transferencia">
                        </div>

                        <div class="form-group">
                            <label for="motivo_transferencia">Motivo:</label>
                            <input type="text" class="form-control" id="motivo_transferencia" name="motivo_transferencia">
                        </div>

                        <div class="form-group">
                            <label for="banco">Nombre del Banco:</label>
                            <select class="form-control" id="banco" name="banco">
                                <option value="">Seleccione un banco...</option>
                                <option value="Pichincha">Pichincha</option>
                                <option value="Austro">Austro</option>
                                <option value="Pacifico">Pacífico</option>
                                <option value="Produbanco">Produbanco</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="monto_transferencia">Monto:</label>
                            <input type="number" step="0.01" class="form-control" id="monto_transferencia" name="monto_transferencia">
                        </div>

                        <div class="form-group">
                            <label for="fecha_transferencia">Fecha:</label>
                            <input type="date" class="form-control fecha-actual" id="fecha_transferencia" name="fecha_transferencia">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-registrar">Registrar Pago</button>
                </form>

                <div id="mensaje-confirmacion" class="alert alert-success mt-3" style="display: none;">
                    Pago registrado correctamente
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div id="historial_pagos">
                <div class="card-header py-3">
                </div>
            </div>
        </div>
    </div>

    <?php
    include '../../Includespro/footer.php';
    ?>
</main>