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

// Consulta para obtener los datos del inventario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productos = isset($_POST['productos']) ? $_POST['productos'] : [];
    $categorias = isset($_POST['categorias']) ? $_POST['categorias'] : [];

    if (empty($productos) && empty($categorias)) {
        die('Debe seleccionar al menos un producto o una categoría.');
    }

    try {
        $placeholdersProductos = !empty($productos) ? implode(',', array_fill(0, count($productos), '?')) : '';
        $placeholdersCategorias = !empty($categorias) ? implode(',', array_fill(0, count($categorias), '?')) : '';

        $queryInventario = "
            SELECT p.producto_nombre AS NOMBRE_PRODUCTO, p.producto_precio AS PRECIO, p.producto_stock AS CANTIDAD, 
                   c.categoria_nombre AS NOMBRE_CATEGORIA
            FROM tab_productos p
            INNER JOIN tab_producto_categoria c ON p.id_categoria_producto = c.id_categoria_producto
            WHERE 1=1
        ";

        if (!empty($productos)) {
            $queryInventario .= " AND p.id_producto IN ($placeholdersProductos)";
        }
        
        if (!empty($categorias)) {
            $queryInventario .= " AND p.id_categoria_producto IN ($placeholdersCategorias)";
        }

        $queryInventario .= " ORDER BY p.producto_nombre";

        $stmtInventario = $conn->prepare($queryInventario);

        // Preparar los parámetros
        $params = array_merge($productos, $categorias);

        $stmtInventario->execute($params);
        $resultInventario = $stmtInventario->fetchAll(PDO::FETCH_ASSOC);

        // Encabezado de la tabla en PDF
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        // Definir el ancho total de la tabla
        $totalWidth = 130; // Ancho total de la tabla
        $pdf->SetX((210 - $totalWidth) / 2); // Centrar la tabla en la página

        $pdf->Cell(50, 8, 'Nombre Producto', 'B', 0, 'C', 0);
        $pdf->Cell(30, 8, 'Categoria', 'B', 0, 'C', 0);
        $pdf->Cell(20, 8, 'Cantidad', 'B', 0, 'C', 0);
        $pdf->Cell(30, 8, 'Precio', 'B', 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 229, 235); // color de fondo rgb
        $pdf->SetDrawColor(61, 61, 61); // color de línea rgb

        foreach ($resultInventario as $row) {
            $pdf->Ln(0.6);
            $pdf->SetX((210 - $totalWidth) / 2); // Centrar cada fila de la tabla
            $pdf->Cell(50, 8, htmlspecialchars($row['NOMBRE_PRODUCTO']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['NOMBRE_CATEGORIA']), 'B', 0, 'C', 1);
            $pdf->Cell(20, 8, htmlspecialchars($row['CANTIDAD']), 'B', 0, 'C', 1);
            $pdf->Cell(30, 8, htmlspecialchars($row['PRECIO']), 'B', 1, 'C', 1);
        }

        // Salida del PDF
        $pdf->Output('D', 'Reporte_Inventario.pdf');
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        die();
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
