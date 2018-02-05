<?php
require '../../bh_con.php';
$link = conexion();

$id=$_GET['a'];
$value=$_GET['b'];

$link = conexion();
	$qry_chkproduct=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$id'");
	$rs_chkproduct=mysql_fetch_assoc($qry_chkproduct);
	$cantidad=$rs_chkproduct['TX_producto_cantidad'];
	if($cantidad >= $value){
		$resto=$cantidad-$value;
		$bh_update="UPDATE bh_producto SET TX_producto_cantidad='$resto' WHERE AI_producto_id = '$id'";
		mysql_query($bh_update, $link) or die (mysql_error());
	}



?>
   
    