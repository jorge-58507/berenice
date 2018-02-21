<?php
require '../../bh_conexion.php';
$link=conexion();
$letra_id=$_GET['a'];
$product_id=$_GET['b'];

$link->query("UPDATE bh_producto SET producto_AI_letra_id = '$letra_id' WHERE AI_producto_id = '$product_id'")OR die($link->error);

echo $letra_id;
$link->close();
?>
