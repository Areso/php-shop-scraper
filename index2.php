<?php
require('vendor/advanced_html_dom.php');
//require('vendor/simple_html_dom.php');


ini_set("default_charset",'utf-8');//utf-8
ini_set('display_errors', 1);

$myfile = fopen('newtestpage.html','r');
$mystring = fread($myfile,filesize('newtestpage.html'));
fclose($myfile);

//$mystring = readfile("testpage.html");

$time_milliB = (int) round(microtime(true) * 1000);
$id  = 1000;
$ids = $id;
$success = 0;
$html_text2      = file_get_contents('http://taranko-shop.ru/'.$ids);// OR die('not found');
//echo gettype($html_text2);
if (gettype($html_text2) === 'boolean') {
	echo '404';
	if ($success == 0) {
		$id = $id+1;
	}
} else {
	$id = $id.'-1';
}
$html_text       = $html_text2;
//echo $html_text;
$success = 1;
//OR
//$html_text = $mystring;

//$myfilew = fopen("newtestpage.html", "w");
//fwrite($myfilew, $html_text2);
//fclose($myfilew);

//echo "length of source file is ".strlen($html_text)." and length of http source is ".strlen($html_text2)."<br>";

//header('Content-Type: text/csv; charset=utf-8');
//header('Content-Disposition: attachment; filename=data.csv');
// create a file pointer connected to the output stream
//$output = fopen('php://output', 'w');
// output the column headings
//fputcsv($output, array($html_text2, ''));

// Create DOM from URL or file
//$html           = file_get_html('http://taranko-shop.ru/1700/');
$html           = str_get_html($html_text);
//echo "html variable is ".isset($html);

/*
$methodsStr = '';
$methods = array();
foreach (get_class_methods($html) as $method) {
    //if (strpos($method, "bla_") === 0) {
        $methods[] = $method;
        $methodsStr = $methodsStr.' '.$method;
    //}
}
//$methodsStr = (string) $methods;
echo $methodsStr;
*/

$myfileresult = fopen('download.csv','w+');
echo fwrite($myfileresult,"");
fclose($myfileresult);
 
//image1 alternative search and name of the item
$n = 0;
foreach ($html->find('#product-image') as $imageAlt) {
        //echo ' partial success ';
        $imageLinkAlt = "http://taranko-shop.ru".$imageAlt->src;
        $itemNameAlt  = $imageAlt->alt;
        echo "src is ".$imageLinkAlt." and item name is ".$itemNameAlt."<br>";
}
 
/*
//image1
$imagePos       = strpos($html_text, "image selected");
$imageSubStr    = substr($html_text, $imagePos, 500);
$imageHrefBegin = strpos($imageSubStr, "href=");
$imageHrefEnd   = strpos($imageSubStr, "class");
$imageStrLen    = $imageHrefEnd - $imageHrefBegin;
$imageLink      = substr($imageSubStr, $imageHrefBegin+6, $imageStrLen-8);
$imageLinkDomain= "http://taranko-shop.ru".$imageLink;
echo "image link is ".$imageLinkDomain."<br>";



//item name
$itemNamePos       = strpos($html_text, "span itemprop");
$itemNameEnd       = strpos($html_text, "span", $itemNamePos+1);
//echo "itemNamePos is ".$itemNamePos." and item name end is ".$itemNameEnd."<br>";
$itemNameLen       = $itemNameEnd - $itemNamePos;   
$itemName          = substr($html_text, $itemNamePos+21, $itemNameLen-8);
echo "itemName is ".$itemName."<br>";
*/

//item price
foreach ($html->find('span') as $priceSuggest) {
	    
	    $price = $priceSuggest->getAttribute("data-price");
        if ($price!='') {	
			$price = $priceSuggest->getAttribute("data-price");
			echo "price is ".$price."<br>";
		}
}
//description
/*
foreach ($html->find('#product-description') as $descr) {
        //echo ' partial success ';
        echo $descr."<br>";
}
*/ 

//big-descr
foreach ($html->find('#product-features') as $features) {
		$features = strip_tags($features);
        echo $features."<br>";
}
$features_ready = false;
while ($features_ready === false) {
	$old_features = $features;
	echo "lets do some work"."<br>";
	$features = preg_replace( "/\r|\n/", "", $features );
	$features = str_replace("  ", " ", $features);	
	if 	($features === $old_features) {
		$features_ready = true;
	}
}

//modifications
$mods = [];
foreach ($html->find('meta[itemprop=name]') as $metas) {
        $mods[] = strip_tags($metas->getAttribute("content"));
        echo strip_tags($metas->getAttribute("content"))." <br>";
}
$modsCount = count($mods);

$prices = [];
$n = 0;
foreach ($html->find('meta[itemprop=price]') as $metas) {
	    if ($n < $modsCount) {
			$prices[] = strip_tags($metas->getAttribute("content"));
			echo strip_tags($metas->getAttribute("content"))." <br>";
			$n = $n + 1;
		}
}

$n = 0;
$stockNumber = [];
foreach ($html->find('.stock-none, .stock-critical, .stock-low, .stock-high') as $stock) {
	    if ($n < $modsCount) {
			$stockNumber[$n] = $stock->plaintext; //text based description
			/*
			$stockNumber = filter_var($stock->plaintext, FILTER_SANITIZE_NUMBER_INT);
			if ($stockNumber == '') {
					$stockNumber = 0;
			}
			//Нет в наличии, 1 штука, 2 штуки, Несколько штук, В наличии
			*/ 
			//echo $stock->plaintext." <br>";
			echo $stockNumber[$n]."<br>";
			$n = $n + 1;
		}
}

$html->clear();
unset($html);
//$html_text->clear();
unset($html_text);



//$myfileresult = fopen('download.csv','w+');
//echo fwrite($myfileresult,"Hello World. Testing!");
//fclose($myfileresult);

for ($x = 0; $x < $modsCount; ) {
    if ($stockNumber[$x] !== "Нет в наличии") {
		//work here
		//w+ and then append
		//or PHP_EOL
		//echo "<br>this is new world! id ".$x." and stockNumber is ".$stockNumber[$x];
		$myfileresult = fopen('download.csv','a');
		fwrite($myfileresult, $id.";".$imageLinkAlt.";".$itemNameAlt.";".$features.";".$mods[$x].";".$prices[$x].";".$stockNumber[$x].PHP_EOL);
		fclose($myfileresult);

	} else {
		//drop
		echo "<br>this is not new world!";
	}
	$x = $x +1;
} 

$time_milliE = (int) round(microtime(true) * 1000);
$time_elapsed = $time_milliE - $time_milliB;
echo " time elapsed ".$time_elapsed;

//var_dump($myvar);
?>