<?php
require '../../bh_conexion.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$approved = 1;
$cambio = 0;

$str_factid=$_GET['a'];
$arr_factid = explode(",",$str_factid);

$txt_clientid="SELECT facturaventa_AI_cliente_id FROM bh_facturaventa WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_clientid=$txt_clientid." AI_facturaventa_id = '$value'";
	}else{
		$txt_clientid=$txt_clientid." AI_facturaventa_id = '$value' OR";
	}
}
$qry_clientid=$link->query($txt_clientid)or die($link->error);
$row_clientid=$qry_clientid->fetch_array();
$client_id=$row_clientid['facturaventa_AI_cliente_id'];

$txt_facturaventa="SELECT
bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' ORDER BY AI_facturaventa_id ASC, AI_datoventa_id ASC ";
	}else {
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' OR";
	}
}
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$raw_facturaventa=array();
while ($rs_facturaventa=$qry_facturaventa->fetch_array()) {
	$raw_facturaventa[]=$rs_facturaventa;
}
$total_ff = 0;
foreach ($raw_facturaventa as $key => $value) {
	$descuento = (($value['TX_datoventa_descuento']*$value['TX_datoventa_precio'])/100);
	$precio_descuento = ($value['TX_datoventa_precio']-$descuento);
	$impuesto = (($value['TX_datoventa_impuesto']*$precio_descuento)/100);
	$precio_total = ($value['TX_datoventa_cantidad']*($precio_descuento+$impuesto));

	$total_ff += $precio_total;
}
$total_ff = round($total_ff,2);



$qry_payment = $link->query("SELECT AI_pago_id, pago_AI_user_id, pago_AI_metododepago_id, TX_pago_monto, TX_pago_numero, TX_pago_fecha FROM bh_pago WHERE pago_AI_user_id = '$uid'")or die($link->error);
$raw_payment=array();
$total_pagado=0;
while($rs_payment = $qry_payment->fetch_assoc()){
	$raw_payment[] = $rs_payment;
	$total_pagado+=$rs_payment['TX_pago_monto'];
}
$total_pagado = round($total_pagado,2);
foreach ($raw_payment as $key => $value) {
	if($value['pago_AI_metododepago_id'] === '1' || $value['pago_AI_metododepago_id'] === '2'){
		$cambio = 1;
	}
	if($value['pago_AI_metododepago_id'] === '0'){
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
	$approved = 0;
}

echo $approved;

?>
