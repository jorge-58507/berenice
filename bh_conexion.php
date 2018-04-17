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
		$to_replace = array("&ampersand;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&nolger;","\'","\'\'","&deg;","&ntilde;","\'","laremun;");
		$replacement = array("&","Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","'","''","º","ñ","'","#");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function replace_regular_character($str){
		$to_replace = array("&","Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","'","''","º","ñ","\\\'","#");
		$replacement = array("&ampersand;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&nolger;","\'","\'\'","&deg;","&ntilde;","\'","laremun;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function url_replace_special_character($str){
		$to_replace = array("Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremun;","nolger;","\'","\'\'","deg;","ntilde;","\'","ampersand;");
		$replacement = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","''","º","ñ","'","&");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function url_replace_regular_character($str){
		$to_replace = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","''","º","ñ","\\\'","&");
		$replacement = array("Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremunx;","nolger;","\'","\'\'","deg;","ntilde;","\'","ampersand;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
}
$r_function = new recurrent_function();

?>
