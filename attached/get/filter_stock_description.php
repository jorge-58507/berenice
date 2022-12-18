<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$r_function->replace_regular_character($_GET['term']);

$prep_precio=$link->prepare("SELECT AI_precio_id, TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' AND precio_AI_medida_id = ? ORDER BY TX_precio_fecha DESC LIMIT 1")or die($link->error);
$prep_checkfacturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

$arr_value = (explode(' ',$value));
$arr_value = array_values(array_unique($arr_value));
$txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_codigo, TX_producto_referencia, TX_producto_activo, TX_producto_minimo, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_medida, TX_producto_inventariado FROM bh_producto WHERE ";
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_value LIKE '%{$value}%' OR " : "TX_producto_value LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_codigo LIKE '%{$value}%' OR " : "TX_producto_codigo LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_referencia LIKE '%{$value}%'" : "TX_producto_referencia LIKE '%{$value}%' AND ";
}
$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 10");

$raw_product=array();
$i=0;

while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
	$raw_product[$i]['value'] = $r_function->replace_special_character($rs_product['TX_producto_value']);
	$i++;
}

echo json_encode($raw_product);
