<?php
require '../../bh_conexion.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$approved = 1;
$cambio = 0;

$str_factid=$_GET['a'];
$arr_factid = explode(",",$str_factid);

$txt_clientid="SELECT facturaf_AI_cliente_id FROM bh_facturaf WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value'";
	}else{
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value' OR";
	}
}
$qry_clientid=$link->query($txt_clientid)or die($link->error);
$row_clientid=$qry_clientid->fetch_array();
$client_id=$row_clientid['facturaf_AI_cliente_id'];

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento,
bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if($value === end($arr_factid)){
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' ORDER BY bh_facturaf.TX_facturaf_deficit ASC";
	}else{
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' OR";
	}
}
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$raw_facturaf=array();
while ($rs_facturaf=$qry_facturaf->fetch_array()) {
	$raw_facturaf[]=$rs_facturaf;
}
$total_ff = 0;
foreach ($raw_facturaf as $key => $value) {
	$total_ff += $value['TX_facturaf_deficit'];
}
$total_ff = round($total_ff,2);
$total_ff = floatval($total_ff);

$txt_payment="SELECT bh_nuevodebito.nuevodebito_AI_metododepago_id, bh_nuevodebito.AI_nuevodebito_id, bh_nuevodebito.TX_nuevodebito_fecha, bh_nuevodebito.TX_nuevodebito_monto, bh_nuevodebito.TX_nuevodebito_numero, bh_metododepago.TX_metododepago_value
FROM (bh_nuevodebito INNER JOIN bh_metododepago ON bh_nuevodebito.nuevodebito_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE nuevodebito_AI_user_id = '$uid'";
$qry_payment=$link->query($txt_payment)or die($link->error);
$raw_payment=array();
$total_pagado=0;
while($rs_payment = $qry_payment->fetch_assoc()){
	$raw_payment[] = $rs_payment;
	$total_pagado+=$rs_payment['TX_nuevodebito_monto'];
}
$total_pagado = round($total_pagado,2);
foreach ($raw_payment as $key => $value) {
	if($value['nuevodebito_AI_metododepago_id'] === '1' || $value['nuevodebito_AI_metododepago_id'] === '2'){
		$cambio = 1;
	}
	if($value['nuevodebito_AI_metododepago_id'] === '0'){
		$approved = 0;
	}
}
if ($total_pagado > $total_ff) {
	if ($cambio === 1) {
		$approved = 1;
	}else{
		$approved = 0;
	}
}elseif ($total_pagado === $total_ff) {
	$approved = 1;
}else{
	$approved = 1;
}
echo $approved;

?>
