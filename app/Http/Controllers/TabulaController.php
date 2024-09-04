<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InitRed\Tabula\Tabula;

class TabulaController extends Controller
{
    public function index()
    {
			$pdfFile = storage_path('app/public/00010_IF-2022-15575413-APN-DCDYPPP#MEC.pdf');
			$csvFile = storage_path("app/public/test.csv");
			//$this->convertPDFToCSV($pdfFile, $csvFile);


			if (($handle = fopen($csvFile, 'r')) !== false) {
				fgetcsv($handle, 1000, ','); // Saltea la primera línea si contiene encabezados

				$columns = ['EXPTE NRO', 'SUSCRIPTOR ORIGINAL', 'DNI M', 'ORDEN', 'PAGINAS'];
				while (($data = fgetcsv($handle, 1000, ',')) !== false) {
					dump(array_values($data));
					/* foreach($data as $d)
					foreach($columns as $column) {
						if($column !== $d) {
							$newTable[$column][] = $d;
							}
							} */
						//dump($data);
					}
					
				fclose($handle);
			}

			//dd($newTable);
			return 'CSV importado exitosamente';
    }

		protected function convertPDFToCSV($pdfFile, $csvFile)
		{
			$tabula = new Tabula('/usr/bin/');

			$tabula->setPdf($pdfFile)
				->setOptions([
					'format' => 'csv',
					'pages' => 'all',
					'lattice' => true,
					'stream' => true,
					'outfile' => $csvFile,
				])
			->convert();
		}
}
