<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$notadecredito_id=$_GET['a'];

$txt_nc="SELECT bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_hora, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_ticket, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto) as total, bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.TX_notadecredito_retencion, bh_notadecredito.TX_notadecredito_motivo, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, bh_cliente.AI_cliente_id, bh_user.TX_user_seudonimo,
bh_notadecredito.TX_notadecredito_destino
FROM (((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_cliente ON bh_notadecredito.notadecredito_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadecredito.notadecredito_AI_user_id)
WHERE bh_notadecredito.AI_notadecredito_id = '$notadecredito_id'";
$qry_nc = $link->query($txt_nc);
$rs_creditnote = $qry_nc->fetch_array();

$qry_client=$link->query("SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif,
	bh_cliente.TX_cliente_telefono, bh_cliente.TX_cliente_direccion, bh_notadecredito.TX_notadecredito_numero
	FROM (bh_cliente INNER JOIN bh_notadecredito ON bh_cliente.AI_cliente_id = bh_notadecredito.notadecredito_AI_cliente_id)
	WHERE AI_notadecredito_id = '$notadecredito_id'")or die($link->error);
$rs_client=$qry_client->fetch_array();

$qry_datodevolucion = $link->query("SELECT bh_datodevolucion.TX_datodevolucion_cantidad,
	bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion
	FROM ((bh_datodevolucion	INNER JOIN bh_producto ON bh_producto.AI_producto_id =	bh_datodevolucion.datodevolucion_AI_producto_id)
	INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datodevolucion.datodevolucion_AI_datoventa_id)
	WHERE bh_datodevolucion.datodevolucion_AI_notadecredito_id = '$notadecredito_id'")or die($link->error);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NC: <?php echo $rs_client['TX_cliente_nombre']." - ".$rs_client['TX_notadecredito_numero']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
setTimeout("self.close()", 10000);
</script>
<!--  -->
<body style="font-family:Arial" onLoad="window.print()" >
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
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
<tr style="height:131px" align="right">
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
<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo $fecha; ?>
    </td>
</tr>
<tr style="height:40px" align="center">
	<td valign="top" colspan="10"><h4>RECIBO POR NOTA DE CREDITO</h4></td>
</tr>
<tr style="height:190px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table">
		<tbody style="background-color:#DDDDDD; border:solid;">
    	<tr>
        	<td valign="top"  class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?>
            </td>
            <td valign="top"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?>
            </td>
            <td valign="top"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top" colspan="3">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper(substr($rs_client['TX_cliente_direccion'],0,70)); ?>
          </td>
        </tr>
    </table>
    <table id="tbl_creditnote" class="table">
		<tbody style="border:solid; font-size: 12px;">
    	<tr>
        	<td valign="top">
            <strong>Usuario(a): </strong><?php echo strtoupper($rs_creditnote['TX_user_seudonimo']); ?>
            </td>
        	<td valign="top">
            <strong>Fecha: </strong><?php echo $rs_creditnote['TX_notadecredito_fecha']; ?>
            </td>
            <td valign="top">
            <strong>Hora: </strong><?php echo $rs_creditnote['TX_notadecredito_hora']; ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td valign="top">
            <strong>N&deg; N.C.: </strong><?php echo $rs_creditnote['TX_notadecredito_numero']; ?>
            </td>
            <td valign="top">
            <strong>N&deg; Factura: </strong><?php echo $rs_creditnote['TX_facturaf_numero']; ?>
            </td>
            <td valign="top">
            <strong>Retencion: </strong><?php echo $rs_creditnote['TX_notadecredito_retencion']."%"; ?>
            </td>
            <td valign="top">
            <strong>Monto: </strong><?php echo number_format($rs_creditnote['total'],2); ?>
            </td>
    	</tr>
			<tr>
				<td valign="top" colspan="3">
				<strong>Motivo: </strong><?php echo $rs_creditnote['TX_notadecredito_motivo']; ?>
				</td>
				<td valign="top" colspan="3">
				<strong>Destino: </strong><?php echo $rs_creditnote['TX_notadecredito_destino']; ?>
				</td>
			</tr>
    </tbody>
		</table>
    </td>
</tr>
<tr style="height:608px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
    <thead style="border:solid">
    	<tr>
            <th style="width:10%; text-align:center; border:solid 1px #000;">
            <strong>Cantidad</strong>
            </th>
						<th style="width:20%; text-align:center; border:solid 1px #000; border-bottom-left-radius:3px;border-top-left-radius:3px;">
						<strong>Codigo </strong>
	           </th>
            <th style="width:50%; text-align:center; border:solid 1px #000;">
            <strong>Descripcion </strong>
            </th>
            <th style="width:10%; text-align:center; border:solid 1px #000;">
            <strong>Precio</strong>
            </th>
            <th style="width:10%; text-align:center; border:solid 1px #000; border-bottom-right-radius:3px;border-top-right-radius:3px;">
            <strong>Total. </strong>
            </th>
		</tr>
	</thead>
    <tbody>
<?php
		$subtotal=0;	$totalitbm=0;	$totaldescuento=0;	$index = 1;	$pager = 0;
		while($rs_datodevolucion=$qry_datodevolucion->fetch_array()){
			$pager++;
			if($index === 1){
				if($pager === 14){
					$pager = 0;
					$index++;
?>
				</tbody>
				</table>
			</td>
		</tr>
		<tr style="height:800px;">
			<td valign="top" colspan="10" style="padding-top:2px;">
		    <table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
		    <thead style="border:solid">
		    	<tr>
						<th style="width:10%; text-align:center; border:solid 1px #000;">
            <strong>Cantidad</strong>
            </th>
						<th style="width:20%; text-align:center; border:solid 1px #000; border-bottom-left-radius:3px;border-top-left-radius:3px;">
						<strong>Codigo </strong>
	           </th>
            <th style="width:50%; text-align:center; border:solid 1px #000;">
            <strong>Descripcion </strong>
            </th>
            <th style="width:10%; text-align:center; border:solid 1px #000;">
            <strong>Precio</strong>
            </th>
            <th style="width:10%; text-align:center; border:solid 1px #000; border-bottom-right-radius:3px;border-top-right-radius:3px;">
            <strong>Total. </strong>
            </th>
					</tr>
				</thead>
				<tbody>
<?php
				}
			}else{
				if($pager === 23){
					$pager = 0;
					$index++;
?>
							</tbody>
							</table>
						</td>
					</tr>
					<tr style="height:580px;">
						<td valign="top" colspan="10" style="padding-top:2px;">
							<table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
							<thead style="border:solid">
								<tr>
									<th style="width:10%; text-align:center; border:solid 1px #000;">
			            <strong>Cantidad</strong>
			            </th>
									<th style="width:20%; text-align:center; border:solid 1px #000; border-bottom-left-radius:3px;border-top-left-radius:3px;">
									<strong>Codigo </strong>
				           </th>
			            <th style="width:50%; text-align:center; border:solid 1px #000;">
			            <strong>Descripcion </strong>
			            </th>
			            <th style="width:10%; text-align:center; border:solid 1px #000;">
			            <strong>Precio</strong>
			            </th>
			            <th style="width:10%; text-align:center; border:solid 1px #000; border-bottom-right-radius:3px;border-top-right-radius:3px;">
			            <strong>Total. </strong>
			            </th>
								</tr>
							</thead>
							<tbody>
<?php
				}
			}
?>

    	<tr style="height:41px;">
				<td style="width:20%; text-align:center;">
					<?php echo $rs_datodevolucion['TX_datodevolucion_cantidad']; ?>
				</td>
				<td style="width:20%; text-align:center;">
					<?php echo $rs_datodevolucion['TX_producto_codigo']; ?>
        </td>
        <td style="width:50%; text-align:center;">
					<?php echo substr($r_function->replace_special_character($rs_datodevolucion['TX_datoventa_descripcion']),0,96); ?>
        </td>
<?php
					$descuento=($rs_datodevolucion['TX_datoventa_precio']*$rs_datodevolucion['TX_datoventa_descuento'])/100;
					$precio_descuento=$rs_datodevolucion['TX_datoventa_precio']-$descuento;
					$impuesto=($precio_descuento*$rs_datodevolucion['TX_datoventa_impuesto'])/100;
					$precio_descuento_impuesto=$precio_descuento+$impuesto;
?>
        <td style="width:10%; text-align:center;">
<?php
					$retenido = ($rs_creditnote['TX_notadecredito_retencion']*$precio_descuento_impuesto)/100;
					$precio_descuento_impuesto -= $retenido;
					echo number_format($precio_descuento_impuesto,2);
?>
        </td>
        <td style="width:10%; text-align:center;">
<?php
					$subtotal = $precio_descuento_impuesto*$rs_datodevolucion['TX_datodevolucion_cantidad'];
					echo number_format($subtotal,2);
?>
        </td>
		</tr>
<?php
		}?>
 	</tbody>
    <tfoot>
    <tr>
    	<td colspan="5"> </td>
    </tr>
    </tfoot>
	</table>
    </td>
</tr>
</table>
</body>
</html>
