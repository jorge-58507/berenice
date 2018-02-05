<?php
require '../../bh_con.php';
$link = conexion();

$product_id=$_GET['a'];

		$qry_checkproduct=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'", $link);
		$nr_checkproduct=mysql_num_rows($qry_checkproduct);
		if($nr_checkproduct >= 1){
			$rs_checkproduct=mysql_fetch_assoc($qry_checkproduct);
			$id=$rs_checkproduct['AI_producto_id'];
			$bh_del="DELETE FROM bh_producto WHERE AI_producto_id = '$id'";
			mysql_query($bh_del, $link) or die(mysql_error());			
		}



