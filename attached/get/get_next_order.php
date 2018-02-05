<?php
require '../../bh_conexion.php';
$link = conexion();

$qry_lastid = $link->query("SELECT COUNT(AI_pedido_id) as last_id FROM bh_pedido")or die($link->error);
$rs_lastid = $qry_lastid->fetch_array();
$next_id = $rs_lastid['last_id']+1;

echo $next_id;

?>
