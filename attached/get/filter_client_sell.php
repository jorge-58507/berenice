<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['term'];


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
$qry_client=mysql_query($txt_client." ORDER BY TX_cliente_nombre ASC");
$raw_cliente = array();
$i=0;
while($rs_cliente=mysql_fetch_assoc($qry_client)){
	$raw_cliente[$i]['id'] = $rs_cliente['AI_cliente_id'];
	$raw_cliente[$i]['value'] = $rs_cliente['TX_cliente_nombre'];
	$raw_cliente[$i]['telefono'] = $rs_cliente['TX_cliente_telefono'];
	$raw_cliente[$i]['direccion'] = $rs_cliente['TX_cliente_direccion'];
	$raw_cliente[$i]['ruc'] = $rs_cliente['TX_cliente_cif'];
$i++;
}

echo json_encode($raw_cliente);
?>
