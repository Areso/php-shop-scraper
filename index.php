<?php
require('vendor/advanced_html_dom.php');

// Create DOM from URL or file
$html      = file_get_html('http://taranko-shop.ru/1700/');
$html_text = file_get_contents('http://taranko-shop.ru/1700/');

$imagePos  = strpos($html_text, "image selected");
echo "imagePos is ".$imagePos; 

foreach ($html->find('.image.selected', 0) as $image) {
        echo 'partial success';
}

$html->clear();
unset($html);
$html_text->clear();
unset($html_text);

//var_dump($myvar);
?>
