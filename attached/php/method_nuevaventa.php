<?php
require '../../bh_conexion.php';
$link=conexion();

function read_nuevaventa_content(){
	$file = fopen("../../nva_venta.txt", "r");
	$contenido = fgets($file);
	fclose($file);
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_nuevaventa_content($contenido){
	$file = fopen("../../nva_venta.txt", "w+");
		fwrite($file, $contenido);
	fclose($file);
}
function plus_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();

	$product_id=$_GET['a'];
	$precio=$_GET['b'];
	$descuento=$_GET['c'];
	$itbm=$_GET['d'];
	$activo=$_GET['e'];
	$cantidad=$_GET['f'];
	$medida=$_GET['g'];

	$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
	$descripcion = $rs_product['TX_producto_value'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	if (!array_key_exists($activo, $raw_decode[$_COOKIE['coo_iuser']])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]= array();
	}
	if (!array_key_exists($product_id, $raw_decode[$_COOKIE['coo_iuser']][$activo])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['cantidad'] = $cantidad;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['precio'] = $precio;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['impuesto'] = $itbm;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['descuento'] = $descuento;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['descripcion'] = $descripcion;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['codigo'] = $rs_product['TX_producto_codigo'];
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['medida'] = $medida;
		$raw_decode[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]['stock'] = $rs_product['TX_producto_cantidad'];
	}
		$contenido = json_encode($raw_decode);
	write_nuevaventa_content($contenido);
	echo $contenido;
}

function upd_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$product_id=$_GET['a'];
	$value=$_GET['b'];
	$activo=$_GET['c'];
	$campo=$_GET['d'];

	$contenido=read_nuevaventa_content();
	$raw_nuevaventa=json_decode($contenido, true);
	$raw_nuevaventa[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"][$campo] = $r_function->replace_special_character($value);
	$contenido = json_encode($raw_nuevaventa);

	write_nuevaventa_content($contenido);
	echo $contenido;
}
function del_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$product_id=$_GET['a'];
	$activo=$_GET['b'];

	$contenido=read_nuevaventa_content();
	$raw_nuevaventa=json_decode($contenido, true);
	unset($raw_nuevaventa[$_COOKIE['coo_iuser']][$activo]["'".$product_id."'"]);
	$contenido = json_encode($raw_nuevaventa, true);

	write_nuevaventa_content($contenido);
	echo $contenido;
}
function reload_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$activo=$_GET['a'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);

	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	if (!array_key_exists($activo, $raw_decode[$_COOKIE['coo_iuser']])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]= array();
	}
	$contenido=json_encode($raw_decode);
	echo $contenido;
}

function duplicate_datoventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$facturaventa_id=$_GET['a'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);

	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {	$raw_decode[$_COOKIE['coo_iuser']]= array();	}

	unset($raw_decode[$_COOKIE['coo_iuser']]['first_sale']);

	$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_medida FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$facturaventa_id'")or die($link->error);
	while ($rs_datoventa=$qry_datoventa->fetch_array(MYSQLI_ASSOC)) {
		$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '{$rs_datoventa['datoventa_AI_producto_id']}'")or die($link->error);
		$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['cantidad'] = $rs_datoventa['TX_datoventa_cantidad'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['precio'] = $rs_datoventa['TX_datoventa_precio'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['impuesto'] = $rs_datoventa['TX_datoventa_impuesto'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['descuento'] = $rs_datoventa['TX_datoventa_descuento'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['descripcion'] = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['codigo'] = $rs_product['TX_producto_codigo'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['medida'] = $rs_datoventa['TX_datoventa_medida'];
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale']["'".$rs_datoventa['datoventa_AI_producto_id']."'"]['stock'] = $rs_product['TX_producto_cantidad'];
	}

	$contenido=json_encode($raw_decode);
	write_nuevaventa_content($contenido);
	echo $contenido;
}

$funct=$_GET['z'];
switch ($funct) {
	case 'plus':
		plus_nuevaventa();
		break;
	case 'upd':
		upd_nuevaventa();
		break;
	case 'del':
		del_nuevaventa();
		break;
	case 'reload':
		reload_nuevaventa();
		break;
	case 'duplicate':
		duplicate_datoventa();
		break;
  //
	// default:
	// 	# code...
	// 	break;
}

?>
