<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$order_id=$_GET['a'];

$qry_opcion = $link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$qry_order = $link->prepare("select bh_user.TX_user_seudonimo, bh_proveedor.TX_proveedor_nombre, bh_pedido.AI_pedido_id, bh_pedido.TX_pedido_numero, bh_pedido.TX_pedido_fecha, bh_pedido.TX_pedido_status
	FROM ((bh_pedido INNER JOIN bh_user ON bh_user.AI_user_id = bh_pedido.pedido_AI_user_id)
INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_pedido.pedido_AI_proveedor_id)
WHERE bh_pedido.AI_pedido_id = ?");
$qry_order->bind_param('i',$order_id);

$qry_order->execute();
$result=$qry_order->get_result();
$rs_order=$result->fetch_array();

$qry_dato_order = $link->prepare("SELECT bh_producto.TX_producto_value, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.TX_datopedido_precio, bh_producto.TX_producto_exento
FROM (bh_datopedido INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id)
WHERE bh_datopedido.datopedido_AI_pedido_id = ?");
$qry_dato_order->bind_param("i",$rs_order['AI_pedido_id']);
$qry_dato_order->execute();
$result=$qry_dato_order->get_result();
$rs_dato_user=$result->fetch_array();

if ($result->num_rows > 0) {
	header('Location: print_order_html_v.php?a='.$_GET['a'].'');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Orden de Compra - <?php echo $rs_order['TX_proveedor_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<body style="font-family:Arial" onload="window.print();">

<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
?>
<table align="center" cellpadding="0" cellspacing="0" border="0" style="height: 760px;width: 1001px;transform: rotate(90deg);
margin-top: 105px;margin-left: -130px;">
<tr>
<td style="width:50%;">
<!-- ##################        LADO IZQUIERDO     ################################-->
<table id="tbl_print" align="center" cellpadding="0" cellspacing="0" border="0" style="height:760px; width:470px; font-size:14px; padding:0 30px 0 0; ">
<tr style="height:1px">
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
</tr>
<tr style="height:123px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="65px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$time=strtotime($fecha_actual);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha_dia.",<br />"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <h3>Orden de Compra</h3>
  </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table id="tbl_top" class="table table-print" style="width:100%; font-size:12px;">
    	<tr>
				<td style="width:45%">
        <strong>Señores(a): </strong><br /><?php echo ucfirst($rs_order['TX_proveedor_nombre']); ?>
        </td>
        <td style="width:25%">
        </td>
        <td style="width:25%">
        <strong>N&deg: </strong><br /><?php echo $rs_order['TX_pedido_numero'] ?>
        </td>
    	</tr>
		</table>
	</td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
		<table id="tbl_datopedido" class="table table-bordered table-print">
    	<thead>
      	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripci&oacute;n</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
    		</tr>
			</thead>
      <tbody>
      <?php
			$impuesto=0;
			$subtotal=0;
			do{  ?>
      <tr>
      	<td>
        	<?php echo $rs_dato_user['TX_datopedido_cantidad']; ?>
        </td>
        <td>
					<?php echo $rs_dato_user['TX_producto_value']; ?>
        </td>
        <td>
					<?php echo number_format($rs_dato_user['TX_datopedido_precio'],4); ?>
        </td>
        <td>
					<?php echo "B/ ".number_format($total4product = $rs_dato_user['TX_datopedido_cantidad']*$rs_dato_user['TX_datopedido_precio'],2); ?>
        </td>
      </tr>
		<?php
		$impuesto4product=($rs_dato_user['TX_producto_exento']*$total4product)/100;
		$impuesto+=$impuesto4product;
		$subtotal+=$total4product;
		}while($rs_dato_user=$result->fetch_array());
		?>
      </tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<table id="tbl_total" class="table-print table">
					<tbody>
						<tr>
							<td><strong>Sub-Total:</strong></td>
							<td>B/ <?php echo number_format($subtotal,4); ?></td>
							<td><strong>Impuesto:</strong></td>
							<td>B/ <?php echo number_format($impuesto,4); ?></td>
							<td><strong>Total:</strong></td>
							<td>B/ <?php echo number_format($total=$subtotal+$impuesto,2); ?></td>
						</tr>
					</tbody>
					</table>
				</td>
			</tr>
			</tfoot>
	</table>
</td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
    <font style="font-size:12px"><strong>
    <?php
	echo $rs_order['TX_user_seudonimo'];
	?>
    </strong></font>
    <br />
    Recib&iacute; conforme
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################        FIN LADO IZQUIERDO   ######################### --->
</td>
<td style="width:50%;">
<!-- ###############################        LADO DERECHO   ######################### --->
<table id="tbl_print" align="center" cellpadding="0" cellspacing="0" border="0" style="height:760px; width:470px; font-size:14px; padding:0 30px 0 0; ">
<tr style="height:1px">
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
</tr>
<tr style="height:123px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="65px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$time=strtotime($fecha_actual);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha_dia.",<br />"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <h3>Orden de Compra</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table id="tbl_top" class="table table-print" style="width:100%; font-size:12px;">
    	<tr>
				<td style="width:45%">
        <strong>Señores(a): </strong><br /><?php echo ucfirst($rs_order['TX_proveedor_nombre']); ?>
        </td>
        <td style="width:25%">
        </td>
        <td style="width:25%">
        <strong>N&deg: </strong><br /><?php echo $rs_order['TX_pedido_numero'] ?>
        </td>
    	</tr>
		</table>
	</td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
<?php
$qry_dato_order->execute();
$result=$qry_dato_order->get_result();
$rs_dato_user=$result->fetch_array();
 ?>
		<table id="tbl_datopedido" class="table table-bordered table-print">
    	<thead>
      	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripci&oacute;n</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
    		</tr>
			</thead>
      <tbody>
      <?php
			$impuesto=0;
			$subtotal=0;
			do{  ?>
      <tr>
      	<td>
        	<?php echo $rs_dato_user['TX_datopedido_cantidad']; ?>
        </td>
        <td>
					<?php echo $rs_dato_user['TX_producto_value']; ?>
        </td>
        <td>
					<?php echo number_format($rs_dato_user['TX_datopedido_precio'],2); ?>
        </td>
        <td>
					<?php echo "B/ ".number_format($total4product = $rs_dato_user['TX_datopedido_cantidad']*$rs_dato_user['TX_datopedido_precio'],2); ?>
        </td>
      </tr>
		<?php
		$impuesto4product=($rs_dato_user['TX_producto_exento']*$total4product)/100;
		$impuesto+=$impuesto4product;
		$subtotal+=$total4product;
		}while($rs_dato_user=$result->fetch_array());
		?>
      </tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<table id="tbl_total" class="table-print table">
					<tbody>
						<tr>
							<td><strong>Sub-Total:</strong></td>
							<td>B/ <?php echo number_format($subtotal,4); ?></td>
							<td><strong>Impuesto:</strong></td>
							<td>B/ <?php echo number_format($impuesto,4); ?></td>
							<td><strong>Total:</strong></td>
							<td>B/ <?php echo number_format($total=$subtotal+$impuesto,2); ?></td>
						</tr>
					</tbody>
					</table>
				</td>
			</tr>
			</tfoot>
	</table>
</td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
    <font style="font-size:12px"><strong>
    <?php
	echo $rs_order['TX_user_seudonimo'];
	?>
    </strong></font>
    <br />
    Recib&iacute; conforme
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################      FIN LADO DERECHO   ######################### --->
</td>
</tr>
</table>
</body>
</html>
