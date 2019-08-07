<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
use App\Model\Car;

class CarController extends Controller
{
	public function view() {
		$cars = Car::paginate(10);

		return view('cars', compact('cars'));
	}

    public function update() {
    	$base_url = 'http://www.carstore.citroen.es/madrid-las-tablas-psa-retail';
		$html = file_get_contents($base_url);
		$dom = new DOMDocument;
		@$dom->loadHTML($html);

		$cars = [];
		$carsUrl = [];

		$links = $dom->getElementsByTagName('a');

		foreach ($links as $link){
		    $url = $link->getAttribute('href');

		    if (strpos($url, 'ficha-detallada') !== false) {
		    	$carUrl = 'http://www.carstore.citroen.es'.$url.'&AjaxRequest=VehicleInfos_getHtmlVehicleById';
		    	$carsUrl[] = $carUrl;
		    	$parts = parse_url($carUrl);
				parse_str($parts['query'], $query);
				$carNum = $query['carNum'];
			    
			    $cars[] = $carNum;
			}
		}

		$page = 2;
		$break = false;

		while (!$break) {
			$bloc = (int) ($page / 10) + 1;

			if ($page % 10 == 0)
				$bloc--;

			$tmpUrl = $base_url.'?nbPage='.$page.'&Bloc='.$bloc;

			$html = file_get_contents($tmpUrl);
			$dom = new DOMDocument;
			@$dom->loadHTML($html);

			
			$links = $dom->getElementsByTagName('a');
			$newCars = [];
			$newCarsUrl = [];

			foreach ($links as $link){
			    $url = $link->getAttribute('href');

			    if (strpos($url, 'ficha-detallada') !== false) {
			    	$carUrl = 'http://www.carstore.citroen.es'.$url.'&AjaxRequest=VehicleInfos_getHtmlVehicleById';
			    	$newCarsUrl[] = $carUrl;
			    	$parts = parse_url($carUrl);
					parse_str($parts['query'], $query);
					$carNum = $query['carNum'];
				    
			    	$newCars[] = $carNum;
				}
			}

			$count = 0;
			foreach($newCars as $c) {
				if (in_array($c, $cars))
					$count++;
			}


			if (sizeof($newCars) == $count)
				$break = true;
			else {
				$cars = array_merge($cars, $newCars);
				$carsUrl = array_merge($carsUrl, $newCarsUrl);
			}

			$page++;
		}

		// Delete All items
		Car::truncate();

		foreach($carsUrl as $a) {
			//$carUrl = 'http://www.carstore.citroen.es/madrid-las-tablas-psa-retail/c3/berlina-5-p/ficha-detallada?nbPage=1&carNum=R-463409&VehListIndex=1%7c149&AjaxRequest=VehicleInfos_getHtmlVehicleById';
			$parts = parse_url($a);
			parse_str($parts['query'], $query);
			$carNum = $query['carNum'];


			$carHtml = file_get_contents($a);
			$carHtml = mb_convert_encoding($carHtml, 'HTML-ENTITIES', "UTF-8");
			$carDom = new DOMDocument;
			@$carDom->loadHTML($carHtml);
			$xpath = new DOMXPath($carDom);

			// Car Image
			$nodelist = $xpath->query("//img");
			$node = $nodelist->item(0); // gets the 1st image
			$carImageUrl = $node->attributes->getNamedItem('src')->nodeValue;

			// Delay
			$nodelist = $xpath->query('//p[@class="delay"]');
			$node = $nodelist->item(0);
			$delay = $node->nodeValue;


			// Name
			$nodelist = $xpath->query('//h2[@class="title"]');
			$node = $nodelist->item(0);
			$name = $node->nodeValue;

			// Price
			$node = $nodelist->item(1);
			$price = $node->nodeValue;

			// Color, Upholstery, Combustible
			$nodelist = $xpath->query('//p[@class="features"]');
			$node = $nodelist->item(0);
			$tmp = $node->nodeValue;

			$tmp = str_replace("Color : : ", "", $tmp);
			$pieces = explode(":", $tmp);

			$color = str_replace(" TapicerÃ­a ", "", $pieces[0]);
			$upholstery = str_replace("Combustible ", "", $pieces[1]);
			$combustible = ltrim($pieces[2]);

			// Consumo mixto
			$nodelist = $xpath->query('//span[@class="value"]');
			$node = $nodelist->item(0);
			$consumoMixto = $node->nodeValue;

			// Emisiones de
			$node = $nodelist->item(1);
			$emisionesDe = $node->nodeValue;

			// Reserved
			$nodelist = $xpath->query('//div[@class="w-ribbon"]');
			$node = $nodelist->item(0);
			$reserved = $node->nodeValue;

			// Options
			$optionUrl = 'http://www.carstore.citroen.es/Resultados?carNum='.$carNum.'&AjaxRequest=VehicleInfos_getOptionalFeatures';
			$optionHtml = file_get_contents($optionUrl);

			$options = explode("<br />", $optionHtml);

			Car::create([
				'name' => $name,
				'image_url' => $carImageUrl,
				'delay' => $delay,
				'price' => $price,
				'color' => $color,
				'upholstery' => $upholstery,
				'combustible' => $combustible,
				'consumo_mixto' => $consumoMixto,
				'emisiones_de' => $emisionesDe,
				'reserved' => $reserved,
				'options' => json_encode($options)
			]);
		}

		echo 'Success!!!';
    }
}
