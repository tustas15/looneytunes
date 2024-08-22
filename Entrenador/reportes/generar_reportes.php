<?php
require_once('../admin/configuracion/conexion.php');
require_once(__DIR__ . '/../../vendor/autoload.php');  // Ruta corregida para autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear una nueva instancia de Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Deportistas');

// Consultas SQL
$sql_deportistas = "SELECT * FROM tab_deportistas";
$result_deportistas = $conn->query($sql_deportistas);

$sql_entrenadores = "SELECT * FROM tab_entrenadores";
$result_entrenadores = $conn->query($sql_entrenadores);

$sql_logs = "SELECT * FROM tab_logs";
$result_logs = $conn->query($sql_logs);

// Agregar los datos de Deportistas al archivo Excel
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Apellido');
$sheet->setCellValue('D1', 'Fecha de Nacimiento');
$sheet->setCellValue('E1', 'Cédula');
$sheet->setCellValue('F1', 'Celular');
$sheet->setCellValue('G1', 'Género');

$row = 2;
if ($result_deportistas->num_rows > 0) {
    while($data = $result_deportistas->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['ID_DEPORTISTA']);
        $sheet->setCellValue('B' . $row, $data['NOMBRE_DEPO']);
        $sheet->setCellValue('C' . $row, $data['APELLIDO_DEPO']);
        $sheet->setCellValue('D' . $row, $data['FECHA_NACIMIENTO']);
        $sheet->setCellValue('E' . $row, $data['CEDULA_DEPO']);
        $sheet->setCellValue('F' . $row, $data['NUMERO_CELULAR']);
        $sheet->setCellValue('G' . $row, $data['GENERO']);
        $row++;
    }
} else {
    $sheet->setCellValue('A2', 'No hay datos disponibles');
}

// Agregar los datos de Entrenadores al archivo Excel
$spreadsheet->createSheet();
$sheet = $spreadsheet->setActiveSheetIndex(1);
$sheet->setTitle('Entrenadores');

$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Apellido');
$sheet->setCellValue('D1', 'Experiencia');
$sheet->setCellValue('E1', 'Celular');
$sheet->setCellValue('F1', 'Correo');
$sheet->setCellValue('G1', 'Dirección');
$sheet->setCellValue('H1', 'Cédula');

$row = 2;
if ($result_entrenadores->num_rows > 0) {
    while($data = $result_entrenadores->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['ID_ENTRENADOR']);
        $sheet->setCellValue('B' . $row, $data['NOMBRE_ENTRE']);
        $sheet->setCellValue('C' . $row, $data['APELLIDO_ENTRE']);
        $sheet->setCellValue('D' . $row, $data['EXPERIENCIA_ENTRE']);
        $sheet->setCellValue('E' . $row, $data['CELULAR_ENTRE']);
        $sheet->setCellValue('F' . $row, $data['CORREO_ENTRE']);
        $sheet->setCellValue('G' . $row, $data['DIRECCION_ENTRE']);
        $sheet->setCellValue('H' . $row, $data['CEDULA_ENTRE']);
        $row++;
    }
} else {
    $sheet->setCellValue('A2', 'No hay datos disponibles');
}

// Agregar los datos de Logs al archivo Excel
$spreadsheet->createSheet();
$sheet = $spreadsheet->setActiveSheetIndex(2);
$sheet->setTitle('Logs');

$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Usuario');
$sheet->setCellValue('C1', 'Evento');
$sheet->setCellValue('D1', 'Hora');
$sheet->setCellValue('E1', 'Día');
$sheet->setCellValue('F1', 'IP');
$sheet->setCellValue('G1', 'Tipo de Evento');

$row = 2;
if ($result_logs->num_rows > 0) {
    while($data = $result_logs->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['ID_LOG']);
        $sheet->setCellValue('B' . $row, $data['ID_USUARIO']);
        $sheet->setCellValue('C' . $row, $data['EVENTO']);
        $sheet->setCellValue('D' . $row, $data['HORA_LOG']);
        $sheet->setCellValue('E' . $row, $data['DIA_LOG']);
        $sheet->setCellValue('F' . $row, $data['IP']);
        $sheet->setCellValue('G' . $row, $data['TIPO_EVENTO']);
        $row++;
    }
} else {
    $sheet->setCellValue('A2', 'No hay datos disponibles');
}

// Crear el archivo Excel y enviarlo al navegador
$writer = new Xlsx($spreadsheet);
$filename = 'Reportes_Sistema_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');

// Cerrar la conexión
$conn->close();
