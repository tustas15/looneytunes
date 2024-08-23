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
        $this->Image('../img/triangulosrecortadosnaranja.png', 0, 0, 70); // imagen(archivo, png/jpg || x,y,tamaño)
        $this->SetXY(60, 15);
        $this->Cell(100, 8, 'Reporte de Administradores', 0, 1, 'C', 0);
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

// Consulta para obtener los logs de los administradores seleccionados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $administradores = isset($_POST['administradores']) ? $_POST['administradores'] : [];
    $eventos = isset($_POST['eventos']) ? $_POST['eventos'] : [];

    if (empty($administradores) || empty($eventos)) {
        die('Debe seleccionar al menos un administrador y un evento.');
    }

    try {
        $placeholders = implode(',', array_fill(0, count($administradores), '?'));
        $placeholdersEventos = implode(',', array_fill(0, count($eventos), '?'));

        $queryLogs = "
            SELECT a.NOMBRE_ADMIN, a.APELLIDO_ADMIN, l.EVENTO, l.HORA_LOG, l.DIA_LOG
            FROM tab_logs l
            INNER JOIN tab_administradores a ON l.ID_USUARIO = a.ID_USUARIO
            WHERE a.ID_ADMINISTRADOR IN ($placeholders)
            AND l.TIPO_EVENTO IN ($placeholdersEventos)
            ORDER BY l.DIA_LOG, l.HORA_LOG
        ";

        $stmtLogs = $conn->prepare($queryLogs);
        $params = array_merge($administradores, $eventos);
        $stmtLogs->execute($params);
        $resultLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        // Encabezado de la tabla en PDF
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        // Calcula el ancho total de la tabla
        $totalWidth = 35 + 25 + 80 + 30 + 30;
        // Centra la tabla
        $pdf->SetX(($pdf->GetPageWidth() - $totalWidth) / 2);

        $pdf->Cell(35, 12, 'Nombre', 'B', 0, 'C', 0);
        $pdf->Cell(25, 12, 'Apellido', 'B', 0, 'C', 0);
        $pdf->Cell(80, 12, 'Evento', 'B', 0, 'C', 0);
        $pdf->Cell(30, 12, 'Hora', 'B', 0, 'C', 0);
        $pdf->Cell(30, 12, 'Fecha', 'B', 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        foreach ($resultLogs as $row) {
            $pdf->Ln(0.6);
            $pdf->SetX(($pdf->GetPageWidth() - $totalWidth) / 2);
            $pdf->Cell(35, 8, htmlspecialchars($row['NOMBRE_ADMIN']), 'B', 0, 'C', 1);
            $pdf->Cell(25, 8, htmlspecialchars($row['APELLIDO_ADMIN']), 'B', 0, 'C', 1);
            $pdf->Cell(80, 8, htmlspecialchars($row['EVENTO']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['HORA_LOG']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['DIA_LOG']), 'B', 1, 'C', 1);
        }

        // Salida del PDF
        $pdf->Output('D', 'Reporte_Administradores.pdf');
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        die();
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
