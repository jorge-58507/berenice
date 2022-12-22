<?php
require '../../bh_con.php';
$link = conexion();

$facturaventa_id=$_GET['a'];

$qry_statusbill=mysql_query("SELECT TX_facturaventa_status FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'");
$row_statusbill=mysql_fetch_row($qry_statusbill);

if($row_statusbill[0] == 'ACTIVA'){
	mysql_query("UPDATE bh_facturaventa SET TX_facturaventa_status = 'INACTIVA' WHERE AI_facturaventa_id = '$facturaventa_id'");
}else if($row_statusbill[0] == 'INACTIVA'){
	mysql_query("UPDATE bh_facturaventa SET TX_facturaventa_status = 'ACTIVA' WHERE AI_facturaventa_id = '$facturaventa_id'");
}else if($row_statusbill[0] == 'FACTURADA'){
	mysql_query("UPDATE bh_facturaventa SET TX_facturaventa_status = 'ACTIVA' WHERE AI_facturaventa_id = '$facturaventa_id'");
}

?>