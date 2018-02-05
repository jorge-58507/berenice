<?php
require '../../bh_con.php';
$link = conexion();

$qry_facturaventa=mysql_query("SELECT COUNT(AI_facturaventa_id) as last_id FROM bh_facturaventa")or die(mysql_error());
$rs_facturaventa=mysql_fetch_assoc($qry_facturaventa);
$next_id = $rs_facturaventa['last_id']+1;

echo $next_id;