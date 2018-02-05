<?php
require '../../bh_con.php';
$link = conexion();
function edit_quote($str){
	$pat = array("\"", "'", "º", "laremun");
	$rep = array("''", "\'", "&deg;", "#");
	return $n_str = str_replace($pat, $rep, $str);
}

$raw_value=$_GET['a'];

foreach($raw_value as $index => $value){
	mysql_query("UPDATE bh_opcion SET TX_opcion_value='$value' WHERE AI_opcion_id = '$index'")or die(mysql_error());
//echo "UPDATE bh_opcion SET TX_opcion_value='$value' WHERE AI_opcion_id = '$index' <br />";
}
?>
