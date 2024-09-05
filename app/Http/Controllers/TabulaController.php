<?php

namespace App\Http\Controllers;

use App\Models\Suscriptor;
use Illuminate\Http\Request;
use InitRed\Tabula\Tabula;

class TabulaController extends Controller
{
    public function index()
    {
			$saved = $this->saveSuscriptores();
			die;
			$pdfFile = storage_path('app/public/00010_IF-2022-15575413-APN-DCDYPPP#MEC.pdf');
			$csvFile = storage_path("app/public/test.csv");
			//$this->convertPDFToCSV($pdfFile, $csvFile);

			if (($handle = fopen($csvFile, 'r')) !== false) {
				fgetcsv($handle, 1000, ',');
				//$suscriptores = $this->parseSuscriptores($handle);
				dump($saved);
				fclose($handle);
			}

		/* 	if (($handle = fopen($csvFile, 'r')) !== false) {
				fgetcsv($handle, 1000, ',');
				$tableConcepto = $this->makeTableConcepto($handle);
				fclose($handle);
			} */
			


			//dump($tableSuscriptores, $tableConcepto);
			return 'CSV importado exitosamente';
    }

		protected function saveSuscriptores()
		{
				Suscriptor::create([
					'expte_nro' => "1",
					'suscriptor_original' => "1",
					'dni' => "1",
					'orden' => 1,
					'paginas' => 1,
					'numero_if' => "1",
				]);			

				return 1;
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

		protected function makeTableConcepto($handle)
		{
			$newTable = [];
			$rows = [];
			while (($data = fgetcsv($handle, 1000, ',')) !== false) {
				$rows[] = $data;
			}
			
			$collect = false;
			foreach($rows as $key => $row) {
				if (in_array('IT', $row)) {
					$collect = true;
					continue;
				}
	
				if ($collect) {
					if (in_array('GLOBAL', $row) || in_array('DICTAMENES', $row)) {
						break;
					}
					$newTable[] = $row;
				}
			}

			return $newTable;
		}

		protected function parseSuscriptores($handle)
		{
			$columns = ['EXPTE NRO', 'SUSCRIPTOR ORIGINAL', 'DNI M', 'ORDEN', 'PAGINAS'];
			$newTable = [];
			while (($data = fgetcsv($handle, 1000, ',')) !== false) {
				//dump($data);
				foreach($columns as $column) {
					foreach($data as $index => $line){
						if (strpos($line, $column) !== false && !isset($processedColumns[$column])) {
							$processedColumns[$column] = true;
							
							if ($column == 'DNI M') {
								$newTable['DNI M'] = $data[$index+1];
								$newTable['ORDEN'] = $data[$index+2];
								$newTable['PAGINAS'] = $data[$index+3];
							} else {
								$newTable[$column] = isset($data[$index + 1]) ? $data[$index + 1] : null;
							}

							}
					}
				}
			}

			return $newTable;
		}
}
