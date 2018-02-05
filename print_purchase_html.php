<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$facturacompra_id=$_GET['a'];

$qry_facturacompra=$link->query("SELECT bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero,
	bh_facturacompra.TX_facturacompra_ordendecompra, bh_facturacompra.TX_facturacompra_observacion, bh_almacen.TX_almacen_value,
	bh_proveedor.TX_proveedor_nombre, bh_proveedor.TX_proveedor_cif, bh_proveedor.TX_proveedor_telefono, bh_proveedor.TX_proveedor_dv,
	bh_proveedor.TX_proveedor_direccion
	FROM ((bh_facturacompra
		INNER JOIN bh_almacen ON bh_almacen.AI_almacen_id = bh_facturacompra.TX_facturacompra_almacen)
		INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id)
		WHERE bh_facturacompra.AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
$rs_facturacompra = $qry_facturacompra->fetch_array(MYSQLI_ASSOC);

$txt_datocompra =	"SELECT bh_datocompra.TX_datocompra_cantidad, bh_datocompra.TX_datocompra_precio, bh_datocompra.TX_datocompra_impuesto, bh_datocompra.TX_datocompra_descuento,
bh_producto.TX_producto_value, bh_producto.TX_producto_codigo
FROM (bh_datocompra
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id)
WHERE bh_datocompra.datocompra_AI_facturacompra_id = '$facturacompra_id'";
$qry_datocompra = $link->query($txt_datocompra)or die($link->error);
$rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $rs_facturacompra['TX_proveedor_nombre']." - #".$rs_facturacompra['TX_facturacompra_numero'] ?></title>
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
$fecha = $dias[date('N', strtotime($rs_facturacompra['TX_facturacompra_fecha']))+1];
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
		$time=strtotime($rs_facturacompra['TX_facturacompra_fecha']);
		$date=date('d-m-Y',$time);
	?>
<strong><?php echo $fecha."&nbsp;-&nbsp;"; ?></strong><?php echo $date; ?>
    </td>

</tr>
<tr style="height:169px">
	<td valign="top" colspan="10">
		<table id="tbl_tittle" class="table table-condensed">
		<tr>
			<td>
				<h4>FACTURA DE COMPRA</h4>
			</td>
		</tr>
		</table>
    <table id="tbl_provider" class="table table-print" style="border:solid; background-color:#DDDDDD;">
  	<tr>
    	<td class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      	<strong>Proveedor: </strong><?php echo strtoupper($rs_facturacompra['TX_proveedor_nombre']); ?>
      </td>
			<td class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<strong>RUC: </strong><?php echo $rs_facturacompra['TX_proveedor_cif']; ?>
				<strong>DV: </strong><?php echo $rs_facturacompra['TX_proveedor_dv']; ?>
      </td>
			<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Tel.: </strong><?php echo strtoupper($rs_facturacompra['TX_proveedor_telefono']); ?>
      </td>
  	</tr>
		<tr>
			<td colspan="4">
				<strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_facturacompra['TX_proveedor_direccion']); ?>
			</td>
		</tr>
		</table>
	  <table id="tbl_compra" class="table table-print" style="border:solid; background-color:#DDDDDD;">
		<tr>
	  	<td valign="top" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Factura N&deg;: </strong><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?>
	    </td>
	    <td valign="top" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
		    <strong>Orden de Compra N&deg;: </strong><?php echo $rs_facturacompra['TX_facturacompra_ordendecompra']; ?>
	    </td>
			<td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		    <strong>Fecha: </strong><?php echo $rs_facturacompra['TX_facturacompra_fecha']; ?>
	    </td>
			<td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		    <strong>Almacen: </strong><?php echo $rs_facturacompra['TX_almacen_value']; ?>
	    </td>
	  </tr>
	  </table>

  </td>
</tr>
<tr style="height:45px">
	<td valign="top" colspan="10">

    <table id="tbl_observation" class="table table-print" style="border:solid;">
		<tr>
	  	<td>
		    <strong>Observaci&oacute;n: </strong><?php echo strtoupper($rs_facturacompra['TX_facturacompra_observacion']); ?>
	    </td>
	  </tr>
    </table>

  </td>
</tr>
<tr style="height:620px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table table id="tbl_product" class="table table-print table-bordered table-striped" >
    <thead style="border:solid">
    	<tr>
	      <th>
					<strong>Codigo </strong>
	      </th>
	      <th>
		      <strong>Detalle</strong>
	      </th>
	      <th>
		      <strong>Cant. </strong>
	      </th>
	      <th>
		      <strong>Base</strong>
	      </th>
	      <th>
		      <strong>Desc.</strong>
	      </th>
				<th>
		      <strong>Imp.</strong>
	      </th>
				<th>
		      <strong>Precio</strong>
	      </th>
	      <th>
		      <strong>Total. </strong>
	      </th>
		</tr>
		</thead>
    <tbody>
	<?php
		$total=0;
		$total_impuesto=0;
		$total_descuento=0;
		$total_base=0;
	do{
		$base4product=$rs_datocompra['TX_datocompra_cantidad']*$rs_datocompra['TX_datocompra_precio'];
		$descuento=($rs_datocompra['TX_datocompra_descuento']*$rs_datocompra['TX_datocompra_precio'])/100;
		$precio_descuento=$rs_datocompra['TX_datocompra_precio']-$descuento;
		$impuesto=($rs_datocompra['TX_datocompra_impuesto']*$precio_descuento)/100;
		$precio_impuesto=$precio_descuento+$impuesto;
		$subtotal = $rs_datocompra['TX_datocompra_cantidad'] * $precio_impuesto;

	$total_base += $base4product;
	$total_descuento += $descuento*$rs_datocompra['TX_datocompra_cantidad'];
	$total_impuesto += $impuesto*$rs_datocompra['TX_datocompra_cantidad'];
	$total += $subtotal;
	?>
		<tr>
			<td><?php echo $rs_datocompra['TX_producto_codigo']; ?></td>
			<td><?php echo $rs_datocompra['TX_producto_value']; ?></td>
			<td><?php echo $rs_datocompra['TX_datocompra_cantidad']; ?></td>
			<td><?php echo $rs_datocompra['TX_datocompra_precio']; ?></td>
			<td><?php echo $descuento; ?></td>
			<td><?php echo $impuesto; ?></td>
			<td><?php echo $precio_impuesto; ?></td>
			<td><?php echo $subtotal; ?></td>
		</tr>
	<?php }while($rs_datocompra=$qry_datocompra->fetch_array(MYSQLI_ASSOC)) ?>
 	</tbody>
	<tfoot>
	<tr>
		<td colspan="8">
		<table id="tbl_total" class="table table-print table-condensed">
		<tr>
			<td><strong>SUBTOTAL: </strong><?php echo "B/ ".number_format($total_base,4)."<br />"; ?></td>
			<td><strong>IMPUESTO: </strong><?php echo "B/ ".number_format($total_impuesto,4)."<br />"; ?></td>
			<td><strong>DESCUENTO: </strong><?php echo "B/ ".number_format($total_descuento,4)."<br />"; ?></td>
			<td><strong>TOTAL: </strong><?php echo "B/ ".number_format($total,2) ?></td>
		</tr>
		</table>
		</td>
	</tr>
	</tfoot>
	</table>
    </td>
</tr>
    </tbody>
    </table>
    </td>
</tr>
</table>
</body>
</html>
