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

	/* 	$table = 
		[
			'EXPTE NRO',
			'SUSCRIPTOR ORIGINAL',
			'DNI M',
		];
 */
		
		$reindexedLines = array_values($lines);

		/* $newTable = [];
		$processedColumns = [];
		foreach($table as $column) {
			foreach($reindexedLines as $key => $line) {
				if (strpos($line, $column) !== false && !isset($processedColumns[$column])) {
					$processedColumns[$column] = true;
					$newTable['TABLE_1'][$column][] = str_replace($table, "", $line);	
					break;		
				}
			}
		} */


		$table = 
		[
			'GLOBAL ANTERIOR N',
			'INTERV. POR SIGEN',
			'NOTA Nº',
		];
// Nuevo array para almacenar los resultados
$newTable = [];

// Inicializar variable para la línea actual
$currentLineKey = null;

// Iterar sobre todas las líneas
foreach ($lines as $key => $line) {
    // Si estamos en la línea que contiene 'GLOBAL ANTERIOR N', procesar la siguiente línea
    if ($currentLineKey !== null) {
        // Obtener la línea siguiente
        $nextLine = $lines[$key];
        
        // Inicializar variables para almacenar los valores encontrados
        $values = [];

        // Buscar los valores correspondientes a cada columna en la línea siguiente
        foreach ($table as $column) {
            // Buscar la posición de cada columna en la línea siguiente
            $pos = strpos($nextLine, $column);
            if ($pos !== false) {
                // Extraer el valor después de la columna encontrada
                $startPos = $pos + strlen($column);
                // Encontrar el siguiente espacio para determinar el final del valor
                $endPos = strpos($nextLine, ' ', $startPos);
                // Extraer el valor, o tomar el resto de la cadena si no hay más espacios
                $value = ($endPos !== false) ? substr($nextLine, $startPos, $endPos - $startPos) : substr($nextLine, $startPos);
                // Almacenar el valor en el array de valores
                $values[$column] = trim($value);
            }
        }

        // Almacenar los valores en $newTable si se encontraron valores
        if (!empty($values)) {
            $newTable[$key] = $values;
        }

        // Reiniciar la variable de línea actual
        $currentLineKey = null;
    }

    // Verificar si la línea actual contiene 'GLOBAL ANTERIOR N'
    if (strpos($line, 'GLOBAL ANTERIOR N') !== false) {
        // Establecer la línea siguiente como la línea actual para procesar
        $currentLineKey = $key;
    }
}


		return $newTable;
	

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