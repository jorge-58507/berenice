<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$raw_compradevolucion = $_GET['b'];
$facturacompra_id = $raw_compradevolucion['facturacompra'];

// echo json_encode($raw_compradevolucion);
// return false;

$prep_producto = $link->prepare("SELECT AI_producto_id, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);
$prep_datocompra = $link->prepare("SELECT * FROM bh_datocompra WHERE AI_datocompra_id = ?")or die($link->error);
$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

// ############################## FUNCIONES #####################
$qry_datocompra = $link->query("SELECT AI_datocompra_id FROM bh_datocompra WHERE datocompra_AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
if ($qry_datocompra->num_rows < 1 || empty($raw_compradevolucion['datocompra'])) {
	header('Location: http://localhost:8080/berenice/start.php');
}
$qry_facturacompra = $link->query("SELECT facturacompra_AI_proveedor_id, AI_facturacompra_id FROM bh_facturacompra WHERE AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
$rs_facturacompra = $qry_facturacompra->fetch_array(MYSQLI_ASSOC);
$proveedor_id=$rs_facturacompra['facturacompra_AI_proveedor_id'];
$facturacompra_id=$rs_facturacompra['AI_facturacompra_id'];
$motivo = $_GET['a'];
$fecha = date('Y-m-d');
$hora = date('h:i a');

$total_precio = 0; $total_impuesto = 0; $total_descuento = 0;
foreach ($raw_compradevolucion['datocompra'] as $key => $obj) {
	$datocompra_id = $obj['id'];	$cantidad = $obj['cantidad'];
	$prep_datocompra->bind_param('i',$datocompra_id); $prep_datocompra->execute(); $qry_datocompra=$prep_datocompra->get_result();
	$rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);
	$descuento =  $rs_datocompra['TX_datocompra_precio']*($rs_datocompra['TX_datocompra_descuento']/100);
	$precio = $rs_datocompra['TX_datocompra_precio'];
	$impuesto = ($precio-$descuento)*($rs_datocompra['TX_datocompra_impuesto']/100);
	$total_descuento += $descuento*$cantidad;	$total_precio += $precio*$cantidad;	$total_impuesto += $impuesto*$cantidad;
}

$link->query("INSERT INTO bh_compradevolucion (compradevolucion_AI_proveedor_id, compradevolucion_AI_facturacompra_id, compradevolucion_AI_user_id, TX_compradevolucion_monto, TX_compradevolucion_descuento, TX_compradevolucion_impuesto, TX_compradevolucion_motivo, TX_compradevolucion_fecha, TX_compradevolucion_hora)
VALUES ('$proveedor_id','$facturacompra_id','{$_COOKIE['coo_iuser']}','$total_precio','$total_descuento','$total_impuesto','$motivo','$fecha','$hora')")or die($link->error);

$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
$rs_lastid = $qry_lastid->fetch_array();
$last_id = trim($rs_lastid[0]);

foreach ($raw_compradevolucion['datocompra'] as $key => $obj) {
	$datocompra_id = $obj['id'];	$cantidad = $obj['cantidad'];
	// $medida = $obj['medida'];
	$prep_datocompra->bind_param('i',$datocompra_id); $prep_datocompra->execute(); $qry_datocompra=$prep_datocompra->get_result();
	$rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);

	$prep_producto_medida->bind_param('ii',$producto_id,$medida);
	$producto_id = $rs_datocompra['datocompra_AI_producto_id'];

	$medida = $rs_datocompra['TX_datocompra_medida'];
	$prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
	$rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
	$compra_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

	$medida = $obj['medida'];
	$prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
	$rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
	$devolucion_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

	$factor = $compra_qtymedida/$devolucion_qtymedida;
	$prep_producto->bind_param('i',$producto_id); $prep_producto->execute(); $qry_producto = $prep_producto->get_result();
	$rs_producto = $qry_producto->fetch_array();

	$old_qty = $rs_producto['TX_producto_cantidad'];
	$qty_measurement = $cantidad*$factor;
	$new_qty = $old_qty-$qty_measurement;

	$link->query("INSERT INTO bh_datocompradevolucion (datocompradevolucion_AI_compradevolucion_id, datocompradevolucion_AI_producto_id, datocompradevolucion_AI_datocompra_id, datocompradevolucion_AI_user_id, TX_datocompradevolucion_cantidad, datocompradevolucion_AI_medida_id) VALUES ('$last_id','$producto_id','$datocompra_id','{$_COOKIE['coo_iuser']}','$qty_measurement','$medida')")or die($link->error);
	$link->query("UPDATE bh_producto SET TX_producto_cantidad = '$new_qty' WHERE AI_producto_id = '$producto_id'")or die($link->error);
}

echo json_encode($return=['compradevolucion_id' => $last_id]);

// foreach ($raw_compradevolucion['datocompra'] as $key => $obj) {
// 	$datocompra_id = $obj['id'];	$cantidad = $obj['cantidad'];	$medida = $obj['medida'];
// 	$prep_datocompra->bind_param('i',$datocompra_id); $prep_datocompra->execute(); $qry_datocompra=$prep_datocompra->get_result();
// 	$rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);
//
// 	$prep_producto->bind_param('i',$producto_id); $prep_producto->execute(); $qry_producto = $prep_producto->get_result();
// 	$rs_producto = $qry_producto->fetch_array();
//
// 	$old_qty = $rs_producto['TX_producto_cantidad'];
// 	$new_qty = $old_qty+$cantidad
// 	$link->query("UPDATE bh_producto SET ('TX_producto_cantidad') VALUES ($new_qty)")or die($link->error);
//
// }

/*
// OBTENER NUMERO DE NOTA DE CREDITO
			// $qry_lastnc=$link->query("SELECT AI_notadecredito_id,TX_notadecredito_numero FROM bh_notadecredito ORDER BY AI_notadecredito_id DESC LIMIT 1")or die($link->error);
			// $rs_lastnc = $qry_lastnc->fetch_array();
			// $numero_nc = $rs_lastnc['TX_notadecredito_numero'];
			// $numero_nc = checknumeronc($numero_nc);
// PREVENIR DUPLICACION
			// $qry_checkcreditnote=$link->query("SELECT AI_notadecredito_id FROM bh_notadecredito WHERE TX_notadecredito_numero = '$numero_nc'")or die($link->error);
			// $nr_checkcreditnote=$qry_checkcreditnote->num_rows;
			// if($nr_checkcreditnote < 1){
			// 	$creditnote_id = insert_notadecredito($cliente_id,$facturaf_id,$user_id,$numero_nc,$motivo,$fecha,$hora,$destino,$_GET['c'],$anulated);
			// }else{
			// 	echo "esta repetida"; return false;
			// 	// header('Location: http://localhost:8080/berenice/start.php');
			// }
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
*/
