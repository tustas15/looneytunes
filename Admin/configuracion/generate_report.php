<?php
require_once('./conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_type'])) {
    $reportType = $_POST['report_type'];
    $reportFormat = $_POST['report_format'] ?? 'csv'; // Por defecto 'csv' si no se especifica

    try {
        switch ($reportType) {
            case 'administradores':
                $sql = "SELECT * FROM tab_administradores";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sqlLogs = "SELECT * FROM tab_logs WHERE ID_USUARIO IN (SELECT ID_USUARIO FROM tab_usu_tipo WHERE ID_TIPO = (SELECT ID_TIPO FROM tab_tipo_usuario WHERE TIPO = 'Administrador'))";
                $stmtLogs = $conn->prepare($sqlLogs);
                $stmtLogs->execute();
                $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

                $sqlUsuarios = "SELECT * FROM tab_usuarios WHERE ID_USUARIO IN (SELECT ID_USUARIO FROM tab_administradores)";
                $stmtUsuarios = $conn->prepare($sqlUsuarios);
                $stmtUsuarios->execute();
                $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, $logs, [], $reportFormat, 'Administradores', $usuarios);
                break;

            case 'entrenadores':
                $sql = "SELECT * FROM tab_entrenadores";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sqlCategorias = "SELECT e.ID_ENTRENADOR, c.ID_CATEGORIA, c.CATEGORIA FROM tab_entrenador_categoria ec JOIN tab_categorias c ON ec.ID_CATEGORIA = c.ID_CATEGORIA JOIN tab_entrenadores e ON ec.ID_ENTRENADOR = e.ID_ENTRENADOR";
                $stmtCategorias = $conn->prepare($sqlCategorias);
                $stmtCategorias->execute();
                $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

                $sqlLogs = "SELECT l.ID_LOG, l.ID_USUARIO, l.EVENTO, l.HORA_LOG, l.DIA_LOG, l.IP, l.TIPO_EVENTO FROM tab_logs l JOIN tab_usuarios u ON l.ID_USUARIO = u.ID_USUARIO JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO WHERE ut.ID_TIPO = 2";
                $stmtLogs = $conn->prepare($sqlLogs);
                $stmtLogs->execute();
                $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

                $sqlUsuarios = "SELECT u.ID_USUARIO, u.USUARIO, u.PASS FROM tab_usuarios u JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO WHERE ut.ID_TIPO = 2";
                $stmtUsuarios = $conn->prepare($sqlUsuarios);
                $stmtUsuarios->execute();
                $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, $logs, $categorias, $reportFormat, 'Entrenadores', $usuarios);
                break;

            case 'representantes':
                $sql = "SELECT * FROM tab_representantes";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sqlDeportistas = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.FECHA_NACIMIENTO, d.CEDULA_DEPO, d.NUMERO_CELULAR, d.GENERO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CELULAR_REPRE, r.CORREO_REPRE FROM tab_deportistas d JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE";
                $stmtDeportistas = $conn->prepare($sqlDeportistas);
                $stmtDeportistas->execute();
                $deportistas = $stmtDeportistas->fetchAll(PDO::FETCH_ASSOC);

                $sqlPagos = "SELECT p.ID_PAGO, p.ID_REPRESENTANTE, p.ID_DEPORTISTA, p.TIPO_PAGO, p.MONTO, p.FECHA, p.BANCO, p.MOTIVO, d.NOMBRE_DEPO, d.APELLIDO_DEPO FROM tab_pagos p JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA";
                $stmtPagos = $conn->prepare($sqlPagos);
                $stmtPagos->execute();
                $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);

                $sqlLogs = "SELECT l.ID_LOG, l.ID_USUARIO, l.EVENTO, l.HORA_LOG, l.DIA_LOG, l.IP, l.TIPO_EVENTO FROM tab_logs l JOIN tab_usuarios u ON l.ID_USUARIO = u.ID_USUARIO JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO WHERE ut.ID_TIPO = 3";
                $stmtLogs = $conn->prepare($sqlLogs);
                $stmtLogs->execute();
                $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

                $sqlUsuarios = "SELECT u.ID_USUARIO, u.USUARIO, u.PASS FROM tab_usuarios u JOIN tab_usu_tipo ut ON u.ID_USUARIO = ut.ID_USUARIO WHERE ut.ID_TIPO = 3";
                $stmtUsuarios = $conn->prepare($sqlUsuarios);
                $stmtUsuarios->execute();
                $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, $logs, $deportistas, $reportFormat, 'Representantes', $usuarios, $pagos);
                break;

            case 'deportistas':
                $sql = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.FECHA_NACIMIENTO, d.CEDULA_DEPO, d.NUMERO_CELULAR, d.GENERO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CELULAR_REPRE, r.CORREO_REPRE FROM tab_deportistas d LEFT JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA LEFT JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sqlLogs = "SELECT l.ID_LOG, l.ID_USUARIO, l.EVENTO, l.HORA_LOG, l.DIA_LOG, l.IP, l.TIPO_EVENTO FROM tab_logs l JOIN tab_usuarios u ON l.ID_USUARIO = u.ID_USUARIO WHERE u.ID_USUARIO IN (SELECT ID_USUARIO FROM tab_deportistas)";
                $stmtLogs = $conn->prepare($sqlLogs);
                $stmtLogs->execute();
                $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

                $sqlUsuarios = "SELECT u.ID_USUARIO, u.USUARIO, u.PASS FROM tab_usuarios u WHERE u.ID_USUARIO IN (SELECT ID_USUARIO FROM tab_deportistas)";
                $stmtUsuarios = $conn->prepare($sqlUsuarios);
                $stmtUsuarios->execute();
                $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                $sqlDetalles = "SELECT d.ID_DEPORTISTA, d.NUMERO_CAMISA, d.ALTURA, d.PESO, d.FECHA_INGRESO FROM tab_detalles d LEFT JOIN tab_deportistas dep ON d.ID_DEPORTISTA = dep.ID_DEPORTISTA";
                $stmtDetalles = $conn->prepare($sqlDetalles);
                $stmtDetalles->execute();
                $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, $logs, $detalles, $reportFormat, 'Deportistas', $usuarios);
                break;

            case 'inventario':
                $sql = "SELECT p.id_producto, p.producto_codigo, p.producto_nombre, p.producto_precio, p.producto_stock, p.producto_foto, c.categoria_nombre FROM tab_productos p JOIN tab_producto_categoria c ON p.id_categoria_producto = c.id_categoria_producto";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, [], [], $reportFormat, 'Inventario', []);
                break;

            case 'categorias':
                $sql = "SELECT c.ID_CATEGORIA, c.CATEGORIA, c.LIMITE_DEPORTISTAS, CONCAT(e.NOMBRE_ENTRE, ' ', e.APELLIDO_ENTRE) AS entrenador_nombre_completo, CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS deportista_nombre_completo FROM tab_categorias c LEFT JOIN tab_entrenador_categoria ec ON c.ID_CATEGORIA = ec.ID_CATEGORIA LEFT JOIN tab_entrenadores e ON ec.ID_ENTRENADOR = e.ID_ENTRENADOR LEFT JOIN tab_categoria_deportista cd ON c.ID_CATEGORIA = cd.ID_CATEGORIA LEFT JOIN tab_deportistas d ON cd.ID_DEPORTISTA = d.ID_DEPORTISTA";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                generateReport($data, [], [], $reportFormat, 'Categorías', []);
                break;

            default:
                echo 'Tipo de informe no válido.';
        }
    } catch (Exception $e) {
        echo 'Error al generar el informe: ' . $e->getMessage();
    }
} else {
    echo 'No se ha seleccionado ningún tipo de informe.';
}

