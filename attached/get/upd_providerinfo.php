<?php
require '../../bh_conexion.php';
$link = conexion();

$nombre=$_GET['a'];
$cif=$_GET['b'];
$dv=$_GET['c'];
$telefono=$_GET['d'];
$direccion=$_GET['e'];

$proveedor_id = $_GET['f'];

$link->query("UPDATE bh_proveedor SET TX_proveedor_nombre = '$nombre', TX_proveedor_cif = '$cif', TX_proveedor_dv = '$dv', TX_proveedor_telefono = '$telefono', TX_proveedor_direccion = '$direccion' WHERE AI_proveedor_id = '$proveedor_id'");

echo $link->affected_rows;
