<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$date=date('Y-m-d',strtotime($_GET['a']));
$provider=$_GET['b'];
$billnumber=$_GET['c'];
$warehouse=$_GET['e'];
$purchaseorder=$_GET['f'];
$cpp = $_GET['g'];
$preguardado=$_GET['h'];

$fecha_actual=date('Y-m-d');

$qry_invoice=$link->query("SELECT AI_facturacompra_id FROM bh_facturacompra WHERE facturacompra_AI_proveedor_id = '$provider' AND TX_facturacompra_numero = '$billnumber'")or die ($link->error);
$rs_invoice=$qry_invoice->fetch_array(MYSQLI_ASSOC);
if ($qry_invoice -> num_rows > 0) {
	$link->query("DELETE FROM bh_facturacompra WHERE AI_facturacompra_id = '{$rs_invoice['AI_facturacompra_id']}'")or die($link->error);
	$link->query("DELETE FROM bh_datocompra WHERE datocompra_AI_facturacompra_id = '{$rs_invoice['AI_facturacompra_id']}'")or die($link->error);
}

// ##################################   INSERTAR DATOCOMPRA   ###################
function ins_datocompra($factura_id,$product,$cantidad,$precio,$itbm,$descuento,$p4,$preguardado){
	$link = conexion();
	$fecha_actual = date('Y-m-d');

	$qry_product=$link->query("SELECT TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$product'");
	$row_product=$qry_product->fetch_array();
	$stock = $row_product[0];
	if($stock == '999'){
		$stock = '0';
	}
	$suma=$stock+$cantidad;
	if ($preguardado < 1) {
		$link->query("UPDATE bh_producto SET TX_producto_cantidad='$suma' WHERE AI_producto_id = '$product'");
	}
	$link->query("INSERT INTO bh_datocompra (datocompra_AI_facturacompra_id, datocompra_AI_producto_id, TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento, TX_datocompra_existencia, TX_datocompra_p4)
		VALUES ('$factura_id','$product','$cantidad','$precio','$itbm','$descuento','$stock','$p4')");


	$qry_precio = $link->query("SELECT AI_precio_id FROM bh_precio WHERE precio_AI_producto_id = '$product' AND TX_precio_cuatro = '$p4' AND TX_precio_fecha = '$fecha_actual' AND TX_precio_inactivo='0' ");
	if($nr_precio = $qry_precio->num_rows < 1 && $p4 > 0 && $preguardado < 1){
		$link->query("UPDATE bh_precio SET TX_precio_inactivo='1' WHERE precio_AI_producto_id = '$product'");
		$txt_insert_precio="INSERT INTO bh_precio (precio_AI_producto_id, TX_precio_cuatro, TX_precio_fecha ) VALUES ('$product','$p4','$fecha_actual')";
		$link->query($txt_insert_precio);
	}
}

function upd_datocompra($factura_id,$product,$cantidad,$precio,$itbm,$descuento){
	$link = conexion();
	$link->query("DELETE FROM bh_datocompra WHERE datocompra_AI_facturacompra_id = $factura_id");
	$link->query("INSERT INTO bh_datocompra (datocompra_AI_facturacompra_id, datocompra_AI_producto_id, TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento)
		VALUES ('$factura_id','$product','$cantidad','$precio','$itbm','$descuento')");
	}

function upd_bh($id,$date,$provider,$billnumber,$warehouse,$purchaseorder,$cpp){
	$link = conexion();
	$link->query("UPDATE bh_facturacompra SET TX_facturacompra_fecha='$date', facturacompra_AI_proveedor_id='$provider', TX_facturacompra_numero='$billnumber', TX_facturacompra_almacen='$warehouse', TX_facturacompra_ordendecompra='$purchaseorder', TX_facturacompra_status='$cpp' WHERE AI_facturacompra_id = '$id'");

	$qry_nuevacompra=$link->query("SELECT * FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'");
	$rs_nuevacompra=$qry_nuevacompra->fetch_array();
	do{
		upd_datocompra($id,$rs_nuevacompra['nuevacompra_AI_producto_id'],$rs_nuevacompra['TX_nuevacompra_unidades'],$rs_nuevacompra['TX_nuevacompra_precio'],$rs_nuevacompra['TX_nuevacompra_itbm'],$rs_nuevacompra['TX_nuevacompra_descuento']);
	}while($rs_nuevacompra=$qry_nuevacompra->fetch_array());
}

function ins_bh($date,$provider,$billnumber,$warehouse,$purchaseorder,$cpp,$preguardado){
	$link = conexion();

	$fecha_actual=date('Y-m-d');
	$link->query("INSERT INTO bh_facturacompra
		(TX_facturacompra_fecha, facturacompra_AI_proveedor_id, TX_facturacompra_numero, TX_facturacompra_almacen, TX_facturacompra_ordendecompra, TX_facturacompra_status, facturacompra_AI_user_id, TX_facturacompra_preguardado, TX_facturacompra_elaboracion)
		VALUES ('$date', '$provider', '$billnumber', '$warehouse', '$purchaseorder', '$cpp', '{$_COOKIE['coo_iuser']}',	'$preguardado','$fecha_actual')")or die($link->error);

	$rs = $link->query("SELECT LAST_INSERT_ID()");
	$rs_lastid = $rs->fetch_array();
	$last_id = trim($rs_lastid[0]);

	$txt_nuevacompra="SELECT * FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'";
	$qry_nuevacompra=$link->query($txt_nuevacompra)or die($link->error);
	$rs_nuevacompra=$qry_nuevacompra->fetch_array();
	$total=0;
		do{

			ins_datocompra($last_id,$rs_nuevacompra['nuevacompra_AI_producto_id'],$rs_nuevacompra['TX_nuevacompra_unidades'],$rs_nuevacompra['TX_nuevacompra_precio'],$rs_nuevacompra['TX_nuevacompra_itbm'],$rs_nuevacompra['TX_nuevacompra_descuento'],$rs_nuevacompra['TX_nuevacompra_p4'],$preguardado);

			$descuento = ($rs_nuevacompra['TX_nuevacompra_descuento']*$rs_nuevacompra['TX_nuevacompra_precio'])/100;
			$precio_descuento = $rs_nuevacompra['TX_nuevacompra_precio']-$descuento;
			$impuesto = ($rs_nuevacompra['TX_nuevacompra_itbm']*$precio_descuento)/100;
			$precio_impuesto = $precio_descuento+$impuesto;
			$precio_producto = $precio_impuesto *$rs_nuevacompra['TX_nuevacompra_unidades'];
			$total += $precio_producto;


		}while($rs_nuevacompra=$qry_nuevacompra->fetch_array());
		$total = round($total,2);
		if ($cpp === 'POR PAGAR' && $preguardado < 1) {
			$link->query("INSERT INTO bh_cpp (TX_cpp_total, TX_cpp_saldo, TX_cpp_fecha, cpp_AI_proveedor_id, cpp_AI_user_id, cpp_AI_facturacompra_id) VALUES ('$total','$total','$date','$provider','{$_COOKIE['coo_iuser']}','$last_id')")or die($link->error);
		}

		return $last_id;
	}

	$last_id = ins_bh($date,$provider,$billnumber,$warehouse,$purchaseorder,$cpp,$preguardado);
	echo $last_id;
	$link->query("DELETE FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
?>
