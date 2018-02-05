<?php
function conexion(){
	if(!($link=@mysql_connect("localhost","root"))){
		echo "Error conectando a la base de datos.".mysql_error();
		exit();
	}
	if(!mysql_select_db("bill_helper",$link)){
		echo "Error Seleccionando la base de datos. ".mysql_error();
		exit();
	}
	return $link;
}
?>
