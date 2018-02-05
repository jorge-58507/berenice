<?php
require '../../bh_con.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$tipo=$_GET['a'];
$motivo=$_GET['b'];
$monto=$_GET['c'];
$fecha_actual = date('Y-m-d');

function ObtenerIP(){
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
$ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
$ip = $_SERVER['REMOTE_ADDR'];
else
$ip = "IP desconocida";
return($ip);
}

$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$rs_impresoraid=mysql_fetch_assoc(mysql_query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'"));

		mysql_query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)
	VALUES ('$uid', '{$rs_impresoraid['AI_impresora_id']}', '$tipo', '$motivo', '$monto', '$fecha_actual', 'ACTIVA')");
		$qry_lastid=mysql_query("SELECT LAST_INSERT_ID();");
		$rs_lastid = mysql_fetch_row($qry_lastid);
		$last_id = trim($rs_lastid[0]);
session_start();
		$_SESSION['efectivo_id']=$last_id;

