<?php
require '../../bh_conexion.php';
$link = conexion();

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

$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

$facturaf_id = $_GET['b'];
$facturaf_id -= 1;

$qry_facturaf = $link->query("SELECT TX_facturaf_numero FROM bh_facturaf WHERE AI_facturaf_id = '$facturaf_id'")or die($link->error);
$rs_facturaf = $qry_facturaf->fetch_array();
$ff_numero = $rs_facturaf['TX_facturaf_numero'];

$qry_impresora=$link->query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_retorno, TX_impresora_cliente, TX_impresora_serial FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'");
$row_impresora=$qry_impresora->fetch_array();

$retorno = $row_impresora['TX_impresora_retorno'];

$file = fopen($retorno."FACTI".substr($ff_numero,-7).".TXT", "r");
$content = fgets($file);
fclose($file);
$raw_content = explode("\t",$content);
$ticket = substr($raw_content[7]);
echo $content;
$link->query("UPDATE bh_facturaf SET TX_facturaf_serial = '$raw_content[6]', TX_facturaf_ticket = '$ticket' WHERE AI_facturaf_id = '$facturaf_id'")or die($link->error);
