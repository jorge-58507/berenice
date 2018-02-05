<?php
require '../../bh_con.php';
$link = conexion();

$vendor_id=$_GET['a'];
$facturaventa_id=$_GET['b'];
	
	mysql_query("UPDATE bh_facturaventa SET facturaventa_AI_user_id = '$vendor_id' WHERE AI_facturaventa_id = '$facturaventa_id'");
	
?>

