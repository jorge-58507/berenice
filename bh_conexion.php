<?php
function conexion(){
	$mysqli = new mysqli('127.0.0.1', 'root', '', 'bill_helper');
	if ($mysqli->connect_errno) {
	    echo "Lo sentimos, este sitio web está experimentando problemas.";
	    echo "Error: Fallo al conectarse a MySQL debido a: \n";
	    echo "Errno: " . $mysqli->connect_errno . "\n";
	    echo "Error: " . $mysqli->connect_error . "\n";
	    exit;
	}
return $mysqli;
}
?>