function generateReport($data, $logs, $categorias, $reportFormat, $title, $usuarios = [], $pagos = [])
{
    if ($reportFormat === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $title . '.csv"');

        $output = fopen('php://output', 'w');

        // Agregar encabezados según el tipo de reporte
        $headers = array_keys($data[0]);
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    } elseif ($reportFormat === 'pdf') {
        require_once('/xampp/htdocs/looneytunes/fpdf/fpdf.php');

        $pdf = new FPDF('L', 'mm', array(420, 594)); 
        $pdf->SetLeftMargin(15);
        $pdf->SetRightMargin(15);
        $pdf->SetTopMargin(15);
        $pdf->SetAutoPageBreak(TRUE, 15); 

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 20, $title, 0, 1, 'C');

        $pdf->SetFont('Arial', '', 16);
        
        // Agregar datos principales
        if (!empty($data)) {
            $pdf->SetXY(15, 40);
            $headerWidth = 60;
            $headerHeight = 15;

            foreach (array_keys($data[0]) as $header) {
                $pdf->Cell($headerWidth, $headerHeight, $header, 1);
            }
            $pdf->Ln();

            foreach ($data as $row) {
                foreach ($row as $value) {
                    $pdf->Cell($headerWidth, $headerHeight, $value, 1);
                }
                $pdf->Ln();
            }
        }

        // Agregar logs si existen
        if (!empty($logs)) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 20, 'Logs', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 16);
            $pdf->SetXY(15, 40);
            foreach (array_keys($logs[0]) as $header) {
                $pdf->Cell($headerWidth, $headerHeight, $header, 1);
            }
            $pdf->Ln();

            foreach ($logs as $row) {
                foreach ($row as $value) {
                    $pdf->Cell($headerWidth, $headerHeight, $value, 1);
                }
                $pdf->Ln();
            }
        }

        // Agregar categorías si existen
        if (!empty($categorias)) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 20, 'Categorías', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 16);
            $pdf->SetXY(15, 40);
            foreach (array_keys($categorias[0]) as $header) {
                $pdf->Cell($headerWidth, $headerHeight, $header, 1);
            }
            $pdf->Ln();

            foreach ($categorias as $row) {
                foreach ($row as $value) {
                    $pdf->Cell($headerWidth, $headerHeight, $value, 1);
                }
                $pdf->Ln();
            }
        }

        // Agregar pagos si existen
        if (!empty($pagos)) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 20, 'Pagos', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 16);
            $pdf->SetXY(15, 40);
            foreach (array_keys($pagos[0]) as $header) {
                $pdf->Cell($headerWidth, $headerHeight, $header, 1);
            }
            $pdf->Ln();

            foreach ($pagos as $row) {
                foreach ($row as $value) {
                    $pdf->Cell($headerWidth, $headerHeight, $value, 1);
                }
                $pdf->Ln();
            }
        }

        $pdf->Output();
    }
}
?>
