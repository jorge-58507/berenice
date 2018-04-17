<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$value=$r_function->replace_regular_character($_GET['term']);
$date_i = date('Y-m-d',strtotime('-12 week'));
$date_f = date('Y-m-d');

$prep_cliente_datoventa=$link->prepare("SELECT TX_datoventa_descripcion, SUM(TX_datoventa_cantidad) as conteo, datoventa_AI_producto_id, TX_datoventa_precio FROM bh_datoventa INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id WHERE facturaventa_AI_cliente_id = ? AND bh_facturaventa.TX_facturaventa_fecha >= ? AND bh_facturaventa.TX_facturaventa_fecha <= ? GROUP BY TX_datoventa_descripcion ORDER BY conteo DESC LIMIT 8");

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_client="SELECT * FROM bh_cliente WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}
$qry_client=$link->query($txt_client." ORDER BY TX_cliente_nombre ASC LIMIT 10")or die($link->error);
$raw_cliente = array();
$i=0;
while($rs_cliente=$qry_client->fetch_array(MYSQLI_ASSOC)){
	if (substr_count($rs_cliente['TX_cliente_nombre'], 'NO USAR') < 1 ) {
		$prep_cliente_datoventa->bind_param("iss",$rs_cliente['AI_cliente_id'],$date_i,$date_f); $prep_cliente_datoventa->execute(); $qry_cliente_datoventa = $prep_cliente_datoventa->get_result();
		$raw_favorito=array();
		while ($rs_cliente_datoventa=$qry_cliente_datoventa->fetch_array(MYSQLI_ASSOC)) {
			$raw_favorito[]=$rs_cliente_datoventa;
		}

		$raw_cliente[$i]['id'] = $rs_cliente['AI_cliente_id'];
		$raw_cliente[$i]['value'] = $r_function->replace_special_character($rs_cliente['TX_cliente_nombre'])." | Dir: ".$rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['telefono'] = $rs_cliente['TX_cliente_telefono'];
		$raw_cliente[$i]['direccion'] = $rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['ruc'] = $rs_cliente['TX_cliente_cif'];
		$raw_cliente[$i]['json_favorito'] = json_encode($raw_favorito);
		$i++;
	}
}


echo json_encode($raw_cliente);
?>
