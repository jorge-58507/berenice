<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

// $time=strtotime($_GET['a']);
$client_id=$_GET['b'];
$client=$_GET['c'];
$date=date('Y-m-d',strtotime($_GET['a']));
$vendor_id=$_GET['d'];
$observation=$_GET['g'];
$status=$_GET['h'];
$activo=$_GET['i'];

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_stock) VALUES (?,?,?,?,?,?,?,?,?)")or die($link->error);

// $prep_product = $link->prepare("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);

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

$qry_nuevaventa=$link->query("SELECT nuevaventa_AI_producto_id, TX_nuevaventa_unidades, TX_nuevaventa_precio, TX_nuevaventa_descuento, TX_nuevaventa_itbm, TX_nuevaventa_descripcion FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);

$file = fopen("../../nva_venta.txt", "r");
$contenido = fgets($file);
fclose($file);
$raw_decode=json_decode($contenido, true);
$raw_contenido = $raw_decode[$_COOKIE['coo_iuser']][$activo];

$total=0; $i=0; $raw_nuevaventa=array();
foreach ($raw_contenido as $key => $line_nuevaventa) {
	$precio = $line_nuevaventa['cantidad']*$line_nuevaventa['precio'];
	$descuento = ($precio*$line_nuevaventa['descuento'])/100;
	$precio_descuento = $precio-$descuento;
	$impuesto = ($precio_descuento*$line_nuevaventa['impuesto'])/100;
	$precio_impuesto = $precio_descuento+$impuesto;
	$total += $precio_impuesto;

	$key = str_replace("'","",$key);
	$raw_nuevaventa[$i]['producto']=$key;
	$raw_nuevaventa[$i]['cantidad']=$line_nuevaventa['cantidad'];
	$raw_nuevaventa[$i]['precio']=$line_nuevaventa['precio'];
	$raw_nuevaventa[$i]['descuento']=$line_nuevaventa['descuento'];
	if($nr_chkexento > 0){
		$raw_nuevaventa[$i]['impuesto']=0;
	}else{
		$raw_nuevaventa[$i]['impuesto']=$line_nuevaventa['impuesto'];
	}
	$raw_nuevaventa[$i]['descripcion']=$line_nuevaventa['descripcion'];
	$raw_nuevaventa[$i]['stock']=$line_nuevaventa['stock'];

	$i++;
}
$total=round($total,2);

$link->query("INSERT INTO bh_facturaventa (TX_facturaventa_fecha, facturaventa_AI_cliente_id, facturaventa_AI_user_id, TX_facturaventa_numero, TX_facturaventa_total, TX_facturaventa_status, TX_facturaventa_observacion) VALUES ('$date', '$client_id', '{$_COOKIE['coo_iuser']}', '$number', '$total', '$status', '$observation')")or die($link->error);

$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
$rs_lastid = $qry_lastid->fetch_array();
$last_facturaventaid = trim($rs_lastid[0]);

foreach ($raw_nuevaventa as $key => $value) {
	$value['descripcion'] = $r_function->replace_regular_character($value['descripcion']);
	$qry_ins_datoventa->bind_param("iisddddss",$last_facturaventaid,$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento'],$value['descripcion'],$value['stock']);
	$qry_ins_datoventa->execute();
}
echo $last_facturaventaid;

unset($raw_decode[$_COOKIE['coo_iuser']][$activo]);
$contenido = json_encode($raw_decode, true);

$file = fopen("../../nva_venta.txt", "w+");
	fwrite($file, $contenido);
fclose($file);

$link->close()

?>
