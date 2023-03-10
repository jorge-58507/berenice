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

$txt_datoventa="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_medida, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion,
bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.AI_datoventa_id, bh_datoventa.datoventa_AI_producto_id
FROM (((bh_datoventa
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaventa.facturaventa_AI_facturaf_id = '$facturaf_id'";
$qry_datoventa=$link->query($txt_datoventa);

$qry_medida = $link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while ($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$prep_datocompra = $link->prepare("SELECT bh_datocompra.TX_datocompra_precio, bh_datocompra.TX_datocompra_descuento, bh_datocompra.TX_datocompra_medida
FROM bh_datocompra 
WHERE datocompra_AI_producto_id = ? ORDER BY AI_datocompra_id DESC LIMIT 1")or die($link->error);

$prep_productomedida = $link->prepare("SELECT rel_producto_medida.TX_rel_productomedida_cantidad 
FROM rel_producto_medida 
WHERE rel_producto_medida.productomedida_AI_producto_id = ? AND rel_producto_medida.productomedida_AI_medida_id = ?")or die($link->error);
$prep_productomedida->bind_param('ii',$product_id,$medida);

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
</script>

<body style="font-family:Arial" >
	<div style="height:975px; width:720px; font-size:12px; margin:0 auto">
		<div id="print_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height: 140px; padding-top: 10px;">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 al_center">
				<img width="200px" height="75px" src="attached/image/logo_factura.png">
				<br />
				<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
				<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
				<font style="font-size:10px"><?php echo "TLF. ".$raw_opcion['TELEFONO']." WHATSAPP: ".$raw_opcion['FAX']."<br />"; ?></font>
				<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php
				$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
				$fecha = $dias[date('N', strtotime(date('d-m-Y')))+1];
				echo $fecha."&nbsp;-&nbsp;".$date=date('d-m-Y');
	?>	</div>
		</div>
		<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 240px;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
				<h4>INFORME DE FACTURA</h4>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 60px; border: solid 2px #000;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?></div>
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Tel&eacute;fono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>Direcci&oacute;n: </strong><?php echo strtoupper(substr($rs_client['TX_cliente_direccion'],0,70)); ?></div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding mt-5" style="height: 90px; border: solid 2px #000;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding_right"><strong>Vendedor: </strong><?php echo strtoupper($rs_facturaf['TX_user_seudonimo']); ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>Fecha: </strong><?php echo $rs_facturaf['TX_facturaf_fecha']; ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding_left"><strong>Hora: </strong><?php echo $rs_facturaf['TX_facturaf_hora']; ?></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding_right"><strong>Factura: </strong><?php echo $rs_facturaf['TX_facturaf_numero']; ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>Ticket: </strong><?php echo $rs_facturaf['TX_facturaf_ticket']; ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>Total: </strong><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding_left"><strong>Saldo: </strong><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding_right"><strong>Base No Imponible: </strong><?php echo number_format($rs_facturaf['TX_facturaf_subtotalni'],2); ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>Base Imponible: </strong><?php echo number_format($rs_facturaf['TX_facturaf_subtotalci'],2); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding"><strong>Descuento: </strong><?php echo number_format($rs_facturaf['TX_facturaf_descuento'],4); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding"><strong>Impuesto: </strong><?php echo number_format($rs_facturaf['TX_facturaf_impuesto'],4); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding_left"><strong>Cambio: </strong><?php echo number_format($rs_facturaf['TX_facturaf_cambio'],4); ?></div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding mt-5" style="height: 30px;border: solid 2px #000;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>Observaci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa[0]); ?></div>
			</div>
		</div>
	<!-- #####################         BODY          #################   -->
		<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
				Producto(s) Relacionados
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>CODIGO</strong></div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>DETALLE</strong></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>MEDIDA</strong></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>CANT</strong></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>VENTA</strong></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>COSTO</strong></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>GANANCIA BRUTA</strong></div>
			</div>
			<?php
			$subtotal=0;	$totalitbm=0;	$totaldescuento=0; $sum_surplus = 0;
			// $index = 1; // $pager = 0;
			while($rs_datoventa=$qry_datoventa->fetch_array(MYSQLI_ASSOC)){?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo substr($rs_datoventa['TX_producto_codigo'],-6); ?></div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><?php
						$descripcion = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
						echo substr($descripcion,0,96);
				?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">
<?php				echo $raw_medida[$rs_datoventa['TX_datoventa_medida']];	?>						
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php
						$product_id = $rs_datoventa['datoventa_AI_producto_id'];					
						$medida = $rs_datoventa['TX_datoventa_medida'];
						$prep_productomedida->execute(); $qry_productomedida = $prep_productomedida->get_result(); 
						$rs_productomedida = $qry_productomedida->fetch_array(MYSQLI_ASSOC);
						$sale_productomedida = $rs_productomedida['TX_rel_productomedida_cantidad'];

						$descuento=($rs_datoventa['TX_datoventa_precio']*$rs_datoventa['TX_datoventa_descuento'])/100;
						$precio_descuento=$rs_datoventa['TX_datoventa_precio']-$descuento;
						$itbm = ($rs_datoventa['TX_datoventa_precio']*$rs_datoventa['TX_datoventa_impuesto'])/100;
						$subtotal += $precio_descuento*$rs_datoventa['TX_datoventa_cantidad'];
						$totalitbm += ($itbm)*$rs_datoventa['TX_datoventa_cantidad'];
						echo number_format($precio_descuento,2);	
					?></div>
<!-- // COSTO -->
<?php  		$prep_datocompra->bind_param('i',$rs_datoventa['datoventa_AI_producto_id']); 
					$prep_datocompra->execute(); 
					$qry_cost = $prep_datocompra->get_result(); 
					$rs_datocompra = $qry_cost->fetch_array(MYSQLI_ASSOC);	
					?>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php
						$medida = $rs_datocompra['TX_datocompra_medida'];
						$prep_productomedida->execute(); $qry_productomedida = $prep_productomedida->get_result(); 
						$rs_productomedida = $qry_productomedida->fetch_array(MYSQLI_ASSOC);
						if ($qry_productomedida->num_rows > 0 ) {
							$cost_productomedida = $rs_productomedida['TX_rel_productomedida_cantidad'];
							$rel_cost_sale = $sale_productomedida/$cost_productomedida;
							$cost_discount = ($rs_datocompra['TX_datocompra_precio']*$rs_datocompra['TX_datocompra_descuento'])/100;
							$cost_per_product = $rs_datocompra['TX_datocompra_precio']-$cost_discount;

							$gross_surplus =  $precio_descuento-($cost_per_product*$rel_cost_sale);
							$sum_surplus += $rs_datoventa['TX_datoventa_cantidad']*$gross_surplus;
							$surplus_message = number_format($rs_datoventa['TX_datoventa_cantidad']*$gross_surplus,2)." (".number_format($gross_surplus,2).")";
							
							echo number_format($cost_per_product*$rel_cost_sale,2);
						}else{
							echo "Sin Informaci&oacute;n";
							$cost_per_product = 0;
							$rel_cost_sale = 0;
							$surplus_message = 'Sin Informaci&oacute;n';
						}
				?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php					
						echo $surplus_message;
				?></div>
				</div>
<?php	} ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_footer minimal">

				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><?php $total=$subtotal+$totalitbm; echo "<strong>Sub-Total:</strong> B/ ".number_format($subtotal,2)."&nbsp;<strong>Imp:</strong> B/ ".number_format($totalitbm,2)."&nbsp;<strong>Total:</strong> B/ ".number_format($total,2);	?></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo "<strong>GB:</strong> B/ ".number_format($sum_surplus,2); ?></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo "<strong>%GB:</strong> ".number_format(($sum_surplus*100)/$subtotal,2); ?>%</div>
<?php 	if($totaldescuento > 0){ echo "<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'><strong>Descuento:</strong> B/ ".number_format($totaldescuento,2)."</div>"; } ?>

			</div>
			<?php			$qry_datopago=$link->query("SELECT bh_metododepago.TX_metododepago_value, bh_datopago.TX_datopago_monto, bh_datopago.TX_datopago_numero, bh_datopago.TX_datopago_fecha FROM (bh_datopago INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datopago.datopago_AI_metododepago_id) WHERE bh_datopago.datopago_AI_facturaf_id = '$facturaf_id'");
								$qry_datodebito=$link->query("SELECT bh_metododepago.TX_metododepago_value, bh_datodebito.TX_datodebito_monto, bh_datodebito.TX_datodebito_numero, bh_datodebito.TX_datodebito_fecha, bh_notadebito.TX_notadebito_numero, rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe
								FROM ((((bh_datodebito
								INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datodebito.datodebito_AI_metododepago_id)
								INNER JOIN bh_notadebito ON bh_notadebito.AI_notadebito_id = bh_datodebito.datodebito_AI_notadebito_id)
								INNER JOIN rel_facturaf_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = datodebito_AI_notadebito_id)
								INNER JOIN bh_facturaf ON rel_facturaf_notadebito.rel_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
								WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id' AND bh_notadebito.TX_notadebito_status = '0'");
			?>			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_footer">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
									Pagos Asociados
								</div>

								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header minimal">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Fecha</strong></div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>M&eacute;todo</strong></div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Monto</strong></div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>Numero</strong></div>
								</div>

<?php 					while($rs_datopago=$qry_datopago->fetch_array()){						?>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body minimal">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Pago</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo date('d-m-Y', strtotime($rs_datopago['TX_datopago_fecha'])); ?></div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_datopago['TX_metododepago_value']; ?></div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">B/ <?php echo number_format($rs_datopago['TX_datopago_monto'],2); ?></div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $rs_datopago['TX_datopago_numero']; ?></div>
									</div>
<?php 					}
								while($rs_datodebito=$qry_datodebito->fetch_array()){ ?>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body minimal">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Abono a Cr&eacute;dito (<?php echo substr($rs_datodebito['TX_notadebito_numero'],-6); ?>)</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo date('d-m-Y', strtotime($rs_datodebito['TX_datodebito_fecha'])); ?></div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_datodebito['TX_metododepago_value']; ?></div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">B/ <?php echo number_format($rs_datodebito['TX_datodebito_monto'],2)." (".number_format($rs_datodebito['TX_rel_facturafnotadebito_importe'],2).")"; ?></div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $rs_datodebito['TX_datodebito_numero']; ?></div>
									</div>
<?php						} ?>



							</div>
						</div>
						<!-- #####################         BODY          #################   -->
					</div>
<!-- ###### FIN POR DIV ######-->
</body>
</html>
