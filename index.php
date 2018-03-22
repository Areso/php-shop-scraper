<?php
require('vendor/advanced_html_dom.php');
ini_set("default_charset",'utf-8');//utf-8
ini_set('display_errors', 1);


// Create DOM from URL or file
$html           = file_get_html('http://taranko-shop.ru/1700/');
$html_text      = file_get_contents('http://taranko-shop.ru/1700/');

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


foreach ($html->find('.image.selected', 0) as $image) {
        echo 'partial success';
}

$html->clear();
unset($html);
//$html_text->clear();
unset($html_text);

//var_dump($myvar);
?>
