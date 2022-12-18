<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora,
	bh_cliente.TX_cliente_nombre, bh_entrega.AI_entrega_id
	FROM ((bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
	INNER JOIN bh_entrega ON bh_facturaf.AI_facturaf_id = bh_entrega.entrega_AI_facturaf_id)
	WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}


$txt_facturaf=$txt_facturaf." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturaf .= " GROUP BY AI_facturaf_id	ORDER BY AI_facturaf_id DESC".$line_limit;
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$raw_return = array();
while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
	$raw_return[]=$rs_facturaf;
}

echo json_encode($raw_return);
