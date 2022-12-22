<?php
require '../../bh_conexion.php';
$link = conexion();

$user_id = $_GET['a'];
$cliente = $_GET['b'];

$link->query("UPDATE bh_user SET TX_user_online= 1, TX_user_cliente = '$cliente' WHERE AI_user_id = '$user_id' ")or die($link->error);

?>
