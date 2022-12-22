<?php
require '../../bh_conexion.php';
$link=conexion();

function check_code ($codigo) {
  $link=conexion();

  $qry_checkcode = $link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_codigo = '$codigo'")or die($link->error);
  $link->close();
  return $qry_checkcode->num_rows;
}
function cal_code ($lastcode) {
  global $prefix, $new_code;
  $raw_code = explode($prefix,$lastcode);
  $code_splited = '000000'.($raw_code[1]+1);

  $new_codesplited = substr($code_splited,-3);
  $new_code = $prefix.$new_codesplited;

  if (check_code ($new_code) > 0) {
    cal_code($new_code);
  }
}

$qry_prefix = $link->query("SELECT bh_familia.TX_familia_prefijo, bh_subfamilia.TX_subfamilia_prefijo FROM bh_subfamilia INNER JOIN bh_familia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id WHERE AI_subfamilia_id = '{$_GET['a']}'")or die($link->error);
$rs_prefix = $qry_prefix->fetch_array(MYSQLI_ASSOC);
$prefix = $rs_prefix['TX_familia_prefijo'].$rs_prefix['TX_subfamilia_prefijo'];

$qry_lastcode = $link->query("SELECT TX_producto_codigo FROM bh_producto WHERE TX_producto_codigo LIKE '$prefix%_%_%_%' ORDER BY TX_producto_codigo DESC LIMIT 1")or die($link->error);
$rs_lastcode = $qry_lastcode->fetch_array(MYSQLI_ASSOC);
$new_codesplited = substr('0000001',-3);
if ($qry_lastcode->num_rows > 0) {
  $lastcode = $rs_lastcode['TX_producto_codigo'];
  $raw_code = explode($prefix,$lastcode);
  $code_splited = '000000'.($raw_code[1]+1);
  $new_codesplited = substr($code_splited,-3);
}
$new_code = $prefix.$new_codesplited;

if (check_code ($new_code) > 0) {
  cal_code($new_code);
}

echo $new_code;

?>
