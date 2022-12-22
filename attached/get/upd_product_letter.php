<?php
require '../../bh_conexion.php';
$link=conexion();
$letra_id=$_GET['a'];
$product_id=$_GET['b'];
$medida=$_GET['c'];

$link->query("UPDATE rel_producto_medida SET productomedida_AI_letra_id = '$letra_id' WHERE productomedida_AI_producto_id = '$product_id' AND productomedida_AI_medida_id = '$medida'")or die($link->error);

echo $letra_id;
$link->close();
?>
