<?php
session_start();
include_once('../../configuracion/conexion.php');
require('../../reportes/fpdf/fpdf.php');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte.pdf"');
echo $pdfContent; // Suponiendo que $pdfContent contiene los datos binarios del PDF

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Times', 'B', 20);
        $this->Image('../reportes/img/triangulosrecortadosnaranja.png', 0, 0, 70);
        $this->SetXY(60, 15);
        $this->Cell(100, 8, 'Reporte de Pagos', 0, 1, 'C', 0);
        $this->Image('../reportes/img/logo_sinfondo.png', 160, 10, 35);
        $this->Ln(40);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(170, 10, 'Todos los derechos reservados', 0, 0, 'C', 0);
        $this->Cell(25, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20);

// Procesar los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_informe = $_POST['tipo_informe'] ?? '';
    $id_especifico = $_POST['id_especifico'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';

    // Validación de datos
    if (empty($tipo_informe) || empty($id_especifico) || empty($fecha_inicio) || empty($fecha_fin)) {
        die('Todos los campos son obligatorios.');
    }

    // Parte común de la consulta SQL para formatear la fecha
    $fecha_formato = "CONCAT(ELT(MONTH(p.FECHA_PAGO), 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'), '-', YEAR(p.FECHA_PAGO)) AS MES_ANIO";

    // Definir la consulta SQL según el tipo de informe
    if ($tipo_informe === 'categoria') {
        $sql = "SELECT c.CATEGORIA AS NOMBRE, 
                        CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS NOMBRE_COMPLETO,
                        $fecha_formato,
                        p.MONTO, ep.ESTADO
                FROM tab_categoria_deportista cd
                JOIN tab_deportistas d ON cd.ID_DEPORTISTA = d.ID_DEPORTISTA
                JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE c.ID_CATEGORIA = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } elseif ($tipo_informe === 'deportista') {
        $sql = "SELECT CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS NOMBRE_COMPLETOS,
                        $fecha_formato,
                        p.MONTO, ep.ESTADO
                FROM tab_deportistas d
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE d.ID_DEPORTISTA = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } elseif ($tipo_informe === 'representante') {
        $sql = "SELECT CONCAT(r.NOMBRE_REPRE, ' ', r.APELLIDO_REPRE) AS NOMBRE_COMPLETO_REPRE, 
                        CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS NOMBRE_COMPLETO_DEPO,
                        $fecha_formato,
                        p.MONTO, ep.ESTADO
                FROM tab_representantes_deportistas rd
                JOIN tab_deportistas d ON rd.ID_DEPORTISTA = d.ID_DEPORTISTA
                JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
                LEFT JOIN tab_pagos p ON d.ID_DEPORTISTA = p.ID_DEPORTISTA
                LEFT JOIN tab_estado_pagos ep ON p.ID_PAGO = ep.ID_PAGO
                WHERE r.ID_REPRESENTANTE = :id AND p.FECHA_PAGO BETWEEN :fecha_inicio AND :fecha_fin";
    } else {
        die('Tipo de informe no válido.');
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_especifico, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultados)) {
            die('No se encontraron datos para los criterios seleccionados.');
        }

        // Encabezado de la tabla en PDF
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetFillColor(233, 229, 235);
        $pdf->SetDrawColor(61, 61, 61);

        // Ajustar el ancho de las columnas según el tipo de informe
        if ($tipo_informe === 'categoria' || $tipo_informe === 'representante') {
            $pdf->Cell(50, 8, 'Nombre', 1, 0, 'C', 1);
            $pdf->Cell(50, 8, 'Deportista', 1, 0, 'C', 1);
        } else {
            $pdf->Cell(70, 8, 'Nombre', 1, 0, 'C', 1);
        }
        $pdf->Cell(30, 8, 'Mes-Año', 1, 0, 'C', 1);
        $pdf->Cell(20, 8, 'Monto', 1, 0, 'C', 1);
        $pdf->Cell(20, 8, 'Estado', 1, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 10);

        foreach ($resultados as $row) {
            if ($tipo_informe === 'categoria') {
                $pdf->Cell(50, 8, utf8_decode($row['NOMBRE']), 1, 0, 'L');
                $pdf->Cell(50, 8, utf8_decode($row['NOMBRE_COMPLETO']), 1, 0, 'L');
            } elseif ($tipo_informe === 'deportista') {
                $pdf->Cell(70, 8, utf8_decode($row['NOMBRE_COMPLETOS']), 1, 0, 'L');
            } elseif ($tipo_informe === 'representante') {
                $pdf->Cell(50, 8, utf8_decode($row['NOMBRE_COMPLETO_REPRE']), 1, 0, 'L');
                $pdf->Cell(50, 8, utf8_decode($row['NOMBRE_COMPLETO_DEPO']), 1, 0, 'L');
            }
            $pdf->Cell(30, 8, $row['MES_ANIO'], 1, 0, 'C');
            $pdf->Cell(20, 8, '$' . number_format($row['MONTO'], 2), 1, 0, 'R');
            $pdf->Cell(20, 8, $row['ESTADO'], 1, 1, 'C');
        }

        // Salida del PDF
        $pdf->Output('D]Ln', 'Reporte_Pagos.pdf');
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
} else {
    die('Método de solicitud no permitido.');
}
?>