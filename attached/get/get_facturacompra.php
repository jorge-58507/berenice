<?php
require '../../bh_conexion.php';
$link = conexion();

$facturacompra_id=$_GET['a'];
$user_id=$_COOKIE['coo_iuser'];

$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);
$prep_producto = $link->prepare("SELECT AI_producto_id, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);
$prep_updcantidad = $link->prepare("UPDATE bh_producto SET TX_producto_cantidad = ? WHERE AI_producto_id = ?")or die($link->error);

$qry_datocompra = $link->query("SELECT datocompra_AI_producto_id, TX_datocompra_medida, TX_datocompra_cantidad,	TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento, TX_datocompra_p4
	FROM bh_datocompra
	WHERE datocompra_AI_facturacompra_id = '$facturacompra_id'")or die($link->error);

while ($rs_datocompra=$qry_datocompra->fetch_array(MYSQLI_ASSOC)) {
	$prep_producto->bind_param("i", $rs_datocompra['datocompra_AI_producto_id']); $prep_producto->execute(); $qry_producto = $prep_producto->get_result();
	$rs_producto=$qry_producto->fetch_array();
	$prep_producto_medida->bind_param("ii", $rs_datocompra['datocompra_AI_producto_id'], $rs_datocompra['TX_datocompra_medida']); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
	$rs_producto_medida = $qry_producto_medida->fetch_array();

	$nueva_cantidad = $rs_producto['TX_producto_cantidad'] - ($rs_datocompra['TX_datocompra_cantidad']*$rs_producto_medida['TX_rel_productomedida_cantidad']);
	$prep_updcantidad->bind_param("di",$nueva_cantidad,$rs_datocompra['datocompra_AI_producto_id']); $prep_updcantidad->execute();
}
$link->query("UPDATE bh_facturacompra SET TX_facturacompra_preguardado = 1 WHERE AI_facturacompra_id = '$facturacompra_id'")or die($link->error);

echo "all right";
?>
