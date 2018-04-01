<?php
//#INIT OUR SYSTEM
require('vendor/advanced_html_dom.php');
//require('vendor/simple_html_dom.php');
if (is_file('taranko_config.php')) {
        require_once('taranko_config.php');
}
ini_set("default_charset",'utf-8');//utf-8
ini_set('display_errors', 1);
error_reporting(E_ALL); 
set_time_limit(20000);
#CHECK TIME
$time_milliB = (int) round(microtime(true) * 1000);

//#FLUSH PROGRESS

$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
// change character set to utf8 //
if (!mysqli_set_charset($conn, "utf8")) {
  //  printf("Error loading character set utf8: %s\n", mysqli_error($conn));
    exit();
} else {
  //  printf("Current character set: %s\n", mysqli_character_set_name($conn));
}
//#database taranko, table progress, record_id (uniq, PK, int, not null), page_id (int, not null), status (nvarchar(20))
//#default values 1, 0, not started yet
$query_line = "UPDATE progress 
 SET page_id = 0, status = 'not started yet'  
 WHERE record_id  = 1";
$query = mysqli_query($conn, $query_line);	
			
$myfileresult = fopen('taranko.csv','w+');
echo fwrite($myfileresult,"");
fclose($myfileresult);

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        $networkpos = strpos($errstr,"failed to open stream: php_network_getaddresses");
        if ($networkpos!==false) {
			echo "Unknown error type: [$errno] $errstr<br />\n";
			$myfileresult = fopen('taranko.csv','a');
			fwrite($myfileresult, $errstr." didn't checked due to the remote server error;".PHP_EOL);
			fclose($myfileresult);	
		}
        break;
    }

    // Don't execute PHP internal error handler //
    return true;
}
$old_error_handler = set_error_handler("myErrorHandler");

$id    = ($_POST["idstart"]);
$idend = ($_POST["idend"]);

//$id  = 1100;
$ids = $id;
$success = 0;
while ($id < $idend) {
	$html_text2      = file_get_contents('http://taranko-shop.ru/'.$ids);// OR die('not found');
	if (gettype($html_text2) === 'boolean') {
		echo '404 '.$ids.'<br>';
		$success = 0;
		if ($success == 0) {
			$id = $id+1;
		}
		$ids = $id;
		usleep(250000);
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
		//prices
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
		//stock item quantity
		$stockQty = [];
		foreach ($html->find('.stock-none, .stock-critical, .stock-low, .stock-high') as $stock) {
				if ($n < $modsCount) {
					$stockQty[$n] = $stock->plaintext; //text based description
					
					//$stockQty = filter_var($stock->plaintext, FILTER_SANITIZE_NUMBER_INT);
					//if ($stockQty == '') {
					//		$stockQty = 0;
					//}
					//Нет в наличии, 1 штука, 2 штуки, Несколько штук, В наличии
					 
					//echo $stock->plaintext." <br>";
					echo $stockQty[$n]."<br>";
					$n = $n + 1;
				}
		}
		for ($x = 0; $x < $modsCount; ) {
			if ($stockQty[$x] !== "Нет в наличии") {
				$myfileresult = fopen('taranko.csv','a');
				fwrite($myfileresult, $ids.";".$imageLinkAlt.";".$itemNameAlt.";".$features.";".$mods[$x].";".$prices[$x].";".$stockQty[$x].PHP_EOL);
				fclose($myfileresult);
			} else {
				//drop
			}
			$x = $x +1;
		}
		//clear workspace 
		$html->clear();
		unset($html);
		//$html_text->clear();
		unset($html_text);
		usleep(250000);
		//END OF MAIN WORK
	}
	if ($id % 10 === 0) {
		$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);
        $query_line = "UPDATE progress 
		 SET page_id = ".$id.", status = 'in progress'
		 WHERE record_id  = 1";
		 echo $query_line."<br>";
		 //mysqli_stmt_execute($query);
		$query = mysqli_query($conn, $query_line);
		echo "query result is ".$query."<br>";	
	}
}
$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);
$query_line = "UPDATE progress 
 SET page_id = ".$id.", status = 'job finished'
 WHERE record_id  = 1";
 echo $query_line."<br>";
$query = mysqli_query($conn, $query_line);
echo "query result is ".$query."<br>";

$time_milliE = (int) round(microtime(true) * 1000);
$time_elapsed = $time_milliE - $time_milliB;
echo " time elapsed ".$time_elapsed;
?>
