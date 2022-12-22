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
$qry_dato_order->bind_param("s",$rs_order['AI_pedido_id']);
$qry_dato_order->execute();
$result=$qry_dato_order->get_result();
$rs_dato_user=$result->fetch_array();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Orden de Compra - <?php echo $rs_order['TX_proveedor_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">

<?php
$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$fecha = $dias[date('N', strtotime($rs_order['TX_pedido_fecha']))+1];
?>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto">
<tr style="height:6px">
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
<tr style="height:135px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="75px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$time=strtotime($rs_order['TX_pedido_fecha']);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha."&nbsp;-&nbsp;"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:108px">
	<td valign="top" colspan="10">
		<table id="tbl_titulo" class="table table-print">
		<tbody>
		<tr>
			<td>    <h3>Orden de Compra</h3>	</td>
		</tr>
		</tbody>
		</table>
		<table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
  	<tr>
	  	<td valign="top" style="width:50%;">
				<strong>Señores(a): </strong><?php echo ucfirst($rs_order['TX_proveedor_nombre']); ?>
      </td>
      <td valign="top" style="width:20%">
      </td>
      <td valign="top" style="width:30%">
				<strong>N&deg;: </strong><?php echo $rs_order['TX_pedido_numero'] ?>
			</td>
  	</tr>
		</table>
  </td>
</tr>
<tr style="height:638px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_datopedido" class="table table-bordered table-print">
    	<thead style="border:solid; background-color:#DDDDDD;">
      	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripci&oacute;n</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
    		</tr>
			</thead>
      <tbody>
      <?php
			$subtotal = 0;
			$impuesto = 0;
			do{
				?>
      <tr>
      	<td>
        	<?php echo $rs_dato_user['TX_datopedido_cantidad']; ?>
        </td>
        <td>
					<?php echo $r_function->replace_special_character($rs_dato_user['TX_producto_value']); ?>
        </td>
        <td>
					<?php echo number_format($rs_dato_user['TX_datopedido_precio'],2); ?>
        </td>
        <td>
					<?php echo "B/ ".number_format($total4product = $rs_dato_user['TX_datopedido_cantidad']*$rs_dato_user['TX_datopedido_precio'],4); ?>
        </td>
      </tr>
		<?php
		$product_impuesto=($rs_dato_user['TX_producto_exento']*$total4product)/100;
		$impuesto+=$product_impuesto;
		$subtotal+=$total4product;
		}while($rs_dato_user=$result->fetch_array());
		?>
      </tbody>
			<tfoot style="border:solid; background-color:#DDDDDD;">
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
<tr style="height:66px;">
	<td colspan="10">
	<table id="tbl_autorized" class="table table-print">
	<tbody>
	<tr>
		<td>
		<p>
			__________________________
			<br />Autorizado por:<br />
			<?php echo $rs_order['TX_user_seudonimo']; ?>
		</p>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
</tr>

</table>
</body>
</html>
