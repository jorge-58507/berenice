<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['term'];

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value FROM bh_producto WHERE TX_producto_codigo LIKE '%$value%' LIMIT 10")or die($link->error);
$raw_producto=array(); $i=0;
while($rs_product=$qry_product->fetch_assoc()){
	$raw_producto[$i]['value'] = $rs_product['TX_producto_codigo']." | ".substr($rs_product['TX_producto_value'],0,40);
	$i++;
}

echo json_encode($raw_producto);

?>
