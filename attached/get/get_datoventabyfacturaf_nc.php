<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$facturaf_id=$_GET['a'];
$txt_datoventa="SELECT bh_datoventa.AI_datoventa_id, bh_datoventa.datoventa_AI_producto_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_medida
FROM ((bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_datoventa = $link->query($txt_datoventa)or die($link->error);
while($rs_datoventa=$qry_datoventa->fetch_array()) {
   $link->query("INSERT INTO bh_nuevadevolucion
		(nuevadevolucion_AI_producto_id, nuevadevolucion_AI_datoventa_id, nuevadevolucion_AI_user_id, TX_nuevadevolucion_cantidad,TX_nuevadevolucion_medida)
	VALUES ('{$rs_datoventa['datoventa_AI_producto_id']}','{$rs_datoventa['AI_datoventa_id']}','$user_id','{$rs_datoventa['TX_datoventa_cantidad']}','{$rs_datoventa['TX_datoventa_medida']}')");
}
echo "string";
?>
