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
			dd($texto);
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
		//$cleanedText = preg_replace('/[^a-zA-Z0-9,:;\n\s]/', '', $text);

		// Separar el texto en líneas
		$lines = explode("\n", trim($text));

		// Filtrar líneas vacías
		$lines = array_filter($lines, function($line) {
				return !empty(trim($line));
		});

		$reindexedLines = array_values($lines);
		
		
		$table = ['EXPTE NRO', 'SUSCRIPTOR ORIGINAL', 'DNI M'];
		$newTable = [];
		$processedColumns = [];
		foreach($table as $column) {
			foreach($reindexedLines as $key => $line) {

				if (strpos($line, $column) !== false && !isset($processedColumns[$column])) {
					$processedColumns[$column] = true;
					$value = str_replace($table, "", $line);	

					if($column == 'DNI M') {
						$newTable['TABLE_1'][$column][] = $this->truncateString($value);
					} else {
						$newTable['TABLE_1'][$column][] = str_replace("ORDEN | PAGINAS", "", $value);
					}

					break;		
				}

			}
		}
		
		
		$columnNames = ["IT", "CONCEPTO", "EXPRESADOS EN PESOS", "CANT"];
		$columnsFound = false;
		foreach ($reindexedLines as $line) {
			if (!$columnsFound) {
				// Buscamos la línea que contiene las columnas
				$matches = [];
				$regex = '/\b(?:' . implode('|', array_map('preg_quote', $columnNames)) . ')\b/';
				preg_match_all($regex, $line, $matches);

				// Si encontramos todas las columnas en la línea, marcamos que las encontramos
				if (count($matches[0]) === count($columnNames)) {
					$columnsFound = true;
					continue;
				}
			} else {
				// Si ya encontramos las columnas, mapeamos los valores
				$values = preg_split('/\s+/', trim($line));

				// Asociamos cada columna con su valor correspondiente
				foreach ($columnNames as $index => $column) {
						if (isset($values[$index])) {
								$newTable['TABLE_2'][$column][] = $values[$index];
						} else {
								$newTable['TABLE_2'][$column][] = null; // Si no hay un valor correspondiente, asignamos null
						}
				}
				
				break; // Salimos del bucle ya que solo necesitamos mapear una línea de valores
			}
		}	

		return $newTable;

	

	}

	protected function truncateString($string) 
	{
    // Trunca el string a 11 caracteres
    $truncated = substr($string, 0, 11);

    // Busca el último espacio en el string truncado
    $lastSpacePosition = strrpos($truncated, ' ');

    // Si hay un espacio, corta el string hasta el último espacio
    if ($lastSpacePosition !== false) {
        $truncated = substr($truncated, 0, $lastSpacePosition);
    }

    return $truncated;
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