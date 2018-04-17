<?php
require '../../bh_conexion.php';
$link = conexion();

$medida_id=$_GET['a'];

$qry_medida = $link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida WHERE AI_medida_id = '$medida_id'")or die($link->error);
$rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC);

echo $rs_medida['TX_medida_value'];
