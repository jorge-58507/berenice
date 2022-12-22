<?php
require '../../bh_conexion.php';
$link = conexion();

$qry_lastid = $link->query("SELECT AI_pedido_id as last_id FROM bh_pedido ORDER BY AI_pedido_id DESC limit 1")or die($link->error);
$rs_lastid = $qry_lastid->fetch_array();
$next_id = $rs_lastid['last_id']+1;

echo $next_id;

?>
