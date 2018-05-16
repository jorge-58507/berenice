<?php
require '../../bh_conexion.php';
$link = conexion();
$date=date('Y-m-d',strtotime($_GET['a']));
$cliente_id=$_GET['b'];
$facturaventa_id=$_GET['c'];
$observation=$_GET['d'];
//   #################################### FUNCIONES
function read_viejaventa_content(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function read_viejaventa_rel(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_viejaventa_content($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
}
function write_viejaventa_rel($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$contenido = $r_function->replace_regular_character($contenido);
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
}
//   #################################### FIN DE FUNCIONES
$qry_facturaventa=$link->query("SELECT AI_facturaventa_id FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id' AND facturaventa_AI_facturaf_id is NULL")or die($link->error);
if ($qry_facturaventa->num_rows < 1) {
	echo "denied";
	return false;
}

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_stock, TX_datoventa_medida, TX_datoventa_promocion) VALUES (?,?,?,?,?,?,?,?,?,?,?)")or die($link->error);

$qry_chkexento = $link->query("SELECT AI_cliente_id FROM bh_cliente WHERE AI_cliente_id = '$cliente_id' AND TX_cliente_exento = '1'")or die($link->error);
$nr_chkexento = $qry_chkexento->num_rows;

$contenido_viejaventa=read_viejaventa_content();
$raw_viejaventa=json_decode($contenido_viejaventa, true);
$total=0; $i=0; $raw_nuevaventa=array();
foreach ($raw_viejaventa[$_COOKIE['coo_iuser']] as $key => $rs_viejaventa) {
	$precio = $rs_viejaventa['cantidad']*$rs_viejaventa['precio'];
	$descuento = ($precio*$rs_viejaventa['descuento'])/100;
	$precio_descuento = $precio-$descuento;
	$impuesto = ($precio_descuento*$rs_viejaventa['impuesto'])/100;
	$precio_impuesto = $precio_descuento+$impuesto;
	$total += $precio_impuesto;

	$raw_nuevaventa[$i]['viejaventa_indice']=$key;
	$raw_nuevaventa[$i]['producto']=$rs_viejaventa['producto_id'];
	$raw_nuevaventa[$i]['cantidad']=$rs_viejaventa['cantidad'];
	$raw_nuevaventa[$i]['precio']=$rs_viejaventa['precio'];
	$raw_nuevaventa[$i]['descuento']=$rs_viejaventa['descuento'];
	$raw_nuevaventa[$i]['impuesto'] = ($nr_chkexento > 0) ? 0 : $rs_viejaventa['impuesto'];
	$raw_nuevaventa[$i]['descripcion']=$rs_viejaventa['descripcion'];
	$raw_nuevaventa[$i]['stock']=$rs_viejaventa['stock'];
	$raw_nuevaventa[$i]['medida']=$rs_viejaventa['medida'];
	$raw_nuevaventa[$i]['promocion']=$rs_viejaventa['promocion'];
	$i++;
}
unset($raw_viejaventa[$_COOKIE['coo_iuser']]);
$contenido_viejaventa=json_encode($raw_viejaventa);
write_viejaventa_content($contenido_viejaventa);

$total=round($total,2);
$rs_facturaventa=$qry_facturaventa->fetch_array(MYSQLI_ASSOC);

$link->query("UPDATE bh_facturaventa SET TX_facturaventa_fecha='$date', facturaventa_AI_cliente_id='$cliente_id', TX_facturaventa_total='$total', TX_facturaventa_observacion='$observation' WHERE AI_facturaventa_id = '$facturaventa_id'")or die($link->error);
$link->query("DELETE FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$facturaventa_id'")or die($link->error);

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_stock, TX_datoventa_medida, TX_datoventa_promocion) VALUES (?,?,?,?,?,?,?,?,?,?,?)")or die($link->error);
$raw_datoventa_inserted = array();
foreach ($raw_nuevaventa as $key => $value) {
	$qry_ins_datoventa->bind_param("iisddddssii",$rs_facturaventa['AI_facturaventa_id'],$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento'],$value['descripcion'],$value['stock'],$value['medida'],$value['promocion']);
	$qry_ins_datoventa->execute();

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();

	$raw_datoventa_inserted[$value['viejaventa_indice']] = trim($rs_lastid[0]);
}

$contenido_viejaventarel=read_viejaventa_rel();
$raw_viejaventarel=json_decode($contenido_viejaventarel, true);
$raw_datoventa_relacionado = array();
if (!empty($raw_viejaventarel[$_COOKIE['coo_iuser']])) {
	foreach ($raw_viejaventarel[$_COOKIE['coo_iuser']] as $key => $string_related) {
		$chain_related='';
		$raw_related = explode(",", $string_related);
		foreach ($raw_related as $key => $value) {
			$chain_related .= ($value === end($raw_related))  ? $raw_datoventa_inserted[$value] : $raw_datoventa_inserted[$value].",";
		}
		$raw_datoventa_relacionado[] = $chain_related;
	}
}

$rel_contenido = json_encode($raw_datoventa_relacionado);
$link->query("UPDATE bh_facturaventa SET TX_facturaventa_promocion = '$rel_contenido' WHERE AI_facturaventa_id = '{$rs_facturaventa['AI_facturaventa_id']}'")or die($link->error);
unset($raw_viejaventarel[$_COOKIE['coo_iuser']]);
$contenido_viejaventarel=json_encode($raw_viejaventarel);
write_viejaventa_rel($contenido_viejaventarel);



// foreach ($raw_viejaventa as $key => $value) {
// 	// $value['descripcion'] = $r_function->replace_regular_character($value['descripcion']);
// 	$qry_ins_datoventa->bind_param("iiiddddsi",$rs_facturaventa['AI_facturaventa_id'],$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento'],$value['descripcion'],$value['medida']);
// 	$qry_ins_datoventa->execute();
// }

// $bh_del="DELETE FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
// $link->query($bh_del) or die($link->error);

echo "acepted";
?>
