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

class recurrent_function{
	public function replace_special_character($str){
		$special_char = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&laremun;","&nolger;","\'","&deg;","&ntilde;");
		$replace = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","º","ñ");
		return $value = str_replace($special_char,$replace,$str);
	}
	public function replace_regular_character($str){
		$special_char = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","º","ñ");
		$replace = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&laremun;","&nolger;","\'","&deg;","&ntilde;");
		return $value = str_replace($special_char,$replace,$str);
	}
}
$r_function = new recurrent_function();

?>
