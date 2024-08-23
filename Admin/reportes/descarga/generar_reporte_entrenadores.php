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
        $this->Cell(100, 8, 'Reporte de Entrenadores', 0, 1, 'C', 0);
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

// Consulta para obtener los entrenadores seleccionados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entrenadores = isset($_POST['entrenadores']) ? $_POST['entrenadores'] : [];
    $eventos = isset($_POST['eventos']) ? $_POST['eventos'] : [];

    if (empty($entrenadores) || empty($eventos)) {
        die('Debe seleccionar al menos un entrenador y un evento.');
    }

    try {
        $placeholders = implode(',', array_fill(0, count($entrenadores), '?'));
        $placeholdersEventos = implode(',', array_fill(0, count($eventos), '?'));

        // Consultar entrenadores seleccionados
        $queryEntrenadores = "
            SELECT * FROM tab_entrenadores
            WHERE ID_ENTRENADOR IN ($placeholders)
        ";

        $stmtEntrenadores = $conn->prepare($queryEntrenadores);
        $stmtEntrenadores->execute($entrenadores);
        $resultEntrenadores = $stmtEntrenadores->fetchAll(PDO::FETCH_ASSOC);

        // Consultar logs filtrados por eventos seleccionados
        $queryLogs = "
            SELECT * FROM tab_logs
            WHERE TIPO_EVENTO IN ($placeholdersEventos)
        ";

        $stmtLogs = $conn->prepare($queryLogs);
        $stmtLogs->execute($eventos);
        $resultLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        // Encabezado de la tabla de entrenadores en PDF
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        // Ancho total de la tabla
        $anchoTabla = 150;
        $margenIzquierdo = ($pdf->GetPageWidth() - $anchoTabla) / 2;

        // Posicionar la tabla en el centro
        $pdf->SetX($margenIzquierdo);
        $pdf->Cell(60, 8, 'Nombre', 'B', 0, 'C', 0);
        $pdf->Cell(60, 8, 'Apellido', 'B', 0, 'C', 0);
        $pdf->Cell(30, 8, 'Estado', 'B', 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        foreach ($resultEntrenadores as $row) {
            $pdf->Ln(0.6);
            $pdf->SetX($margenIzquierdo);
            $pdf->Cell(60, 8, htmlspecialchars($row['NOMBRE_ENTRE']), 'B', 0, 'C', 1);
            $pdf->Cell(60, 8, htmlspecialchars($row['APELLIDO_ENTRE']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['status']), 'B', 1, 'C', 1);
        }

        // Agregar una sección para los logs de eventos seleccionados
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        // Posicionar la tabla de logs en el centro
        $anchoTablaLogs = 120;
        $margenIzquierdoLogs = ($pdf->GetPageWidth() - $anchoTablaLogs) / 2;
        
        $pdf->SetX($margenIzquierdoLogs);
        $pdf->Cell(60, 8, 'Evento', 'B', 0, 'C', 0);
        $pdf->Cell(30, 8, 'Hora', 'B', 0, 'C', 0);
        $pdf->Cell(30, 8, 'Fecha', 'B', 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        foreach ($resultLogs as $row) {
            $pdf->Ln(0.6);
            $pdf->SetX($margenIzquierdoLogs);
            $pdf->Cell(60, 8, htmlspecialchars($row['EVENTO']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['HORA_LOG']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['DIA_LOG']), 'B', 1, 'C', 1);
        }

        // Salida del PDF
        $pdf->Output('D', 'Reporte_Entrenadores.pdf');
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        die();
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
