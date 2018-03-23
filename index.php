<?php
require('vendor/advanced_html_dom.php');
//require('source.php');

ini_set("default_charset",'utf-8');//utf-8
ini_set('display_errors', 1);

$myfile = fopen('newtestpage.html','r');
$mystring = fread($myfile,filesize('testpage.html'));
fclose($myfile);

//$mystring = readfile("testpage.html");

$time_milliB = (int) round(microtime(true) * 1000);

//$html_text2      = file_get_contents('http://taranko-shop.ru/1700/');
$html_text = $mystring;

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
        //echo ' partial success ';
        echo strip_tags($features)."<br>";
}
//find(' img [width]'); 

$html->clear();
unset($html);
//$html_text->clear();
unset($html_text);


$time_milliE = (int) round(microtime(true) * 1000);
$time_elapsed = $time_milliE - $time_milliB;
echo " time elapsed ".$time_elapsed;

//var_dump($myvar);
?>
