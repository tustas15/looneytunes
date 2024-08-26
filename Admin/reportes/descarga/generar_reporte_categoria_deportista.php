<?php 
session_start();
include_once('../../configuracion/conexion.php');
require('../fpdf/fpdf.php');

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Times', 'B', 20);
        $this->Image('../img/triangulosrecortadosnaranja.png', 0, 0, 70);
        $this->SetXY(60, 15);
        $this->Cell(100, 8, 'Reporte de Deportistas', 0, 1, 'C', 0);
        $this->Cell(0, 10, 'por Categoria', 0, 1, 'C', 0);
        $this->Image('../img/logo_sinfondo.png', 160, 10, 35);
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

// Consulta para obtener los deportistas por categoría seleccionada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Asegúrate de que solo se haya enviado una categoría
    $categoria = isset($_POST['categorias']) ? $_POST['categorias'] : '';

    if (empty($categoria)) {
        die('Debe seleccionar una categoría.');
    }

    try {
        $queryDeportistas = "
            SELECT c.CATEGORIA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, r.CORREO_REPRE
            FROM tab_deportistas d
            INNER JOIN tab_categoria_deportista cd ON d.ID_DEPORTISTA = cd.ID_DEPORTISTA
            INNER JOIN tab_categorias c ON cd.ID_CATEGORIA = c.ID_CATEGORIA
            INNER JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
            INNER JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
            WHERE c.ID_CATEGORIA = ?
            ORDER BY c.CATEGORIA, d.APELLIDO_DEPO
        ";

        $stmtDeportistas = $conn->prepare($queryDeportistas);
        $stmtDeportistas->execute([$categoria]);
        $resultDeportistas = $stmtDeportistas->fetchAll(PDO::FETCH_ASSOC);

        // Encabezado de la tabla en PDF
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235); 
        $pdf->SetDrawColor(61, 61, 61);

        $pdf->SetX((210 - 190) / 2); // Centra la tabla en una página A4
        $pdf->Cell(50, 8, 'Categoria', 'B', 0, 'C', 0);
        $pdf->Cell(40, 8, 'Nombre', 'B', 0, 'C', 0);
        $pdf->Cell(40, 8, 'Apellido', 'B', 0, 'C', 0);
        $pdf->Cell(60, 8, 'Email Representante', 'B', 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235);
        $pdf->SetDrawColor(61, 61, 61);

        foreach ($resultDeportistas as $row) {
            $pdf->Ln(0.6);
            $pdf->SetX((210 - 190) / 2); // Centra la tabla en una página A4
            $pdf->Cell(50, 8, htmlspecialchars($row['CATEGORIA']), 'B', 0, 'C', 1);
            $pdf->Cell(40, 8, htmlspecialchars($row['NOMBRE_DEPO']), 'B', 0, 'C', 1);
            $pdf->Cell(40, 8, htmlspecialchars($row['APELLIDO_DEPO']), 'B', 0, 'C', 1);
            $pdf->Cell(60, 8, htmlspecialchars($row['CORREO_REPRE']), 'B', 1, 'C', 1);
        }

        $pdf->Output('D', 'Reporte_Deportistas_por_Categoria.pdf');
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        die();
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
