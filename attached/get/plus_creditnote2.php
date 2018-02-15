<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';
// ############################## FUNCIONES #####################
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

function insert_notadecredito($cliente_id,$facturaf_id,$user_id,$numero_nc,$motivo,$fecha,$hora,$destino,$retencion){
	$link = conexion();
	$host_ip=ObtenerIP();
	$host_name=gethostbyaddr($host_ip);
	$qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
	$rs_impresora = $qry_impresora->fetch_array();

	$bh_insert_nc="INSERT INTO bh_notadecredito (notadecredito_AI_cliente_id, notadecredito_AI_facturaf_id, notadecredito_AI_user_id, notadecredito_AI_impresora_id, TX_notadecredito_tipo, TX_notadecredito_numero, TX_notadecredito_motivo, TX_notadecredito_fecha, TX_notadecredito_hora, TX_notadecredito_destino, TX_notadecredito_status, TX_notadecredito_retencion) VALUES ('$cliente_id', '$facturaf_id', '$user_id', '{$rs_impresora['AI_impresora_id']}', '1', '$numero_nc', '$motivo', '$fecha', '$hora', '$destino', 'ACTIVA', $retencion)";
	$link->query($bh_insert_nc) or die ($link->error);

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();
	return $last_id = trim($rs_lastid[0]);
	$link->close();
}

function insert_devolucion($notacredito_id,$producto_id,$datoventa_id,$user_id,$cantidad){
	$link = conexion();
	$bh_insert_devolution="INSERT INTO bh_datodevolucion (datodevolucion_AI_notadecredito_id,	datodevolucion_AI_producto_id, datodevolucion_AI_datoventa_id, datodevolucion_AI_user_id, TX_datodevolucion_cantidad ) VALUES ('$notacredito_id','$producto_id','$datoventa_id','$user_id','$cantidad')";
	$link->query($bh_insert_devolution)or die($link->error);

	$qry_product=$link->query("SELECT TX_producto_cantidad  FROM bh_producto WHERE AI_producto_id = '$producto_id'")or die($link->error);
	$row_product=$qry_product->fetch_array();
	$product_quantity=$row_product[0];
	$suma=$product_quantity+$cantidad;
	$link->query("UPDATE bh_producto SET TX_producto_cantidad = '$suma' WHERE AI_producto_id = '$producto_id'");
	$link->close();
}

function checknumeronc($numero_nc){
	$link=conexion();
	$qry=$link->query("SELECT AI_notadecredito_id FROM bh_notadecredito WHERE TX_notadecredito_numero = '$numero_nc'");
	$nr=$qry->num_rows;
	$link->close();
	if($nr > 0){
		return sumarnumeronc($numero_nc);
	}else{
		return $numero_nc;
	}
}
function sumarnumeronc($numero_nc){
	$pre_numero_nc = "00000000".($numero_nc +1);
		$numero_nc = substr($pre_numero_nc,-8);
		return checknumeronc($numero_nc);
}

