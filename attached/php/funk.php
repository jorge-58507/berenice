<?php
function ins_factura_short($cliente, $uid, $fecha_actual, $hora_actual, $numero_ff){
	mysql_query("INSERT INTO bh_facturaf (facturaf_AI_cliente_id, facturaf_AI_user_id, TX_facturaf_fecha, TX_facturaf_hora, TX_facturaf_numero) VALUES ('$cliente', '{$_COOKIE['coo_iuser']}', '$fecha_actual', '$hora_actual', '$numero_ff');
	");
	$qry_lastid=mysql_query("SELECT LAST_INSERT_ID();");
	$rs_lastid = mysql_fetch_row($qry_lastid);
	return $last_id = trim($rs_lastid[0]);
}
?>