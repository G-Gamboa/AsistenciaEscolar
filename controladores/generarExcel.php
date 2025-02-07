<?php
// Incluir el autoloader de Composer
require '../Json/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once 'conexion.php';
require_once 'consultaVacaciones.php';


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'Apellido');
$sheet->setCellValue('C1', 'Correo');
$sheet->setCellValue('D1', 'Vacaciones');
$sheet->setCellValue('E1', 'Vacaciones Totales');

$rowNum = 2;
foreach ($datos as $dato) {
    $sheet->setCellValue('A' . $rowNum, $dato['nombre']);
    $sheet->setCellValue('B' . $rowNum, $dato['apellido']);
    $sheet->setCellValue('C' . $rowNum, $dato['correo']);
    $sheet->setCellValue('D' . $rowNum, $dato['vacaciones']);
    $sheet->setCellValue('E' . $rowNum, $dato['vacacionesTotales']);
    $rowNum++;
}

// Guardar el archivo Excel
$writer = new Xlsx($spreadsheet);
$filename = '../archivos/Vacaciones.xlsx';
$writer->save($filename);

// Verificar si el archivo se guardó correctamente
if (file_exists($filename)) {
    // El archivo se guardó correctamente
    header('Content-Type: application/json');
    echo json_encode(['filename' => $filename, 'data' => $datos]);
} else {
    // Hubo un error al guardar el archivo
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error al guardar el archivo Excel.']);
}

$conn->close(); 
?>