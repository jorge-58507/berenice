<?php
require '../../bh_con.php';
$link = conexion();
$client_id=$_GET['b'];
$time=strtotime($_GET['a']);
$date=date('Y-m-d',$time);
$vendor_id=$_GET['d'];
$total=$_GET['f'];

//function checkfacturaventa($numero){
//	$qry_checkfacturaventa=mysql_query("SELECT * FROM bh_facturaventa WHERE TX_facturaventa_numero = '$numero'");
//	$nr_checkfacturaventa=mysql_num_rows($qry_checkfacturaventa);
//	if($nr_checkfacturaventa > 0){
//		return sumarfacturaventa($numero);
//	}else{
//		return $numero;
//	}
//}
//function sumarfacturaventa($numero){
//		return checkfacturaventa($numero+1);
//}

//function ins_facturaventa($date,$number,$total){
//	$link = conexion();
//
//$client_id=$_GET['b'];
//$client=$_GET['c'];
//$observation=$_GET['g'];
//
//	if(empty($client_id)){
//		mysql_query("INSERT INTO bh_cliente (TX_cliente_nombre) VALUES ('$client')");
//		$qry_lastclientid=mysql_query("SELECT MAX(AI_cliente_id) AS id FROM bh_cliente");
//		if ($row = mysql_fetch_row($qry_lastclientid)) {
//			$last_clientid = trim($row[0]);
//		}
//		$client_id=$last_clientid;
//	}else{
//		$client_id = $client_id;
//	}
//
//	mysql_query("INSERT INTO bh_facturaventa (TX_facturaventa_fecha, facturaventa_AI_cliente_id, facturaventa_AI_user_id, TX_facturaventa_numero, TX_facturaventa_total, TX_facturaventa_status, TX_facturaventa_observacion)
//	VALUES ('$date', '$client_id', '{$_COOKIE['coo_iuser']}', '$number', '$total', 'ACTIVA', '$observation')");
//
//	$qry_lastfacturaventaid = mysql_query("SELECT MAX(AI_facturaventa_id) AS id FROM bh_facturaventa");
//	if ($row = mysql_fetch_row($qry_lastfacturaventaid)) {
//		$last_facturaventaid = trim($row[0]);
//	}
//
//	$qry_nuevaventa=mysql_query("SELECT * FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'");
//	$rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa);
//
//	do{
//
//		ins_datoventa($last_facturaventaid,$rs_nuevaventa['nuevaventa_AI_producto_id'],$rs_nuevaventa['TX_nuevaventa_unidades'], $rs_nuevaventa['TX_nuevaventa_precio'],$rs_nuevaventa['TX_nuevaventa_itbm'],$rs_nuevaventa['TX_nuevaventa_descuento']);
//
//	}while($rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa));
/*
//	?>
//    <label for="txt_filterclient">Cliente:</label>
//    <input type="text" class="form-control" alt="<?php echo $client_id ?>" id="txt_filterclient" name="txt_filterclient" onkeyup="filter_client_sell(this);" value="<?php echo $client ?>" />
//    <?php
//}
*/
function ins_datoventa($venta_id,$product,$cantidad,$precio,$itbm,$descuento){
$link = conexion();
	mysql_query("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento)
	VALUES ('$venta_id', '{$_COOKIE['coo_iuser']}','$product','$cantidad','$precio','$itbm','$descuento')");
}

function upd_facturaventa($venta_id,$date,$number,$total){
	$link = conexion();
	$client_id=$_GET['b'];
	$client=$_GET['c'];
	if(empty($client_id)){
		mysql_query("INSERT INTO bh_cliente (TX_cliente_nombre) VALUES ('$client')");
		$qry_lastclientid=mysql_query("SELECT MAX(AI_cliente_id) AS id FROM bh_cliente");
		if ($row = mysql_fetch_row($qry_lastclientid)) {
			$last_clientid = trim($row[0]);
		}
		$client_id=$last_clientid;
	}
	mysql_query("UPDATE bh_facturaventa SET TX_facturaventa_fecha='$date', facturaventa_AI_cliente_id='$client_id',  TX_facturaventa_numero='$number', TX_facturaventa_total='$total'
	WHERE AI_facturaventa_id = $venta_id");

	mysql_query("DELETE FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$venta_id'");

	$qry_nuevaventa=mysql_query("SELECT * FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'");
	$rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa);

	do{

ins_datoventa($venta_id,$rs_nuevaventa['nuevaventa_AI_producto_id'],$rs_nuevaventa['TX_nuevaventa_unidades'], $rs_nuevaventa['TX_nuevaventa_precio'],$rs_nuevaventa['TX_nuevaventa_itbm'],$rs_nuevaventa['TX_nuevaventa_descuento']);

	}while($rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa));
	?>
    <label for="txt_filterclient">Cliente:</label>
    <input type="text" class="form-control" alt="<?php echo $client_id ?>" id="txt_filterclient" name="txt_filterclient" onkeyup="filter_client_sell(this);" value="<?php echo $client ?>" />
    <?php
}

		$number=$_GET['e'];
		$qry_checksell=mysql_query("SELECT AI_facturaventa_id FROM bh_facturaventa WHERE TX_facturaventa_numero = '$number'", $link);
		$rs_checksell=mysql_fetch_assoc($qry_checksell);
		upd_facturaventa($rs_checksell['AI_facturaventa_id'],$date,$number,$total);


?>
