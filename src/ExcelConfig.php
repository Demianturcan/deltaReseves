<?php
namespace src;
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelConfig {
    public static function leerReservas($archivo): array
    {
        $spreadsheet = IOFactory::load($archivo);
        $datos = [];

        foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row) {
            $celdaIterator = $row->getCellIterator();
            $celdaIterator->setIterateOnlyExistingCells(false);

            $reserva = [];
            foreach ($celdaIterator as $celda) {
                $reserva[] = $celda->getFormattedValue();
            }

            $datos[] = $reserva;
        }

        return $datos;
    }
}

/*
$archivo = '../data/cola.xlsx'; // Reemplaza con la ruta real del archivo Excel
$datos = ExcelConfig::leerReservas($archivo);

echo '<pre>'; // Formato más legible
print_r($datos);
echo '</pre>';
*/