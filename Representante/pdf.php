<?php 
session_start();
require_once('../admin/configuracion/conexion.php');
require('../admin/reportes/fpdf/fpdf.php');

// Definir la clase PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Times', 'B', 20);
        $this->Image('../admin/reportes/img/triangulosrecortadosnaranja.png', 0, 0, 70);
        $this->SetXY(60, 15);
        $this->Cell(100, 8, 'Reporte de Pagos', 0, 1, 'C', 0);
        $this->Image('../admin/reportes/img/logo_sinfondo.png', 160, 10, 35);
        $this->Ln(40);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(170, 10, 'Todos los derechos reservados', 0, 0, 'C', 0);
        $this->Cell(25, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20);

try {
    // Verificar que la conexión se estableció correctamente
    if ($conn === null) {
        throw new Exception('Error de conexión a la base de datos.');
    }

    // Obtener el ID del usuario desde la sesión
    $id_usuario = $_SESSION['user_id'];

    // Obtener el ID_REPRESENTANTE correspondiente al ID_USUARIO
    $stmt = $conn->prepare("SELECT ID_REPRESENTANTE FROM tab_representantes WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $id_representante = $stmt->fetchColumn();

    // Verificar si el ID_REPRESENTANTE fue encontrado
    if (!$id_representante) {
        throw new Exception('No se encontró el representante para este usuario.');
    }

    // Preparar la consulta para obtener los pagos
    $stmt = $conn->prepare("
        SELECT p.ID_PAGO, d.NOMBRE_DEPO, d.APELLIDO_DEPO, p.FECHA_PAGO, p.MONTO, p.MOTIVO, p.METODO_PAGO
        FROM tab_pagos p
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
        ORDER BY p.FECHA_PAGO DESC
    ");
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica si la consulta devolvió resultados
    if (empty($pagos)) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontraron pagos.', 0, 1, 'C');
    } else {
        // Configuración de fuentes y colores
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235);
        $pdf->SetDrawColor(61, 61, 61);

        // Ancho total de las celdas
        $totalWidth = 190;

        // Encabezados de las columnas
        $pdf->SetX((210 - $totalWidth) / 2);
        $pdf->Cell(40, 8, 'Deportista', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Monto', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Metodo', 1, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Motivo', 1, 1, 'C', 1);

        // Datos de los pagos
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235);
        $pdf->SetDrawColor(61, 61, 61);

        foreach ($pagos as $pago) {
            $pdf->Ln();
            $pdf->SetX((210 - $totalWidth) / 2);
            $pdf->Cell(40, 8, htmlspecialchars($pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars(date('d/m/Y', strtotime($pago['FECHA_PAGO']))), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars('$' . number_format($pago['MONTO'], 2)), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($pago['METODO_PAGO']), 'B', 0, 'C', 1);
            
            // Usar MultiCell para el motivo
            $pdf->MultiCell(90, 8, htmlspecialchars($pago['MOTIVO']), 'B', 0,'C', 1);
            $pdf->SetX((210 - $totalWidth) / 2); // Alinear la siguiente fila
        }
    }

    // Generar el PDF
    $pdf->Output('D', 'Reporte_Pagos.pdf');
} catch (PDOException $e) {
    echo 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
