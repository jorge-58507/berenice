<?php
require '../../bh_conexion.php';
$link = conexion();

$efectivo_id=$_GET['a'];

$link->query("UPDATE bh_efectivo SET efectivo_AI_arqueo_id = '-1' WHERE AI_efectivo_id = '$efectivo_id'")or die($link->error);

// ############################# ANSWER ####################
echo "acepted";
?>
