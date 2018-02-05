<?php
require '../../bh_con.php';
$link = conexion();


	$qry_nuevacompra=mysql_query("SELECT * FROM bh_nuevacompra", $link);
	$nr_nuevacompra=mysql_num_rows($qry_nuevacompra);
	if($nr_nuevacompra >= 1){
		$rs_nuevacompra=mysql_fetch_assoc($qry_nuevacompra);
		do{
			$product_id=$rs_nuevacompra['nuevacompra_AI_producto_id'];
			
$qry_product=mysql_query("SELECT TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$product_id'", $link);
		$rs_product=mysql_fetch_assoc($qry_product);

			$existencia = $rs_product['TX_producto_cantidad'];
			$cantidad = $rs_nuevacompra['TX_nuevacompra_unidades'];
			$resta=$existencia-$cantidad;
						
			mysql_query("UPDATE bh_producto SET TX_producto_cantidad='$resta' WHERE AI_producto_id = '$product_id'");
		}while($rs_nuevacompra=mysql_fetch_assoc($qry_nuevacompra));
	}
	$rs_nuevacompra=mysql_fetch_assoc($qry_nuevacompra);

	mysql_query("TRUNCATE bh_nuevacompra");
?>
