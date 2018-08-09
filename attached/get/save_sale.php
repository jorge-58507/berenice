<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

function read_nuevaventa_content(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function read_nuevaventa_rel(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 2")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_nuevaventa_content($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	// $contenido = $r_function->replace_regular_character($contenido);
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
}
function write_nuevaventa_rel($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	// $contenido = $r_function->replace_regular_character($contenido);
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 2")or die($link->error);
}

$client_id=$_GET['b'];
$client=$_GET['c'];
$date=date('Y-m-d',strtotime($_GET['a']));
$vendor_id=$_GET['d'];
$observation=$_GET['g'];
$status=$_GET['h'];
$activo=$_GET['i'];


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

$contenido = read_nuevaventa_content();
$raw_decode=json_decode($contenido, true);
if(!is_array($raw_decode)){ 
	echo "failed";
	return false;
}
$raw_contenido = $raw_decode[$_COOKIE['coo_iuser']][$activo];

$total=0; $i=0; $raw_nuevaventa=array();
foreach ($raw_contenido as $key => $line_nuevaventa) {
	$precio = $line_nuevaventa['cantidad']*$line_nuevaventa['precio'];
	$descuento = ($precio*$line_nuevaventa['descuento'])/100;
	$precio_descuento = $precio-$descuento;
	$impuesto = ($precio_descuento*$line_nuevaventa['impuesto'])/100;
	$precio_impuesto = $precio_descuento+$impuesto;
	$total += $precio_impuesto;

	$raw_nuevaventa[$i]['nuevaventa_indice']=$key;
	$raw_nuevaventa[$i]['producto']=$line_nuevaventa['producto_id'];
	$raw_nuevaventa[$i]['cantidad']=$line_nuevaventa['cantidad'];
	$raw_nuevaventa[$i]['precio']=$line_nuevaventa['precio'];
	$raw_nuevaventa[$i]['descuento']=$line_nuevaventa['descuento'];
	$raw_nuevaventa[$i]['impuesto'] = ($nr_chkexento > 0) ? 0 : $line_nuevaventa['impuesto'];
	$raw_nuevaventa[$i]['descripcion']=$line_nuevaventa['descripcion'];
	$raw_nuevaventa[$i]['stock']=$line_nuevaventa['stock'];
	$raw_nuevaventa[$i]['medida']=$line_nuevaventa['medida'];
	$raw_nuevaventa[$i]['promocion']=$line_nuevaventa['promocion'];
	$i++;
}
$total=round($total,2);

$link->query("INSERT INTO bh_facturaventa (TX_facturaventa_fecha, facturaventa_AI_cliente_id, facturaventa_AI_user_id, TX_facturaventa_numero, TX_facturaventa_total, TX_facturaventa_status, TX_facturaventa_observacion) VALUES ('$date', '$client_id', '{$_COOKIE['coo_iuser']}', '$number', '$total', '$status', '$observation')")or die($link->error);
$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
$rs_lastid = $qry_lastid->fetch_array();
$last_facturaventaid = trim($rs_lastid[0]);

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_stock, TX_datoventa_medida, TX_datoventa_promocion) VALUES (?,?,?,?,?,?,?,?,?,?,?)")or die($link->error);

$raw_datoventa_inserted = array();
foreach ($raw_nuevaventa as $key => $value) {
	$value['descripcion'] = $r_function->replace_regular_character($value['descripcion']);
	$qry_ins_datoventa->bind_param("iisddddssii",$last_facturaventaid,$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento'],$value['descripcion'],$value['stock'],$value['medida'],$value['promocion']);
	$qry_ins_datoventa->execute();

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();

	$raw_datoventa_inserted[$value['nuevaventa_indice']] = trim($rs_lastid[0]);
}
echo $last_facturaventaid;
// ###### RELACIONES EN DATOVENTA
$contenido_nuevaventarel=read_nuevaventa_rel();
$raw_contenido_nuevaventarel=json_decode($contenido_nuevaventarel, true);
$raw_datoventa_relacionado = array();
if (!empty($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo])) {
	foreach ($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo] as $key => $string_related) {
		$chain_related='';
		$raw_related = explode(",", $string_related);
		foreach ($raw_related as $key => $value) {
			$chain_related .= ($value === end($raw_related))  ? $raw_datoventa_inserted[$value] : $raw_datoventa_inserted[$value].",";
		}
		$raw_datoventa_relacionado[] = $chain_related;
	}
}
$rel_contenido = json_encode($raw_datoventa_relacionado);
$link->query("UPDATE bh_facturaventa SET TX_facturaventa_promocion = '$rel_contenido' WHERE AI_facturaventa_id = '$last_facturaventaid'")or die($link->error);
unset($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo]);
$contenido_nuevaventarel=json_encode($raw_contenido_nuevaventarel);
write_nuevaventa_rel($contenido_nuevaventarel);

unset($raw_decode[$_COOKIE['coo_iuser']][$activo]);
$contenido = json_encode($raw_decode, true);

write_nuevaventa_content($contenido);

$link->close()

?>
