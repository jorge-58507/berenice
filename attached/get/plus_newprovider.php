<?php
require '../../bh_conexion.php';
$link = conexion();

$providername=$_GET['a'];
$cif=$_GET['b'];
$direction=$_GET['c'];
$telephone=$_GET['d'];
$type=$_GET['e'];
$dv=$_GET['f'];

	$link->query("INSERT INTO bh_proveedor (TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_direccion, TX_proveedor_telefono, TX_proveedor_tipo, TX_proveedor_dv) VALUES ('$providername', '$cif', '$direction', '$telephone', '$type', '$dv')")or die($link->error);
	$qry_lastid = $link->query("SELECT LAST_INSERT_ID();")or die($link->error);
	$rs_lastid = $qry_lastid->fetch_array();

$raw_proveedor=['id' => $rs_lastid[0],'nombre'=> $providername];
echo json_encode($raw_proveedor);
?>
