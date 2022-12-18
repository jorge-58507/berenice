<?php
require '../../bh_conexion.php';
$link = conexion();

$message='';

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
		$approved_payment = $approved = 1;
	}else{
		$approved = 0;
	}
}elseif ($total_pagado === $total_ff) {
	$approved_payment = $approved = 1;
}else{
	$approved = 0;
}


// ############################# 								VERIFICAR SI HAY ACCESO A LA RED								########################


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
// $host_name='noexiste';
$qry_impresora = $link->query("SELECT AI_impresora_id, TX_impresora_retorno, TX_impresora_recipiente FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$nr_impresora = $qry_impresora->num_rows;
if ($nr_impresora < 1) {
	echo "denied, sin impresora ".$host_name;
	return false;
}

$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
$recipiente = $rs_impresora['TX_impresora_recipiente'];
// $recipiente = "//noexiste/P_CAJA/";
// $recipiente = "//TPV3/docs trillis/";

// ############################# 								VERIFICAR SI HAY ACCESO A LA RED								########################
$retorno = $rs_impresora['TX_impresora_retorno'];
if (!file_exists($recipiente)) {
	$message = "Verificar Conexion de Red";
	$approved = 0;

    // if(!mkdir($recipiente, 0777, true)){
			// $message = "Verificar Conexion de Red";
			// $approved = 0;
		// };
}



$raw_answer['answer'] = $approved;
$raw_answer['message'] = $message;
$raw_answer['payment'] = $approved_payment;
echo json_encode($raw_answer);

?>