// ############################## FUNCIONES #########################
// ################################## INSERCION NOTA DE CREDITO  #######################################
$qry_facturaf=$link->query("SELECT bh_facturaf.facturaf_AI_cliente_id, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_deficit
 FROM (((bh_nuevadevolucion
  INNER JOIN bh_datoventa ON bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
   INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id)
   	INNER JOIN bh_facturaf ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
	 WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id' ")or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();
$cliente_id=$rs_facturaf['facturaf_AI_cliente_id'];
$facturaf_id=$rs_facturaf['AI_facturaf_id'];
$numero_nc = checknumeronc('00000001');
$motivo = $_GET['a'];
$fecha = date('Y-m-d');
$hora = date('h:i a');
$destino = $_GET['b'];
$retencion = (100-$_GET['c'])/100;

$qry_checkcreditnote=$link->query("SELECT AI_notadecredito_id FROM bh_notadecredito WHERE TX_notadecredito_numero = '$numero_nc'")or die($link->error);
$nr_checkcreditnote=$qry_checkcreditnote->num_rows;
if($nr_checkcreditnote < 1){
	$creditnote_id = insert_notadecredito($cliente_id,$facturaf_id,$user_id,$numero_nc,$motivo,$fecha,$hora,$destino,$_GET['c']);
}
// ################################## INSERCIONES  #######################################
// ########################## CALCULAR MONTO E IMPUESTO ############################

$txt_nuevadevolucion="SELECT bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.nuevadevolucion_AI_producto_id, bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id,
bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_impuesto
FROM (bh_nuevadevolucion
       INNER JOIN bh_datoventa ON bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
	WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id'";
$qry_nuevadevolucion=$link->query($txt_nuevadevolucion)or die($link->error);
$rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array();

$precio=0;
$impuesto=0;
$descuento=0;

do{
insert_devolucion($creditnote_id,$rs_nuevadevolucion['nuevadevolucion_AI_producto_id'],$rs_nuevadevolucion['nuevadevolucion_AI_datoventa_id'],$user_id,$rs_nuevadevolucion['TX_nuevadevolucion_cantidad']);

	$precio_uni=$rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*$rs_nuevadevolucion['TX_datoventa_precio'];
	$descuento_uni=($rs_nuevadevolucion['TX_datoventa_precio']*$rs_nuevadevolucion['TX_datoventa_descuento'])/100;
	$precio_descuento_uni=$precio_uni-$descuento_uni;
	$impuesto_uni=($precio_descuento_uni*$rs_nuevadevolucion['TX_datoventa_impuesto'])/100;
	$precio_descuento_impuesto_uni=$precio_descuento_uni+$impuesto_uni;
	$retencion_uni=$precio_descuento_impuesto_uni*$retencion;
	$precio_descuento_impuesto_retencion_uni=$precio_descuento_impuesto_uni-$retencion_uni;

	$precio += $precio_uni;
	$impuesto += $impuesto_uni*$retencion;
	$descuento += $descuento_uni;
}while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array());

$monto_nc = round(($precio*$retencion)-$descuento,2);
$exedente = round($monto_nc+$impuesto,2);
// ########################## CALCULAR MONTO E IMPUESTO ############################
if($destino == 'EFECTIVO'){
	$exedente='0';
}
$link->query("UPDATE bh_notadecredito SET TX_notadecredito_monto = '$monto_nc', TX_notadecredito_impuesto = '$impuesto', TX_notadecredito_exedente = '0' WHERE AI_notadecredito_id = '$creditnote_id'");

$qry_cliente = $link->query("SELECT bh_cliente.TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$cliente_id'")or die($link->error);
$rs_cliente = $qry_cliente->fetch_array();
$new_saldo = $rs_cliente['TX_cliente_saldo'] + $exedente;

if ($rs_facturaf['TX_facturaf_deficit'] > 0) {
	//actualizo aqui
	$new_deficit = $rs_facturaf['TX_facturaf_deficit']-$new_saldo;
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit =	''")
	$new_saldo = $new_saldo-$rs_facturaf['TX_facturaf_deficit'];
}
if ($new_saldo <= 0) {
	$new_saldo=0;
}
$link->query("UPDATE bh_cliente SET TX_cliente_saldo = '$new_saldo' WHERE AI_cliente_id = '$cliente_id'")or die($link->error);

$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$rs_impresora = $qry_impresora->fetch_array();

if($destino == "EFECTIVO"){
	$motivo_efectivo="NOTA DE CREDITO ".$numero_nc;
	$monto_efectivo=round((($precio*$retencion)-$descuento)+$impuesto,2);
		$link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)
		VALUES ('$user_id', '{$rs_impresora['AI_impresora_id']}', 'SALIDA', '$motivo_efectivo', '$monto_efectivo', '$fecha', 'ACTIVA')");
}


$_SESSION['creditnote_id'] = $creditnote_id;
