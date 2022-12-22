<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

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
$qry_impresoraid = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$rs_impresoraid = $qry_impresoraid->fetch_array();

		$link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)
	VALUES ('$uid', '{$rs_impresoraid['AI_impresora_id']}', '$tipo', '$motivo', '$monto', '$fecha_actual', 'ACTIVA')")or die($link->error);
		$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
		$rs_lastid = $qry_lastid->fetch_array();
		$last_id = trim($rs_lastid[0]);
session_start();
		$_SESSION['efectivo_id']=$last_id;

		$raw_user = $r_function->read_user();
		$qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE TX_user_type = '2'")or die($link->error);
		while($rs_user=$qry_user->fetch_array(MYSQLI_ASSOC)) {
			$content =		$raw_user[$_COOKIE['coo_iuser']].' realiz&oacute; una '.$tipo.' por B/'.number_format($monto,2);
			$r_function->method_message('create', $_COOKIE['coo_iuser'], $rs_user['AI_user_id'], 'Mov. Caja Menuda', $content, 'notification', date('H:i:s'), date('d-m-Y'));
		}
