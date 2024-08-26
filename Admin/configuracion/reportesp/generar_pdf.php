<?php
require('../../reportes/fpdf/fpdf.php');
require_once('../conexion.php');

// Recibir los parámetros del reporte
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$tipo_reporte = $_POST['tipo_reporte'];
$opcion_especifica = $_POST['opcion_especifica'];

// Aquí debes hacer la misma consulta que en generar_tabla.php para obtener los datos

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Reporte de Pagos',0,1,'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,'Rango de fechas: '.$fecha_inicio.' a '.$fecha_fin,0,1);
$pdf->Cell(0,10,'Tipo de reporte: '.$tipo_reporte,0,1);

// Añadir la tabla de datos
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,7,'Categoría',1);
$pdf->Cell(40,7,'Deportista',1);
$pdf->Cell(30,7,'Fecha',1);
$pdf->Cell(30,7,'Monto',1);
$pdf->Cell(30,7,'Estado',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
foreach($resultados as $row) {
    $pdf->Cell(40,6,$row['categoria'],1);
    $pdf->Cell(40,6,$row['deportista'],1);
    $pdf->Cell(30,6,$row['fecha'],1);
    $pdf->Cell(30,6,'$'.number_format($row['monto'], 2),1);
    $pdf->Cell(30,6,$row['estado'],1);
    $pdf->Ln();
}

$pdf->Output();