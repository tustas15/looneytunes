<?php
session_start();
include_once('../../configuracion/conexion.php');
require('../reportes/fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Times', 'B', 20);
        $this->Image('../img/triangulosrecortadosnaranja.png', 0, 0, 70); // imagen(archivo, png/jpg || x,y,tamaño)
        $this->SetXY(60, 15);
        $this->Cell(100, 8, 'Reporte de Inventario', 0, 1, 'C', 0);
        $this->Image('../img/logo_sinfondo.png', 160, 10, 35); // imagen(archivo, png/jpg || x,y,tamaño)
        $this->Ln(40);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 10);
        // Número de página
        $this->Cell(170, 10, 'Todos los derechos reservados', 0, 0, 'C', 0);
        $this->Cell(25, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
}
// Creación del objeto de la clase heredada
$pdf = new PDF(); // hacemos una instancia de la clase
$pdf->AliasNbPages();
$pdf->AddPage(); // añade la página / en blanco
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20); // salto de página automático

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_informe = $_POST['tipo_informe'] ?? '';
    $id_especifico = $_POST['id_especifico'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';

    // Validación de datos
    if (empty($tipo_informe) || empty($id_especifico) || empty($fecha_inicio) || empty($fecha_fin)) {
        die("Todos los campos son obligatorios.");
    }

    // Parte común de la consulta SQL para formatear la fecha
    $fecha_formato = "CONCAT(ELT(MONTH(p.FECHA_PAGO), 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'), '-', YEAR(p.FECHA_PAGO)) AS MES_ANIO";

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
        $sql = "SELECT CONCAT(d.NOMBRE_DEPO, ' ', d.APELLIDO_DEPO) AS NOMBRE_COMPLETO,
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
        die("Tipo de informe no válido.");
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_especifico, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            die("No se encontraron datos para los criterios seleccionados.");
        }

        // Crear PDF
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Añadir los datos al PDF
        $pdf->Cell(0, 10, "Tipo de informe: " . ucfirst($tipo_informe), 0, 1);
        $pdf->Cell(0, 10, "Período: $fecha_inicio a $fecha_fin", 0, 1);
        $pdf->Ln(10);

        // Encabezados de la tabla
        $pdf->SetFont('Arial', 'B', 12);
        if ($tipo_informe === 'categoria') {
            $pdf->Cell(40, 10, 'Categoría', 1);
            $pdf->Cell(50, 10, 'Deportista', 1);
            $pdf->Cell(30, 10, 'Mes/Año', 1);
            $pdf->Cell(30, 10, 'Monto', 1);
            $pdf->Cell(30, 10, 'Estado', 1);
        } elseif ($tipo_informe === 'deportista') {
            $pdf->Cell(60, 10, 'Deportista', 1);
            $pdf->Cell(40, 10, 'Mes/Año', 1);
            $pdf->Cell(40, 10, 'Monto', 1);
            $pdf->Cell(40, 10, 'Estado', 1);
        } elseif ($tipo_informe === 'representante') {
            $pdf->Cell(50, 10, 'Representante', 1);
            $pdf->Cell(50, 10, 'Deportista', 1);
            $pdf->Cell(30, 10, 'Mes/Año', 1);
            $pdf->Cell(30, 10, 'Monto', 1);
            $pdf->Cell(30, 10, 'Estado', 1);
        }
        $pdf->Ln();

        // Datos de la tabla
        $pdf->SetFont('Arial', '', 12);
        foreach ($data as $row) {
            if ($tipo_informe === 'categoria') {
                $pdf->Cell(40, 10, $row['NOMBRE'], 1);
                $pdf->Cell(50, 10, $row['NOMBRE_COMPLETO'], 1);
                $pdf->Cell(30, 10, $row['MES_ANIO'], 1);
                $pdf->Cell(30, 10, $row['MONTO'], 1);
                $pdf->Cell(30, 10, $row['ESTADO'], 1);
            } elseif ($tipo_informe === 'deportista') {
                $pdf->Cell(60, 10, $row['NOMBRE_COMPLETO'], 1);
                $pdf->Cell(40, 10, $row['MES_ANIO'], 1);
                $pdf->Cell(40, 10, $row['MONTO'], 1);
                $pdf->Cell(40, 10, $row['ESTADO'], 1);
            } elseif ($tipo_informe === 'representante') {
                $pdf->Cell(50, 10, $row['NOMBRE_COMPLETO_REPRE'], 1);
                $pdf->Cell(50, 10, $row['NOMBRE_COMPLETO_DEPO'], 1);
                $pdf->Cell(30, 10, $row['MES_ANIO'], 1);
                $pdf->Cell(30, 10, $row['MONTO'], 1);
                $pdf->Cell(30, 10, $row['ESTADO'], 1);
            }
            $pdf->Ln();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Reporte_Pagos.pdf"');
        header('Content-Length: ' . strlen($pdf->Output('S'))); // Si necesitas conocer la longitud del archivo

        $pdf->Output('D', 'Reporte_Pagos.pdf');
        file_put_contents('debug_pdf.pdf', $pdf->Output('S')); // Guarda el PDF en un archivo temporal para revisarlo

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    die("Método no permitido");
}
