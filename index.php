<?php
require('vendor/advanced_html_dom.php');
//require('vendor/simple_html_dom.php');
ini_set("default_charset",'utf-8');//utf-8
ini_set('display_errors', 1);

$time_milliB = (int) round(microtime(true) * 1000);

$myfileresult = fopen('download.csv','w+');
echo fwrite($myfileresult,"");
fclose($myfileresult);

$id  = 1733;
$ids = $id;
$success = 0;
while ($id < 1737) {
	$html_text2      = file_get_contents('http://taranko-shop.ru/'.$ids);// OR die('not found');
	if (gettype($html_text2) === 'boolean') {
		echo '404 '.$ids.'<br>';
		$success = 0;
		if ($success == 0) {
			$id = $id+1;
		}
		$ids = $id;
	} else {
		echo $ids."<br>";
		$html_text       = $html_text2;
		$html            = str_get_html($html_text);
    	$success = 1;
    	$ids = $ids.'-1';
    	//ALL WORK IS HERE
    	
    	//image1 alternative search and name of the item
		$n = 0;
		foreach ($html->find('#product-image') as $imageAlt) {
				//echo ' partial success ';
				$imageLinkAlt = "http://taranko-shop.ru".$imageAlt->src;
				$itemNameAlt  = $imageAlt->alt;
				echo "src is ".$imageLinkAlt." and item name is ".$itemNameAlt."<br>";
		}
		//item price
		foreach ($html->find('span') as $priceSuggest) {
				
				$price = $priceSuggest->getAttribute("data-price");
				if ($price!='') {	
					$price = $priceSuggest->getAttribute("data-price");
					echo "price is ".$price."<br>";
				}
		}
    	//END OF MAIN WORK
	}
}

$time_milliE = (int) round(microtime(true) * 1000);
$time_elapsed = $time_milliE - $time_milliB;
echo " time elapsed ".$time_elapsed;

?>
