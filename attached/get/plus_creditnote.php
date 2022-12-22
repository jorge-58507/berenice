<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';
$anulated = $_GET['d'];
// ############################## FUNCIONES #####################

function insert_notadecredito($cliente_id,$facturaf_id,$user_id,$numero_nc,$motivo,$fecha,$hora,$destino,$retencion,$anulated){
	$link = conexion();
	$impresora_id = get_printer();
	$bh_insert_nc="INSERT INTO bh_notadecredito (notadecredito_AI_cliente_id, notadecredito_AI_facturaf_id, notadecredito_AI_user_id, notadecredito_AI_impresora_id, TX_notadecredito_tipo, TX_notadecredito_numero, TX_notadecredito_motivo, TX_notadecredito_fecha, TX_notadecredito_hora, TX_notadecredito_destino, TX_notadecredito_status, TX_notadecredito_retencion, TX_notadecredito_anulado) VALUES ('$cliente_id', '$facturaf_id', '$user_id', '$impresora_id', '1', '$numero_nc', '$motivo', '$fecha', '$hora', '$destino', 'ACTIVA', $retencion, '$anulated')";
	$link->query($bh_insert_nc) or die ($link->error);

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();
	return $last_id = trim($rs_lastid[0]);
	$link->close();
}

function insert_devolucion($notacredito_id,$producto_id,$datoventa_id,$user_id,$cantidad,$medida_id){
	$link = conexion();
	$bh_insert_devolution="INSERT INTO bh_datodevolucion (datodevolucion_AI_notadecredito_id,	datodevolucion_AI_producto_id, datodevolucion_AI_datoventa_id, datodevolucion_AI_user_id, TX_datodevolucion_cantidad, TX_datodevolucion_medida ) VALUES ('$notacredito_id','$producto_id','$datoventa_id','$user_id','$cantidad','$medida_id')";
	$link->query($bh_insert_devolution)or die($link->error);

	$qry_product=$link->query("SELECT TX_producto_cantidad, TX_producto_medida, TX_producto_descontable  FROM bh_producto WHERE AI_producto_id = '$producto_id'")or die($link->error);
	$row_product=$qry_product->fetch_array();
	$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);
	$prep_producto_medida->bind_param("ii", $producto_id, $medida_id); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
	$rs_producto_medida = $qry_producto_medida->fetch_array();
  // ACTUALIZAR CANTIDAD "INVENTARIO"
	$product_quantity=$row_product[0];
	$suma=$product_quantity+($cantidad*$rs_producto_medida['TX_rel_productomedida_cantidad']);
	if($row_product['TX_producto_descontable'] === '1'){
		$link->query("UPDATE bh_producto SET TX_producto_cantidad = '$suma' WHERE AI_producto_id = '$producto_id'");
	}
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
function get_printer(){
	$link = conexion();

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

	$host_ip=$ip;
	$host_name=gethostbyaddr($host_ip);
	$qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'") or die ($link->error);
	$rs_impresora=$qry_impresora->fetch_array();
	return $rs_impresora['AI_impresora_id'];
}
function insert_notadebito($cliente_id,$user_id,$numero_nd,$motivo,$fecha,$hora,$total_nd){
	$link = conexion();
	$impresora_id = get_printer();
	$bh_insert_nd="INSERT INTO bh_notadebito (notadebito_AI_cliente_id, notadebito_AI_user_id, notadebito_AI_impresora_id, TX_notadebito_numero, TX_notadebito_motivo, TX_notadebito_fecha, TX_notadebito_hora, TX_notadebito_total) VALUES ('$cliente_id', '$user_id', '$impresora_id', '$numero_nd', '$motivo', '$fecha', '$hora', '$total_nd')";
	$link->query($bh_insert_nd) or die ($link->error);
	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();
	return $last_id = trim($rs_lastid[0]);
	$link->close();
}
function checknumerond($numero_nd){
	$link = conexion();
	$qry=$link->query("SELECT AI_notadebito_id FROM bh_notadebito WHERE TX_notadebito_numero = '$numero_nd'")or die($link->error);
	$nr=$qry->num_rows;
	$link->close();
	if($nr > 0){
		return sumarnumerond($numero_nd);
	}else{
		return $numero_nd;
	}
}
function sumarnumerond($numero_nd){
	$pre_numero_nd = "00000000".($numero_nd +1);
		$numero_nd = substr($pre_numero_nd,-8);
		return checknumerond($numero_nd);
}
function get_rel_medida_cantidad($producto_id, $medida_id){
  $link=conexion();
  $prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);
  $prep_producto_medida->bind_param("ii", $producto_id, $medida_id); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
  $rs_producto_medida = $qry_producto_medida->fetch_array();
  $link->close();
  return $rs_producto_medida['TX_rel_productomedida_cantidad'];
}
function update_saldo($saldo,$cliente_id) {
	$link=conexion();
	$qry_cliente = $link->query("SELECT bh_cliente.TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$cliente_id'")or die($link->error);
	$rs_cliente = $qry_cliente->fetch_array();
	$new_saldo = $rs_cliente['TX_cliente_saldo']+$saldo;

	$link->query("UPDATE bh_cliente SET TX_cliente_saldo = '$new_saldo' WHERE AI_cliente_id = '$cliente_id'")or die($link->error);
	$link->close();
}
// ############################## FIN DE FUNCIONES #########################
// ################################## INSERCION NOTA DE CREDITO  #######################################
// VERIFICAR PRODUCTOS A DEVOLVER
$txt_nuevadevolucion="SELECT bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.nuevadevolucion_AI_producto_id, bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id, bh_nuevadevolucion.TX_nuevadevolucion_medida,
bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_impuesto
FROM (bh_nuevadevolucion
	INNER JOIN bh_datoventa ON bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
	WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id'";
$qry_nuevadevolucion=$link->query($txt_nuevadevolucion)or die($link->error);
if ($qry_nuevadevolucion->num_rows < 1) {
	header('Location: http://localhost:8080/berenice/start.php');
}
// OBTENER DATOS DE LA FACTURAF
$qry_facturaf=$link->query("SELECT bh_facturaf.facturaf_AI_cliente_id, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_deficit
 FROM (((bh_nuevadevolucion
  INNER JOIN bh_datoventa ON bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
   INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id)
   	INNER JOIN bh_facturaf ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
	 WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id' ")or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();
// DATOS PARA INSERTAR NOTA DE CREDITO
$cliente_id=$rs_facturaf['facturaf_AI_cliente_id'];
$facturaf_id=$rs_facturaf['AI_facturaf_id'];
$motivo = $_GET['a'];
$fecha = date('Y-m-d');
$hora = date('h:i a');
$destino = $_GET['b'];
$retencion = (100-$_GET['c'])/100;
// OBTENER NUMERO DE NOTA DE CREDITO
$qry_lastnc=$link->query("SELECT AI_notadecredito_id,TX_notadecredito_numero FROM bh_notadecredito ORDER BY AI_notadecredito_id DESC LIMIT 1")or die($link->error);
$rs_lastnc = $qry_lastnc->fetch_array();
$numero_nc = $rs_lastnc['TX_notadecredito_numero'];
$numero_nc = checknumeronc($numero_nc);
// PREVENIR DUPLICACION
$qry_checkcreditnote=$link->query("SELECT AI_notadecredito_id FROM bh_notadecredito WHERE TX_notadecredito_numero = '$numero_nc'")or die($link->error);
$nr_checkcreditnote=$qry_checkcreditnote->num_rows;
if($nr_checkcreditnote < 1){
	$creditnote_id = insert_notadecredito($cliente_id,$facturaf_id,$user_id,$numero_nc,$motivo,$fecha,$hora,$destino,$_GET['c'],$anulated);
}else{
	echo "esta repetida"; return false;
	// header('Location: http://localhost:8080/berenice/start.php');
}
// ################################## INSERCIONES  #######################################
// ########################## CALCULAR MONTO E IMPUESTO ############################
$precio=0;	$impuesto=0;	$descuento=0;
while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array()){
  // INSERTAR DEVOLUCION
	insert_devolucion($creditnote_id,$rs_nuevadevolucion['nuevadevolucion_AI_producto_id'],$rs_nuevadevolucion['nuevadevolucion_AI_datoventa_id'],$user_id,$rs_nuevadevolucion['TX_nuevadevolucion_cantidad'],$rs_nuevadevolucion['TX_nuevadevolucion_medida']);
	// OBTENER INFO DE DATOVENTA
	$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_producto_id, TX_datoventa_medida, TX_datoventa_cantidad FROM bh_datoventa WHERE AI_datoventa_id = '{$rs_nuevadevolucion['nuevadevolucion_AI_datoventa_id']}'")or die($link->error);
	$rs_datoventa=$qry_datoventa->fetch_array();
  // OBTENER FACTORES DE CONVERSION DE MEDIDA
	$rel_nuevadevolucion = get_rel_medida_cantidad($rs_nuevadevolucion['nuevadevolucion_AI_producto_id'],$rs_nuevadevolucion['TX_nuevadevolucion_medida']);
	$rel_datoventa = get_rel_medida_cantidad($rs_datoventa['datoventa_AI_producto_id'],$rs_datoventa['TX_datoventa_medida']);
	$rel_coheficiente = $rel_nuevadevolucion/$rel_datoventa;
  // CALCULO DE MONTOS PARA NOTA DE CREDITO
	$precio_uni=($rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*$rel_coheficiente)*$rs_nuevadevolucion['TX_datoventa_precio'];
	$descuento_uni=($precio_uni*$rs_nuevadevolucion['TX_datoventa_descuento'])/100;
	$precio_descuento_uni=$precio_uni-$descuento_uni;
	$precio_descuento_retencion = $precio_descuento_uni*$retencion;
	$impuesto_uni=($precio_descuento_retencion*$rs_nuevadevolucion['TX_datoventa_impuesto'])/100;
	$precio_descuento_impuesto_uni=$precio_descuento_retencion+$impuesto_uni;

	$precio += $precio_uni;
	$impuesto += $impuesto_uni*$retencion;
	$descuento += $descuento_uni;
};

$monto_nc = round(($precio*$retencion)-$descuento,2);
$exedente = ($destino == 'EFECTIVO' || $anulated != 0) ? '0' : round($monto_nc+$impuesto,2);
// ########################## FIN DE CALCULAR MONTO E IMPUESTO ############################
$link->query("UPDATE bh_notadecredito SET TX_notadecredito_monto = '$monto_nc', TX_notadecredito_impuesto = '$impuesto', TX_notadecredito_exedente = '0' WHERE AI_notadecredito_id = '$creditnote_id'");
$new_saldo = $exedente;

if($anulated != 1){
	// SIN ANULACION
	if ($rs_facturaf['TX_facturaf_deficit'] > 0) {
		$def_saldo = $rs_facturaf['TX_facturaf_deficit']-$new_saldo;
		if ($def_saldo > 0) {
			$new_deficit=$rs_facturaf['TX_facturaf_deficit']-$new_saldo;
			$new_saldo=0;
		}else if ($def_saldo < 0) {
			$new_deficit=0;
			$new_saldo=$new_saldo-$rs_facturaf['TX_facturaf_deficit'];
		}else{
			$new_deficit=0;
			$new_saldo=0;
		}
		$total_nd = $rs_facturaf['TX_facturaf_deficit']-$new_deficit;
		$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit =	'$new_deficit' WHERE AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'")or die($link->error);
		$motivo_nd = 'DEDUCCION POR NC '.$numero_nc;
		if ($total_nd > 0) {

			$qry_lastnd=$link->query("SELECT AI_notadebito_id, TX_notadebito_numero FROM bh_notadebito ORDER BY AI_notadebito_id DESC LIMIT 1")or die($link->error);
			$rs_lastnd=$qry_lastnd->fetch_array();
			$numero_nd = $rs_lastnd['TX_notadebito_numero'];
			$numero_nd = checknumerond($numero_nd);

			$debito_id = insert_notadebito($cliente_id,$user_id,$numero_nd,$motivo_nd,$fecha,$hora,$total_nd);
			$link->query("INSERT INTO rel_facturaf_notadebito (rel_AI_facturaf_id, rel_AI_notadebito_id, TX_rel_facturafnotadebito_importe) VALUES ('{$rs_facturaf['AI_facturaf_id']}','$debito_id','$total_nd')");
			$bh_insert_datodebito="INSERT INTO bh_datodebito (datodebito_AI_notadebito_id, datodebito_AI_user_id,  datodebito_AI_metododepago_id, TX_datodebito_monto, TX_datodebito_numero, TX_datodebito_fecha) VALUES ('$debito_id','$user_id','7','$total_nd','','$fecha')";
			$link->query($bh_insert_datodebito)or die($link->error);
		}
	}
	if($destino == "EFECTIVO"){
		//   #######################	INSERCION DE LA SALIDA
		$impresora_id = get_printer();
		$motivo_efectivo="NOTA DE CREDITO ".$numero_nc;
		$monto_efectivo=round((($precio*$retencion)-$descuento)+$impuesto,2);
		$link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)
		VALUES ('$user_id', '$impresora_id', 'SALIDA', '$motivo_efectivo', '$monto_efectivo', '$fecha', 'ACTIVA')");
	}
}else{
// POR ANULACION
	if ($rs_facturaf['TX_facturaf_deficit'] > 0) {
		$new_deficit=0;
		$new_saldo=0;
		$total_nd = $rs_facturaf['TX_facturaf_deficit']-$new_deficit;
		$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit =	'$new_deficit' WHERE AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'")or die($link->error);
	}
}
// ACTUALIZAR SALDO
update_saldo($new_saldo,$cliente_id);
// ACTUALIZAR DEFICIT
// $link->query("UPDATE bh_facturaf SET TX_facturaf_deficit =	'$new_deficit' WHERE AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'")or die($link->error);
// $impresora_id = get_printer();
// $host_ip=ObtenerIP();
// $host_name=gethostbyaddr($host_ip);
// $qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
// $rs_impresora = $qry_impresora->fetch_array();
// session_open();
echo $_SESSION['creditnote_id'] = $creditnote_id;
