<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_stock.php';

$fecha_actual=date('Y-m-d');
$qry_datopedido = $link->prepare("SELECT bh_producto.TX_producto_value, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.TX_datopedido_precio FROM (bh_datopedido INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id) WHERE bh_datopedido.datopedido_AI_pedido_id = ?")or die($link->error);
$qry_datocompra = $link->prepare("SELECT bh_producto.TX_producto_value, bh_datocompra.TX_datocompra_cantidad, bh_datocompra.TX_datocompra_precio, bh_datocompra.TX_datocompra_descuento, bh_datocompra.TX_datocompra_impuesto FROM (bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) WHERE datocompra_AI_facturacompra_id = ?")or die($link->error);

$qry_pedido = $link->prepare("SELECT bh_proveedor.TX_proveedor_nombre, bh_pedido.TX_pedido_numero, bh_pedido.TX_pedido_fecha, bh_user.TX_user_seudonimo FROM ((bh_pedido INNER JOIN bh_user ON bh_user.AI_user_id = bh_pedido.pedido_AI_user_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_pedido.pedido_AI_proveedor_id) WHERE AI_pedido_id = ?");
$qry_facturacompra = $link->prepare("SELECT bh_proveedor.TX_proveedor_nombre, bh_facturacompra.TX_facturacompra_numero, bh_facturacompra.TX_facturacompra_fecha, bh_user.TX_user_seudonimo FROM ((bh_facturacompra INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturacompra.facturacompra_AI_user_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id) WHERE AI_facturacompra_id = ?")or die($link->error);

if ($_GET['a'] === 'oc') {
	$qry_pedido->bind_param("i",$_GET['b']); $qry_pedido->execute();
	$result = $qry_pedido->get_result(); $rs_result=$result->fetch_array();

	$qry_datopedido->bind_param("i",$_GET['b']); $qry_datopedido->execute();
	$result = $qry_datopedido->get_result();
	$raw_datoresult=array(); $i=0;
	while($rs_datoresult=$result->fetch_array()){
		$raw_datoresult[$i]['producto'] = $rs_datoresult['TX_producto_value'];
		$raw_datoresult[$i]['cantidad'] = $rs_datoresult['TX_datopedido_cantidad'];
		$raw_datoresult[$i]['precio'] = $rs_datoresult['TX_datopedido_precio'];
		$i++;
	};
	$tipo_doc = 'Orden de Compra';
}
if ($_GET['a'] === 'fc') {
	$qry_facturacompra->bind_param("i",$_GET['b']); $qry_facturacompra->execute();
	$result = $qry_facturacompra->get_result(); $rs_result=$result->fetch_array();

	$qry_datocompra->bind_param("i",$_GET['b']); $qry_datocompra->execute();
	$result = $qry_datocompra->get_result();
	$raw_datoresult=array(); $i=0;
	while($rs_datoresult=$result->fetch_array()){
		$raw_datoresult[$i]['producto'] = $rs_datoresult['TX_producto_value'];
		$raw_datoresult[$i]['cantidad'] = $rs_datoresult['TX_datocompra_cantidad'];
		$precio_descuento = $rs_datoresult['TX_datocompra_precio']-(($rs_datoresult['TX_datocompra_precio']*$rs_datoresult['TX_datocompra_descuento'])/100);
		$raw_datoresult[$i]['precio'] = $precio_descuento+(($precio_descuento*$rs_datoresult['TX_datocompra_impuesto'])/100);
		$i++;
	};
	$tipo_doc = 'Factura de Compra';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

});

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<form action="sale.php" method="post" name="form_sell"  id="form_sell">
	<div class="container-fluid">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="">Proveedor</label>
			<span class="form-control bg-disabled"><?php echo $rs_result[0]; ?></span>
		</div>
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="">Numero</label>
			<span class="form-control bg-disabled"><?php echo $rs_result[1]; ?></span>
		</div>
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="">Fecha</label>
			<span class="form-control bg-disabled"><?php echo $rs_result[2]; ?></span>
		</div>
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="">Usuario</label>
			<span class="form-control bg-disabled"><?php echo $rs_result[3]; ?></span>
		</div>
	</div>
	<div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<caption>Detalle de <?php echo $tipo_doc; ?></caption>
	<thead class="bg-primary">
    	<tr>
        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6 al_center">PRODUCTOS</th>
    	  <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">CANTIDAD</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">PRECIO</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">TOTAL</th>
      </tr>
    </thead>
    <tbody>
<?php
		$total_doc=0;
		foreach ($raw_datoresult as $key => $value): $total_doc+=($value['cantidad']*$value['precio'])?>
	<tr>
		<td><?php echo $value['producto']; ?></td>
		<td class="al_center"><?php echo $value['cantidad']; ?></td>
		<td class="al_center"><?php echo "B/ ".number_format($value['precio'],2); ?></td>
		<td class="al_center"><?php echo "B/ ".number_format($value['cantidad']*$value['precio'],2); ?></td>
	</tr>
<?php endforeach; ?>
    </tbody>
		<tfoot class="bg-primary">
    	<tr>
      	<td colspan="3"></td>
				<td class="al_center"><?php echo "<strong> B/ ".number_format($total_doc,2)."</strong>"; ?></td>
			</tr>
    </tfoot>
</table>

	</div>
</form>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
