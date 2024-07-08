<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <?php include '../vista/header.php'; ?>
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include '../vista/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <?php
                    $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
                    include '../vista/navigation.php';
                    ?>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Gestión de Pagos</h1>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Registrar Pago</h6>
                                </div>
                                <div class="card-body">
                                    <form id="paymentForm">
                                        <div class="form-group">
                                            <label for="cedula">Cédula del Representante</label>
                                            <input type="text" class="form-control" id="cedula" name="cedula" onkeyup="fetchRepresentante(this.value)">
                                        </div>
                                        <div class="form-group">
                                            <label for="representante">Nombre del Representante</label>
                                            <input type="text" class="form-control" id="representante" name="representante" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_deportista">Nombre del Deportista</label>
                                            <select class="form-control" id="id_deportista" name="id_deportista"></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="metodo_pago">Método de Pago</label>
                                            <select class="form-control" id="metodo_pago" name="metodo_pago">
                                                <option value="transferencia">Transferencia</option>
                                                <option value="efectivo">Efectivo</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="monto">Monto</label>
                                            <input type="number" class="form-control" id="monto" name="monto" step="0.01" required>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="registrarPago()">Registrar Pago</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Historial de Pagos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>ID Pago</th>
                                                    <th>Nombre</th>
                                                    <th>Apellido</th>
                                                    <th>Método de Pago</th>
                                                    <th>Fecha</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="historialPagos">
                                                <!-- Aquí se cargarán los pagos dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="detallesPago" style="display:none;">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Detalles del Pago</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="editarPagoForm">
                                            <!-- Aquí se cargarán los detalles del pago para editar -->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../vista/footer.php'; ?>
        </div>
    </div>
    <?php include '../vista/scripts.php'; ?>
    <script>
        function fetchRepresentante(cedula) {
            if (cedula.length < 10) {
                document.getElementById("representante").value = "";
                document.getElementById("id_deportista").innerHTML = "<option value=''>Seleccione primero un representante</option>";
                return;
            }

            fetch(`fetch_representante.php?cedula=${cedula}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.getElementById("representante").value = data.representante;
                        
                        let htmlDeportistas = "<option value=''>Seleccione un deportista</option>";
                        data.deportistas.forEach(deportista => {
                            htmlDeportistas += `<option value="${deportista.id_deportista}">${deportista.nombre_deportista}</option>`;
                        });
                        document.getElementById("id_deportista").innerHTML = htmlDeportistas;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function registrarPago() {
            const formData = new FormData(document.getElementById('paymentForm'));
            
            fetch('registrar_pago.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert('Pago registrado con éxito');
                    cargarHistorialPagos();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function cargarHistorialPagos() {
            fetch('cargar_historial_pagos.php')
                .then(response => response.json())
                .then(data => {
                    const historial = document.getElementById('historialPagos');
                    historial.innerHTML = '';
                    data.forEach(pago => {
                        historial.innerHTML += `
                            <tr>
                                <td>${pago.id_pago}</td>
                                <td>${pago.nombre_repre}</td>
                                <td>${pago.apellido_repre}</td>
                                <td>${pago.metodo_pago}</td>
                                <td>${pago.fecha}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="verPago(${pago.id_pago})">Ver</button>
                                    <button class="btn btn-success btn-sm" onclick="enviarComprobante(${pago.id_pago})">Enviar</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function verPago(id_pago) {
            fetch(`ver_pago.php?id=${id_pago}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        const form = document.getElementById('editarPagoForm');
                        form.innerHTML = `
                            <input type="hidden" name="id_pago" value="${data.id_pago}">
                            <div class="form-group">
                                <label>ID Pago</label>
                                <input type="text" class="form-control" value="${data.id_pago}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Representante</label>
                                <input type="text" class="form-control" value="${data.nombre_repre} ${data.apellido_repre}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Deportista</label>
                                <input type="text" class="form-control" value="${data.nombre_deportista}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Método de Pago</label>
                                <input type="text" class="form-control" name="metodo_pago" value="${data.metodo_pago}">
                            </div>
                            <div class="form-group">
                                <label>Monto</label>
                                <input type="number" class="form-control" name="monto" value="${data.monto}" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="date" class="form-control" name="fecha" value="${data.fecha}">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="actualizarPago()">Actualizar</button>
                            <button type="button" class="btn btn-danger" onclick="eliminarPago(${data.id_pago})">Eliminar</button>
                        `;
                        document.getElementById('detallesPago').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function actualizarPago() {
            const formData = new FormData(document.getElementById('editarPagoForm'));
            
            fetch('actualizar_pago.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert('Pago actualizado con éxito');
                    cargarHistorialPagos();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function eliminarPago(id_pago) {
            if (confirm('¿Está seguro de que desea eliminar este pago?')) {
                fetch(`eliminar_pago.php?id=${id_pago}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            alert('Pago eliminado con éxito');
                            cargarHistorialPagos();
                            document.getElementById('detallesPago').style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function enviarComprobante(id_pago) {
            fetch(`enviar_comprobante.php?id=${id_pago}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        alert('Comprobante enviado con éxito');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Cargar el historial de pagos al cargar la página
        document.addEventListener('DOMContentLoaded', cargarHistorialPagos);
    </script>
</body>
</html>