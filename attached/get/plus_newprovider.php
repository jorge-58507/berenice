<?php
require '../../bh_conexion.php';
$link = conexion();

$providername=$_GET['a'];
$cif=$_GET['b'];
$direction=$_GET['c'];
$telephone=$_GET['d'];
$type=$_GET['e'];

	$link->query("INSERT INTO bh_proveedor (TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_direccion, TX_proveedor_telefono, TX_proveedor_tipo) VALUES ('$providername', '$cif', '$direction', '$telephone', '$type')")or die($link->error);

?>
