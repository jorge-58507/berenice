<?php
require '../../bh_conexion.php';
$link = conexion();

$facturacompra_id=$_GET['a'];
$user_id=$_COOKIE['coo_iuser'];

$link->query("DELETE FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '$user_id'")or die($link->error);

$prep_ins_nuevacompra=$link->prepare("INSERT INTO bh_nuevacompra (nuevacompra_AI_user_id, nuevacompra_AI_producto_id, TX_nuevacompra_unidades, TX_nuevacompra_precio, TX_nuevacompra_itbm, TX_nuevacompra_descuento, TX_nuevacompra_p4) VALUES (?, ?, ?, ?, ?, ?, ?)") or die($link->error);

$qry_datocompra = $link->query("SELECT datocompra_AI_producto_id, TX_datocompra_medida, TX_datocompra_cantidad,
	TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento, TX_datocompra_p4 FROM bh_datocompra
	WHERE datocompra_AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
while ($rs_datocompra=$qry_datocompra->fetch_array(MYSQLI_ASSOC)) {
	$prep_ins_nuevacompra->bind_param("sssssss", $user_id, $producto_id, $cantidad, $precio, $impuesto, $descuento, $p4);
	$user_id=$_COOKIE['coo_iuser'];
	$producto_id=$rs_datocompra['datocompra_AI_producto_id'];	$cantidad=$rs_datocompra['TX_datocompra_cantidad'];
	$precio=$rs_datocompra['TX_datocompra_precio'];						$impuesto=$rs_datocompra['TX_datocompra_impuesto'];
	$descuento=$rs_datocompra['TX_datocompra_descuento'];			$p4=$rs_datocompra['TX_datocompra_p4'];
	$prep_ins_nuevacompra->execute();
}

$qry_facturacompra=$link->query("SELECT AI_facturacompra_id, TX_facturacompra_ordendecompra, TX_facturacompra_numero, TX_facturacompra_almacen, facturacompra_AI_proveedor_id, TX_facturacompra_observacion FROM bh_facturacompra WHERE AI_facturacompra_id =	'$facturacompra_id'")or die($link->error);
$rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC);
$raw_facturacompra['resultado']='acepted';
$raw_facturacompra['numero']=$rs_facturacompra['TX_facturacompra_numero'];
$raw_facturacompra['orden']=$rs_facturacompra['TX_facturacompra_ordendecompra'];
$raw_facturacompra['almacen']=$rs_facturacompra['TX_facturacompra_almacen'];
$raw_facturacompra['proveedor']=$rs_facturacompra['facturacompra_AI_proveedor_id'];
$raw_facturacompra['observacion']=$rs_facturacompra['TX_facturacompra_observacion'];

echo json_encode($raw_facturacompra);
?>
