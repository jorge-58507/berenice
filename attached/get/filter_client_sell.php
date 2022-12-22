<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$value=$r_function->replace_regular_character($_GET['term']);

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_client="SELECT * FROM bh_cliente WHERE ";
foreach ($arr_value as $key => $value) {
	$txt_client .= ($value === end($arr_value)) ? "TX_cliente_nombre LIKE '%{$value}%' OR " : "TX_cliente_nombre LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_client .= ($value === end($arr_value)) ? "TX_cliente_cif LIKE '%{$value}%'" : "TX_cliente_cif LIKE '%{$value}%' AND ";
}
$qry_client=$link->query($txt_client." ORDER BY TX_cliente_nombre ASC LIMIT 15")or die($link->error);
$raw_cliente = array();
$i=0;
while($rs_cliente=$qry_client->fetch_array(MYSQLI_ASSOC)){
	if (substr_count($rs_cliente['TX_cliente_nombre'], 'NO USAR') < 1 ) {
		$raw_cliente[$i]['id'] = $rs_cliente['AI_cliente_id']; 
		$raw_cliente[$i]['value'] = $r_function->replace_special_character_no_html($rs_cliente['TX_cliente_nombre'])." | Dir: ".$rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['telefono'] = $rs_cliente['TX_cliente_telefono'];
		$raw_cliente[$i]['direccion'] = $rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['ruc'] = $rs_cliente['TX_cliente_cif'];
		$i++;
	}
}


echo json_encode($raw_cliente);
?>
