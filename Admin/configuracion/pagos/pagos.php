<?php
// Conexión a la base de datos
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
include '../../Includespro/header.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-end mb-4">
        <a href="../configuracion/respaldo/downloadFile.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generar Respaldo</a>
    </div>
    <form id="formulario-pago" action="procesar_pagos.php" method="post" enctype="multipart/form-data">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Registrar Nuevo Pago</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="apellido_representante" class="form-label">Representante</label>
                    <select id="apellido_representante" class="form-select" required>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cedula_representante" class="form-label">Cédula del Representante</label>
                    <input type="text" class="form-control" id="cedula_representante" readonly>
                </div>
                <div class="mb-3">
                    <label for="deportista" class="form-label">Deportista</label>
                    <select id="deportista" class="form-select" required>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cedula_deportista" class="form-label">Cédula del Deportista</label>
                    <input type="text" class="form-control" id="cedula_deportista" readonly>
                </div>
                <div class="mb-3">
                    <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                    <select id="tipo_pago" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>

                <!-- Campos adicionales para efectivo -->
                <div id="campos-efectivo" class="d-none">
                    <div class="mb-3">
                        <label for="fecha_pago_efectivo" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago_efectivo" value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="motivo_efectivo" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo_efectivo">
                    </div>
                    <div class="mb-3">
                        <label for="monto_efectivo" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto_efectivo">
                    </div>
                    <div class="mb-3">
                        <label for="mes_efectivo" class="form-label">Mes de Pago</label>
                        <select id="mes_efectivo" class="form-select">
                            <?php
                            $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                            $mes_actual = date('n') - 1;
                            foreach ($meses as $index => $mes) {
                                $selected = ($index == $mes_actual) ? "selected" : "";
                                echo "<option value='$mes' $selected>$mes</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="anio_efectivo" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio_efectivo" value="<?= date('Y'); ?>">
                    </div>
                </div>

                <!-- Campos adicionales para transferencia -->
                <div id="campos-transferencia" class="d-none">
                    <div class="mb-3">
                        <label for="banco_destino" class="form-label">Banco de Destino</label>
                        <select id="banco_destino" class="form-select" required>
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="entidad_financiera" class="form-label">Entidad Financiera de Origen</label>
                        <input type="text" class="form-control" id="entidad_financiera" required>
                    </div>
                    <div class="mb-3">
                        <label for="comprobante" class="form-label">Comprobante</label>
                        <input type="file" class="form-control" id="comprobante" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_pago_transferencia" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago_transferencia" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_transferencia" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo_transferencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="monto_transferencia" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto_transferencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="mes_transferencia" class="form-label">Mes de Pago</label>
                        <select id="mes_transferencia" class="form-select" required>
                            <?php
                            foreach ($meses as $index => $mes) {
                                $selected = ($index == $mes_actual) ? "selected" : "";
                                echo "<option value='$mes' $selected>$mes</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="anio_transferencia" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio_transferencia" value="<?= date('Y'); ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Pago</button>
            </div>
        </div>
    </form>
    <div id="mensaje-confirmacion" class="alert alert-success mt-3" style="display: none;">
        Pago registrado correctamente
    </div>
    <div class="card shadow mb-4">
        <div id="historial_pagos">
        <div class="card-header py-3">
            </div>
        </div>
    </div>
    <?php include '../../Includespro/footer.php'; ?>
    <!-- jQuery y Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- DataTables y botones -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

        <!-- Script para la gestión de pagos -->
        <script src="gestionar_pagos.js"></script>
        
</body>
</html>
