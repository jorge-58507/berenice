<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$facturaf_id=$_GET['a'];

$qry_client=$link->query("SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, bh_cliente.TX_cliente_direccion, bh_facturaf.TX_facturaf_numero FROM (bh_cliente INNER JOIN bh_facturaf ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id) WHERE AI_facturaf_id = '$facturaf_id'");
$rs_client=$qry_client->fetch_array();

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE AI_facturaf_id = '$facturaf_id'";
$qry_facturaf=$link->query($txt_facturaf) or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();

$qry_facturaventa=$link->query("SELECT bh_facturaventa.TX_facturaventa_observacion FROM (bh_facturaventa INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id) WHERE AI_facturaf_id = '$facturaf_id' GROUP BY AI_facturaf_id");
$rs_facturaventa=$qry_facturaventa->fetch_array();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_client['TX_cliente_nombre']." - ".$rs_client['TX_facturaf_numero']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
// setTimeout("self.close()", 10000);
</script>

<body style="font-family:Arial" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto">
	<tbody>
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
			<td colspan="2" style="text-align:left"></td>
		 	<td valign="top" colspan="6" style="text-align:center">
				<img width="200px" height="75px" src="attached/image/logo_factura.png">
				<br />
				<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
				<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
				<font style="font-size:10px"><?php echo "TLF. ".$raw_opcion['TELEFONO']." WHATSAPP: ".$raw_opcion['FAX']."<br />"; ?></font>
				<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
		  </td>
	    <td valign="top" colspan="2" class="optmayuscula">
				<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo $fecha; ?>
	    </td>
		</tr>
		<tr style="height:21px" align="center">
			<td valign="top" colspan="10"><h4>FACTURA</h4></td>
		</tr>
		<tr style="height:184px">
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
					</tbody>
		    </table>
		    <table id="tbl_facturaf" class="table">
					<tbody style="border:solid;">
			    	<tr>
		        	<td valign="top">
            		<strong>Vendedor(a): </strong><?php echo strtoupper($rs_facturaf['TX_user_seudonimo']); ?>
            	</td>
        			<td valign="top">
	            	<strong>Fecha: </strong><?php echo $rs_facturaf['TX_facturaf_fecha']; ?>
	            </td>
	            <td valign="top">
		            <strong>Hora: </strong><?php echo $rs_facturaf['TX_facturaf_hora']; ?>
	            </td>
	            <td></td>
		        </tr>
		        <tr>
	            <td valign="top">
		            <strong>Factura Nº: </strong><?php echo $rs_facturaf['TX_facturaf_numero']; ?>
	            </td>
	            <td valign="top">
		            <strong>Ticket Nº: </strong><?php echo $rs_facturaf['TX_facturaf_ticket']; ?>
	            </td>
	            <td valign="top">
		            <strong>Total: </strong><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?>
	            </td>
	            <td valign="top">
		            <strong>Saldo: </strong><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?>
	            </td>
			    	</tr>
		        <tr>
		        	<td colspan="4">
		            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px; ">
									<tbody>
										<tr>
				            	<td>
				            		<strong>Base No Imponible: </strong><?php echo number_format($rs_facturaf['TX_facturaf_subtotalni'],2); ?>
				            	</td>
			        				<td>
						            <strong>Base Imponible: </strong><?php echo number_format($rs_facturaf['TX_facturaf_subtotalci'],2); ?>
					            </td>
						        	<td>
						            <strong>Descuento: </strong><?php echo number_format($rs_facturaf['TX_facturaf_descuento'],4); ?>
					            </td>
						        	<td>
						            <strong>Impuesto: </strong><?php echo number_format($rs_facturaf['TX_facturaf_impuesto'],4); ?>
					            </td>
						        	<td>
						            <strong>Cambio: </strong><?php echo number_format($rs_facturaf['TX_facturaf_cambio'],4); ?>
					            </td>
				            </tr>
									</tbody>
		            </table>
		          </td>
		        </tr>
		      </tbody>
				</table>
		  </td>
		</tr>
		<tr style="height:45px">
			<td valign="top" colspan="10">
		    <table id="tbl_observation" class="table table-print">
		      <tbody style="border:solid;">
		        <tr>
		        	<td valign="top" style="width:100%">
		            <strong>Observaci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa[0]); ?>
		          </td>
		        </tr>
		      </tbody>
		    </table>
		  </td>
		</tr>
