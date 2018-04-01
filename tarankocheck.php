<?php
if (is_file('config.php')) {
        require_once('taranko_config.php');
}
ini_set("default_charset",'utf-8');//utf-8

$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
/* change character set to utf8 */
if (!mysqli_set_charset($conn, "utf8")) {
  //  printf("Error loading character set utf8: %s\n", mysqli_error($conn));
    exit();
} else {
  //  printf("Current character set: %s\n", mysqli_character_set_name($conn));
}

$query_line = "SELECT * FROM progress";
$query = mysqli_query($conn, $query_line);

$field = mysqli_field_count($conn);
// loop through database query
while($row = mysqli_fetch_array($query)) {
    for($i = 0; $i < $field; $i++) {
		$variable	= $row[mysqli_fetch_field_direct($query, $i)->name];
		echo $variable." ";
	}	
}
?>
