<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$fecha_actual = date('Y-m-d');

$saldo=round($_GET['a'],2);
$client_id=$_GET['b'];

$user_id = $_COOKIE['coo_iuser'];

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
$rs_impresoraid=$qry_impresoraid -> fetch_array();

$qry_cliente = $link->query("SELECT AI_cliente_id, TX_cliente_saldo, TX_cliente_nombre FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_cliente = $qry_cliente->fetch_array();
$cliente_saldo = round($rs_cliente['TX_cliente_saldo'],2);

  $suma = $cliente_saldo + $saldo;
  $suma = round($suma,2);
  $motivo =  'CONVERSION DE PREABONO '.$rs_cliente['TX_cliente_nombre'];
  $link->query("UPDATE bh_cliente SET TX_cliente_saldo = '$suma' WHERE AI_cliente_id = '$client_id'")or die($link->error);
  $link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)
  VALUES ('$user_id', '{$rs_impresoraid['AI_impresora_id']}', 'ENTRADA', '$motivo', '$saldo', '$fecha_actual', 'ACTIVA')")or die($link->error);

//################ RESPUESTA

$qry_cliente = $link->query("SELECT AI_cliente_id, TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_cliente = $qry_cliente->fetch_array();
$cliente_saldo = round($rs_cliente['TX_cliente_saldo'],2);
echo number_format($cliente_saldo,2);

?>
