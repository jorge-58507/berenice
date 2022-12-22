<?php
require '../../bh_conexion.php';
$link=conexion();
$total_discount=$_GET['a'];
$product_id=$_GET['b'];

$link->query("UPDATE bh_producto SET TX_producto_descuento = '$total_discount' WHERE AI_producto_id = '$product_id'")OR die($link->error);

echo $total_discount;
$link->close();
?>
