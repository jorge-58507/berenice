<?php
require '../../bh_conexion.php';
$link = conexion();

$name = $_GET['a'];
$type = $_GET['b'];
$password = $_GET['c'];
$user_id = $_GET['d'];

$link->query("UPDATE bh_user SET TX_user_seudonimo = '$name', TX_user_type =	'$type', TX_user_password = '$password' WHERE AI_user_id = '$user_id'")or die($link->error);

echo "All Right";

?>
