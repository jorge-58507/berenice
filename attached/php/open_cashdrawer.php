<?php
require '../../bh_conexion.php';
$link=conexion();
require 'req_login_paydesk.php';


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

if (strstr($ip, ', ')) {
  $ips = explode(', ', $ip);
  $ip = $ips[0];
}
return($ip);
}

$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);
$qry_impresora=$link->query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_retorno, TX_impresora_cliente, TX_impresora_serial, TX_impresora_cajaregistradora FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'")or die($link->error);
$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
// echo $rs_impresora['TX_impresora_cajaregistradora'];
// return false;
//  #### APERTURA DEL CAJON
if (!empty($rs_impresora['TX_impresora_cajaregistradora'])){
  $dir_cajaregistradora = $rs_impresora['TX_impresora_cajaregistradora'];
  $handle = printer_open($dir_cajaregistradora);
  printer_start_doc($handle, "");
  printer_start_page($handle);
  printer_set_option($handle, PRINTER_MODE, 'raw');
  printer_draw_text($handle, "Open Sesame", 400, 400);
  printer_end_page($handle);
  printer_end_doc($handle);
  printer_close($handle);
}

 ?>
