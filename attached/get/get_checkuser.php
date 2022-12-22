<?php
require '../../bh_conexion.php';
$link=conexion();
$name = $_GET['a'];
$password = $_GET['c'];

$qry_user = $link->query("SELECT AI_user_id FROM bh_user WHERE TX_user_seudonimo = '$name' OR TX_user_password = '$password'")or die($link->error);
echo $nr_user = $qry_user->num_rows;

?>