<?php
		$txt_datoventa="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion,
		bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.AI_datoventa_id
		FROM (((bh_datoventa
		INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
		INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
		INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
		WHERE bh_facturaventa.facturaventa_AI_facturaf_id = '$facturaf_id'";
		$qry_datoventa=$link->query($txt_datoventa);
		$rs_datoventa=$qry_datoventa->fetch_array();
?>
		<tr style="height:588px;">
			<td valign="top" colspan="10" style="padding-top:2px;">
		    <table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
		    	<thead style="border:solid">
		    		<tr>
		      		<th style="width:20%; text-align:center; border:solid 1px #000; border-bottom-left-radius:3px;border-top-left-radius:3px;">
								<strong>Codigo </strong>
		        	</th>
			        <th style="width:50%; text-align:center; border:solid 1px #000;">
				        <strong>Detalle</strong>
			        </th>
			        <th style="width:10%; text-align:center; border:solid 1px #000;">
				        <strong>Cant. </strong>
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
						$subtotal=0;	$totalitbm=0;	$totaldescuento=0;
						$index = 1;
						$pager = 0;
						do{
							$pager++;
							if($index === 1){
								if($pager === 14){
									$pager = 0;
									$index++;
?>								<tr style="height:52.15px">
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr style="height:588px;">
						<td valign="top" colspan="10" style="padding-top:2px;">
					    <table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
						    <thead style="border:solid">
						    	<tr>
					        	<th style="width:20%; text-align:center; border:solid 1px #000; border-bottom-left-radius:3px;border-top-left-radius:3px;">
											<strong>Codigo </strong>
				            </th>
				            <th style="width:50%; text-align:center; border:solid 1px #000;">
					            <strong>Detalle</strong>
				            </th>
				            <th style="width:10%; text-align:center; border:solid 1px #000;">
					            <strong>Cant. </strong>
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
								if($pager === 20){
									$pager = 0;
									$index++;
?>								<tr style="height:52.15px">
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr style="height:588px;">
						<td valign="top" colspan="10" style="padding-top:2px;">
							<table  id="tbl_datoventa" class="table table-print table-bordered table-striped">
								<thead style="border:solid">
									<tr>
										<th style="width:20%; text-align:center; border:solid 1px #000;">
											<strong>Codigo </strong>
										</th>
										<th style="width:50%; text-align:center; border:solid 1px #000;">
											<strong>Detalle</strong>
										</th>
										<th style="width:10%; text-align:center; border:solid 1px #000;">
											<strong>Cant. </strong>
										</th>
										<th style="width:10%; text-align:center; border:solid 1px #000;">
											<strong>Precio</strong>
										</th>
										<th style="width:10%; text-align:center; border:solid 1px #000;">
											<strong>Total. </strong>
										</th>
									</tr>
								</thead>
								<tbody>
<?php 					}
							}
?>
				    	<tr style="height:41px;">
			        	<td style="width:20%; text-align:center;"><?php echo $rs_datoventa['TX_producto_codigo']; ?></td>
			          <td style="width:50%; text-align:center;"><?php
									$descripcion = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
									echo substr($descripcion,0,96);
								?></td>
		            <td style="width:10%; text-align:center;"><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></td>
            		<td style="width:10%; text-align:center;"><?php
									$descuento=($rs_datoventa['TX_datoventa_precio']*$rs_datoventa['TX_datoventa_descuento'])/100;
									$precio_descuento=$rs_datoventa['TX_datoventa_precio']-$descuento;
									$impuesto=($precio_descuento*$rs_datoventa['TX_datoventa_impuesto'])/100;
									$precio_descuento_impuesto=$precio_descuento+$impuesto;
									echo number_format($precio_descuento_impuesto,2);	?>
		            </td>
		            <td style="width:10%; text-align:center;"><?php
									$total4product = $rs_datoventa['TX_datoventa_cantidad'] * $precio_descuento_impuesto;
									echo number_format($total4product,2);
									$totalitbm+=$rs_datoventa['TX_datoventa_cantidad'] * $impuesto;
									$totaldescuento+=$rs_datoventa['TX_datoventa_cantidad'] * $descuento;
									$subtotal+=$rs_datoventa['TX_datoventa_cantidad'] * $precio_descuento;
?>	            </td>
							</tr>
<?php
						}while($rs_datoventa=$qry_datoventa->fetch_array()); ?>
						 	</tbody>
						  <tfoot>
								<tr>
						    	<td colspan="5">
						        <table class="table table-print table-bordered">
							        <tbody>
								        <tr>
								        	<td><?php echo "<strong>Subtotal:</strong> B/ ".number_format($subtotal,2); ?></td>
            							<td><?php echo "<strong>ITBM:</strong> B/ ".number_format($totalitbm,2); ?></td>
<?php 										if($totaldescuento > 0){ echo "<td><strong>Descuento:</strong> B/ ".number_format($totaldescuento,2)."</td>"; } ?>
            							<td>
<?php 											$total=$subtotal+$totalitbm; echo "<strong>Total:</strong> B/ ".number_format($total,2);	?>
													</td>
								        </tr>
							        </tbody>
						        </table>
					        </td>
						    </tr>
					    </tfoot>
						</table>
	<?php			$qry_datopago=$link->query("SELECT bh_metododepago.TX_metododepago_value, bh_datopago.TX_datopago_monto, bh_datopago.TX_datopago_numero FROM (bh_datopago INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datopago.datopago_AI_metododepago_id) WHERE bh_datopago.datopago_AI_facturaf_id = '$facturaf_id'");
						$qry_datodebito=$link->query("SELECT bh_metododepago.TX_metododepago_value, bh_datodebito.TX_datodebito_monto, bh_datodebito.TX_datodebito_numero
						FROM (((bh_datodebito
						INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datodebito.datodebito_AI_metododepago_id)
						INNER JOIN rel_facturaf_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = datodebito_AI_notadebito_id)
						INNER JOIN bh_facturaf ON rel_facturaf_notadebito.rel_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
						WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'");
	?>
						<table id="tbl_payment" class="table table-print table-bordered table-condensed tbl-padding-0">
							<caption><strong>Pagos Asociados</strong></caption>
							<thead>
								<tr>
									<th></th>
									<th>M&eacute;todo</th>
									<th>Monto</th>
									<th>Numero</th>
								</tr>
							</thead>
							<tbody>
<?php 					while($rs_datopago=$qry_datopago->fetch_array()){						?>
									<tr>
										<td>Pago</td>
										<td><?php echo $rs_datopago['TX_metododepago_value']; ?></td>
										<td><?php echo number_format($rs_datopago['TX_datopago_monto'],2); ?></td>
										<td><?php echo $rs_datopago['TX_datopago_numero']; ?></td>
									</tr>
<?php 					}
								while($rs_datodebito=$qry_datodebito->fetch_array()){ ?>
									<tr>
										<td>Abono a Cr&eacute;dito </td>
										<td><?php echo $rs_datodebito['TX_metododepago_value']; ?></td>
										<td><?php echo number_format($rs_datodebito['TX_datodebito_monto'],2); ?></td>
										<td><?php echo $rs_datodebito['TX_datodebito_numero']; ?></td>
									</tr>
<?php 					}		?>
							</tbody>
						</table>
			    </td>
				</tr>
			</tbody>
		</table>
</body>
</html>
