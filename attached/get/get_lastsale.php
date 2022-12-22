<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_sale.php';

$fecha_actual = date('Y-m-d');
// $fecha_actual = '2018-07-20';

$prep_payment = $link->prepare("SELECT AI_datopago_id, datopago_AI_metododepago_id, TX_datopago_monto FROM bh_datopago WHERE datopago_AI_facturaf_id = ?")or die($link->error);

$qry_lastsale = $link->query("SELECT bh_facturaventa.TX_facturaventa_numero, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_total, bh_cliente.TX_cliente_nombre FROM ((bh_facturaf INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id) INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id) WHERE facturaventa_AI_user_id = '{$_GET['a']}' AND bh_facturaf.TX_facturaf_fecha = '$fecha_actual' ORDER BY AI_facturaf_id DESC")or die($link->error);
$raw_lastsale=["last_sale" => [],"datopago" => 0];
$raw_payment = ["1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"7"=>0,"8"=>0];
while ($rs_lastsale = $qry_lastsale->fetch_array()) {
  $raw_lastsale['last_sale'][] = $rs_lastsale;
  $prep_payment->bind_param("i",$rs_lastsale['AI_facturaf_id']); $prep_payment->execute(); $qry_payment = $prep_payment->get_result();
  while ($rs_payment = $qry_payment->fetch_array(MYSQLI_ASSOC)) {
    $raw_payment[$rs_payment['datopago_AI_metododepago_id']] += $rs_payment['TX_datopago_monto'];
  }
}
$sumatoria = $raw_payment[1]+$raw_payment[2]+$raw_payment[3]+$raw_payment[4]+$raw_payment[7]+$raw_payment[8];
$qry_user = $link->query("SELECT TX_user_meta FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$rs_user = $qry_user->fetch_array();
$porcentaje = ($sumatoria*100)/$rs_user['TX_user_meta'];

$raw_lastsale['datopago'] = $porcentaje;
echo json_encode($raw_lastsale);

?>
