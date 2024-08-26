<?php
require('../../reportes/fpdf/fpdf.php');
require('../conexion.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('path/to/your/logo.png', 160, 10, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Pagos', 0, 1, 'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Obtener datos de la base de datos
$sql = "SELECT ... "; // Tu consulta SQL aquí
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'fecha_inicio' => $_POST['fecha_inicio'],
    'fecha_fin' => $_POST['fecha_fin'],
    'opcion_especifica' => $_POST['opcion_especifica']
]);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(40, 10, 'Categoría', 1);
$pdf->Cell(30, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Monto', 1);
$pdf->Cell(40, 10, 'Estado', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(50, 10, $row['nombre'], 1);
    $pdf->Cell(40, 10, $row['categoria'], 1);
    $pdf->Cell(30, 10, $row['fecha'], 1);
    $pdf->Cell(30, 10, $row['monto'], 1);
    $pdf->Cell(40, 10, $row['estado'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'reporte_pagos.pdf');