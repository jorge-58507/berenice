<?php
require '../../bh_conexion.php';
$link = conexion();

$product_code = $_GET['a'];
$product_code = substr('0'.$product_code,-13);

$qry_producto= $link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_codigo = '$product_code'")or die($link->error);
$rs_producto=$qry_producto->fetch_array();

$raw_producto=['producto_id' => $rs_producto['AI_producto_id']];

echo json_encode($raw_producto);
