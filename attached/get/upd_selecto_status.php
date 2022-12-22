<?php
require '../../bh_conexion.php';
$link = conexion();

$selecto_id=$_GET['a'];
$status=$_GET['b'];

	$link->query("UPDATE bh_selecto SET TX_selecto_status = '$status' WHERE AI_selecto_id = '$selecto_id'")or die($link->error);

	echo "All Right";

?>
