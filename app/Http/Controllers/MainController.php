<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class MainController extends Controller
{
    public function index()
    {
        try {
            //$folder = 'Otros/2018-24123718-APN_DGD_MHA/';
            $pdfFile = '00010_IF-2022-15575413-APN-DCDYPPP#MEC.pdf';
            $outputImage = '00010_IF-2022-15575413-APN-DCDYPPP#MEC.png';
            $this->convertirPdfAImagen($pdfFile, $outputImage);
        
        
            $texto = $this->extraerTextoDeImagen($outputImage);
        		$data = $this->procesarTextoOk($texto);
						dd($data);
						//echo $this->guardar((data);
        
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

	protected function convertirPdfAImagen($pdfFile, $outputImage, $firstPage = 1, $lastPage = 1) 
	{
    	//$command = "gswin64c -r720 -sDEVICE=pngalpha -o $outputImage -dFirstPage=$firstPage -dLastPage=$lastPage $pdfFile";
    	$command = "gs -r720 -sDEVICE=pngalpha -o $outputImage -dFirstPage=$firstPage -dLastPage=$lastPage $pdfFile";
    	exec($command, $output, $return_var);
	    if ($return_var !== 0) {
        	throw new Exception();
    	}
	}

	protected function extraerTextoDeImagen($outputImage) 
	{
    	$ocr = new TesseractOCR($outputImage);
    	$texto = ($ocr)->run();
	    return $texto;
	}

	protected function procesarTextoOk($text) 
	{
		// Limpiar el texto de caracteres no deseados
		$cleanedText = preg_replace('/[^a-zA-Z0-9,:;\n\s]/', '', $text);

		// Separar el texto en líneas
		$lines = explode("\n", trim($cleanedText));

		// Filtrar líneas vacías
		$lines = array_filter($lines, function($line) {
				return !empty(trim($line));
		});

		$columns = [
			'EXPTE NRO',
			'ORDEN',
			'PAGINAS',
			'SUSCRIPTOR ORIGINAL',
			'DNI',
			'IMPORTE A CANCELAR',
			'BONOS 4TA SERIE AL 2',
			'CONCEPTO',
			'EXPRESADOS EN PESOS',
			'CANT',
		];

		//return $lines;
		$reindexedLines = array_values($lines);

		$result = [];
		foreach ($reindexedLines as $key => $line) {
			//if ($key == 1) { // O la condición que prefieras
					$tempLine = $line; 
	
					foreach ($columns as $index => $column) {
							// Generar un identificador único para cada columna
							$identifier = $index . '-' . $column;
							$columnIdentifiers[$identifier] = $column;
	
							// Reemplazar cada columna en la línea temporal
							$tempLine = str_replace($column, "", $tempLine);
					}
	
					// Almacenar el resultado final con un nombre correspondiente
					foreach ($columnIdentifiers as $identifier => $originalColumn) {
							if (strpos($line, $originalColumn) !== false) {
									$result[$identifier] = $tempLine;
									break; // Salir del bucle una vez que se ha asignado el resultado
							}
					}
			//}
	}
	
	return $result;

		return $result;
	

	}

	protected function procesarTexto($texto) 
	{
   		// Limpiar el texto de caracteres no deseados
    	$cleanedText = preg_replace('/[^a-zA-Z0-9,:;\n\s]/', '', $texto);

    	// Separar el texto por líneas
    	$lines = explode("\n", $cleanedText);

	    $data = [];

	    foreach ($lines as $line) {
        
    	    if (preg_match('/([A-Z\s]+)\s+([0-9a-zA-Z-\/]+)/', trim($line), $matches)) {
        	    $key = trim($matches[1]);  // Primera parte como clave
            	$value = trim($matches[2]); // Segunda parte como valor

            	// Guardar en el array
            	if (!empty($key) && !empty($value)) {
	                $data[$key] = $value;
    	        }
        	}
    	}

    	return $data;
	}

	protected function guardar($data) 
	{
		foreach($data as $d) { 
			//
		}
	}

}