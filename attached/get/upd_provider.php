<?php
require '../../bh_conexion.php';
$link = conexion();

$proveedor_id=$_GET['g'];

$providername=$_GET['a'];
$cif=$_GET['b'];
$direction=$_GET['c'];
$telephone=$_GET['d'];
$type=$_GET['e'];
$dv=$_GET['f'];

	$link->query("UPDATE bh_proveedor SET TX_proveedor_nombre='$providername', TX_proveedor_cif='$cif', TX_proveedor_direccion='$direction', TX_proveedor_telefono = '$telephone', TX_proveedor_tipo='$type', TX_proveedor_dv='$dv' WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);

	$raw_proveedor=['id' => $proveedor_id,'nombre'=> $providername];
 	echo json_encode($raw_proveedor);


?>
