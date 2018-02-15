<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$client_id=$_GET['b'];
$client=$_GET['c'];
$time=strtotime($_GET['a']);
$date=date('Y-m-d');
$vendor_id=$_GET['d'];
$total=$_GET['f'];
$observation=$_GET['g'];
$status=$_GET['h'];

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento) VALUES (?,?,?,?,?,?,?)")or die($link->error);

// ######################## FUNCIONES  ###########################
function checkfacturaventa($numero){
	$link = conexion();
	$qry_checkfacturaventa=$link->query("SELECT AI_facturaventa_id FROM bh_facturaventa WHERE TX_facturaventa_numero = '$numero'")or die($link->error);
	$nr_checkfacturaventa=$qry_checkfacturaventa->num_rows;
	$link->close();
	if($nr_checkfacturaventa > 0){
		return sumarfacturaventa($numero);
	}else{
		return $numero;
	}
}
function sumarfacturaventa($numero){
	return checkfacturaventa($numero+1);
}

$qry_facturaventa_numero=$link->query("SELECT AI_facturaventa_id, TX_facturaventa_numero FROM bh_facturaventa ORDER BY AI_facturaventa_id DESC LIMIT 1")or die($link->error);
$rs_facturaventa_numero=$qry_facturaventa_numero->fetch_array();
$number = $rs_facturaventa_numero['TX_facturaventa_numero'];
$number=checkfacturaventa($number);

$qry_chkexento = $link->query("SELECT AI_cliente_id FROM bh_cliente WHERE AI_cliente_id = '$client_id' AND TX_cliente_exento = '1'")or die($link->error);
$nr_chkexento = $qry_chkexento->num_rows;

$qry_nuevaventa=$link->query("SELECT nuevaventa_AI_producto_id, TX_nuevaventa_unidades, TX_nuevaventa_precio, TX_nuevaventa_descuento, TX_nuevaventa_itbm FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$total=0; $i=0; $raw_nuevaventa=array();
while($rs_nuevaventa=$qry_nuevaventa->fetch_array()){
	$precio = $rs_nuevaventa['TX_nuevaventa_unidades']*$rs_nuevaventa['TX_nuevaventa_precio'];
	$descuento = ($precio*$rs_nuevaventa['TX_nuevaventa_descuento'])/100;
	$precio_descuento = $precio-$descuento;
	$impuesto = ($precio_descuento*$rs_nuevaventa['TX_nuevaventa_itbm'])/100;
	$precio_impuesto = $precio_descuento+$impuesto;
	$total += $precio_impuesto;
	$raw_nuevaventa[$i]['producto']=$rs_nuevaventa['nuevaventa_AI_producto_id'];
	$raw_nuevaventa[$i]['cantidad']=$rs_nuevaventa['TX_nuevaventa_unidades'];
	$raw_nuevaventa[$i]['precio']=$rs_nuevaventa['TX_nuevaventa_precio'];
	$raw_nuevaventa[$i]['descuento']=$rs_nuevaventa['TX_nuevaventa_descuento'];
	if($nr_chkexento > 0){
		$raw_nuevaventa[$i]['impuesto']=0;
	}else{
		$raw_nuevaventa[$i]['impuesto']=$rs_nuevaventa['TX_nuevaventa_itbm'];
	}
	$i++;
}
$total=round($total,2);

$link->query("INSERT INTO bh_facturaventa (TX_facturaventa_fecha, facturaventa_AI_cliente_id, facturaventa_AI_user_id, TX_facturaventa_numero, TX_facturaventa_total, TX_facturaventa_status, TX_facturaventa_observacion) VALUES ('$date', '$client_id', '{$_COOKIE['coo_iuser']}', '$number', '$total', '$status', '$observation')")or die($link->error);

$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
$rs_lastid = $qry_lastid->fetch_array();
$last_facturaventaid = trim($rs_lastid[0]);

foreach ($raw_nuevaventa as $key => $value) {
	$qry_ins_datoventa->bind_param("iiidddd",$last_facturaventaid,$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento']);
	$qry_ins_datoventa->execute();
}
echo $last_facturaventaid;

$link->query("DELETE FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);

$link->close()
?>
