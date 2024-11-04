<?php
namespace src;

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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

                if ($celda->getColumn() == 'F') {
                    $valor = $celda->getValue();

                    //comprobar si la celda es una fecha
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($celda)) {
                        //convertir y formatear la fecha y hora
                        $dateTimeValue = Date::excelToDateTimeObject($valor);
                        $reserva[] = $dateTimeValue->format('Y-m-d H:i:s');
                    } else {

                        $reserva[] = $valor;
                    }
                } else {

                    $reserva[] = $celda->getFormattedValue();
                }
            }
/*
            //imprimir datos
            echo "Datos leídos: " . implode(", ", $reserva) . "<br>";
*/
            $datos[] = $reserva;
        }

        return $datos;
    }
}
