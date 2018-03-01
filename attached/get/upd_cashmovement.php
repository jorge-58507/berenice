<?php
require '../../bh_conexion.php';
$link = conexion();

$motivo = $_GET['a'];
$type = $_GET['b'];
$efectivo_id = $_GET['c'];

$link->query("UPDATE bh_efectivo SET TX_efectivo_motivo = '$motivo', TX_efectivo_tipo =	'$type' WHERE AI_efectivo_id = '$efectivo_id'")or die($link->error);

echo "All Right";

?>
