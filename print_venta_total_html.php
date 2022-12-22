<?php
set_time_limit(180);
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if (!empty($date_i) && !empty($date_f)) {
	$line_date = " TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
	$line_date_nc = " TX_notadecredito_fecha >= '$date_i' AND TX_notadecredito_fecha <= '$date_f'";
}

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_numero,  bh_facturaf.TX_facturaf_total, bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
WHERE ".$line_date." ORDER BY TX_facturaf_fecha DESC, AI_facturaf_id DESC";

$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$nr_facturaf=$qry_facturaf->num_rows;

$txt_nc = "SELECT TX_notadecredito_monto, TX_notadecredito_impuesto FROM bh_notadecredito WHERE ".$line_date_nc;
$qry_nc = $link->query($txt_nc)or die($link->error);
$ttl_nc_impuesto = 0; $ttl_nc = 0;
while ($rs_nc = $qry_nc->fetch_array()) {
	$ttl_nc_impuesto += $rs_nc['TX_notadecredito_impuesto'];
	$ttl_nc += $rs_nc['TX_notadecredito_monto']+$rs_nc['TX_notadecredito_impuesto'];
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Reporte de Totales de Ventas</title>
	<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="attached/js/jquery.js"></script>
	<script type="text/javascript" src="attached/js/bootstrap.js"></script>
	<script type="text/javascript" src="attached/js/general_funct.js"></script>
</head>
<!-- ################      INICIO DEL BODY      ############# -->
<body style="font-family:Arial" onload="window.print();" >
	<div style="height:975px; width:720px; font-size:12px; margin:0 auto">

		<div id="print_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height: 140px; padding-top: 10px;">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 al_center">
				<img width="200px" height="75px" src="attached/image/logo_factura.png" ondblclick="window.location.href='print_sale_html_materiales.php?a=<?php echo $facturaventa_id; ?>'">
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
?>		</div>
		</div>
		<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 80px;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
				<h3>Total de Ventas</h3>
				<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
				<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>

			</div>
		</div><?php
		$qry_datopago = $link->prepare("SELECT bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id FROM bh_datopago WHERE bh_datopago.datopago_AI_facturaf_id = ?")or die($link->error);
		$qry_datoventa = $link->prepare("SELECT bh_datoventa.TX_datoventa_precio,
			bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_facturaf.AI_facturaf_id
		FROM ((bh_datoventa
		INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
		INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
		WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
		$total=0;
		$total_impuesto=0;
		$total_base_i=0;
		$total_base_ni=0;
		$raw_pago=array();
		$raw_metododepago = array();
		$qry_metododepago = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
		while ($rs_metododepago = $qry_metododepago->fetch_array()) {
			$raw_pago[$rs_metododepago['AI_metododepago_id']] = 0;
			$raw_metododepago[$rs_metododepago['AI_metododepago_id']] = $rs_metododepago['TX_metododepago_value'];
		}

		while($rs_facturaf=$qry_facturaf->fetch_array()){
			$qry_datoventa->bind_param("i", $rs_facturaf['AI_facturaf_id']);
			$qry_datoventa->execute()or die($link->error);
			$result=$qry_datoventa->get_result();
			$total4facturaf=0;
			$base_ni4facturaf=0;
			$base_i4facturaf=0;
			$impuesto4facturaf=0;
			while ($rs_datoventa=$result->fetch_array()) {
				$base4product=$rs_datoventa['TX_datoventa_cantidad']*$rs_datoventa['TX_datoventa_precio'];
				$descuento=($rs_datoventa['TX_datoventa_descuento']*$base4product)/100;
				$base_descuento=$base4product-$descuento;
				$impuesto=($rs_datoventa['TX_datoventa_impuesto']*$base_descuento)/100;
				$impuesto4facturaf += $impuesto;
				if ($impuesto == 0) {	$base_ni4facturaf += $base_descuento;	}else{	$base_i4facturaf += $base_descuento;	}
				$precio4product=$base_descuento+$impuesto;
				$total4facturaf += $precio4product;
			}
			$total_impuesto+=$impuesto4facturaf;
			$total_base_i+=$base_i4facturaf;
			$total_base_ni+=$base_ni4facturaf;
			$total+=$total4facturaf;
			$qry_datopago->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_datopago->execute(); $result = $qry_datopago->get_result();
			while ($rs_datopago = $result->fetch_array()) {
				$raw_pago[$rs_datopago['datopago_AI_metododepago_id']] += $rs_datopago['TX_datopago_monto'];
			}
		}

	?>


	<!-- #####################         BODY          #################   -->
		<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<div id="container_print_total" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<h4>TOTALES DE VENTA</h4>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Base Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_i,2); ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Base No Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_ni,2); ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Impuesto</strong><br /><strong>B/</strong> <?php echo number_format($total_impuesto,2); ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Total</strong><br /><strong>B/</strong> <?php echo number_format($total,2); ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<h4>TOTALES NOTA DE CREDITO</h4>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Monto</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc,2); ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<strong>Devolucion de Impuestos</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc_impuesto,2); ?>
				</div>
			</div>
			<div id="container_print_methods" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<?php foreach ($raw_metododepago as $key => $metododepago_value): ?>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong><?php echo $metododepago_value; ?></strong><br />
						B/ <?php echo $raw_pago[$key]; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<!-- #####################         BODY          #################   -->
	</div>
</body>
</html>


<?php
/*
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
<tr style="height:132px" align="right">
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
<tr style="height:64px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
      <tr>
				<td valign="top" style="text-align:center;">
					<p><h4>FACTURAS DE VENTAS</h4></p>
					<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
					<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr id="content_print" style="height:773px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
		<thead style="border: solid">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
			<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
		</tr>
		</thead>
		<tbody>
<?php
	$qry_datoventa = $link->prepare("SELECT bh_datoventa.TX_datoventa_precio,
		bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
	FROM ((bh_datoventa
	INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
	$total=0;
	$total_impuesto=0;
	$total_base_i=0;
	$total_base_ni=0;
	$index = 1;
	$pager = 1;
	while($rs_facturaf=$qry_facturaf->fetch_array()){
	$pager++;
	if($index === 1){
	if($pager === 28){
		$pager = 0;
		$index++;
	?>
		</tbody>
		</table>
	</td>
</tr>
<tr style="height:773px;">
<td valign="top" colspan="10" style="padding-top:2px;">
		<table class="table table-bordered table-condensed table-striped table-print">
			<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
			<thead style="border: solid">
			<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
				<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
			</tr>
			</thead>
			<tbody>
	<?php
	}
}else{
	if($pager === 33){
		$pager = 0;
		$index++;
	?>
		</tbody>
		</table>
	</td>
</tr>
<tr style="height:773px;">
<td valign="top" colspan="10" style="padding-top:2px;">
		<table class="table table-bordered table-condensed table-striped table-print">
			<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
			<thead style="border: solid">
			<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
				<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
			</tr>
			</thead>
			<tbody>
	<?php
	}
}
		$qry_datoventa->bind_param("i", $rs_facturaf['AI_facturaf_id']);
		$qry_datoventa->execute()or die($link->error);
		$result=$qry_datoventa->get_result();
		$total4facturaf=0;
		$base_ni4facturaf=0;
		$base_i4facturaf=0;
		$impuesto4facturaf=0;
		while ($rs_datoventa=$result->fetch_array()) {
			$base4product=$rs_datoventa['TX_datoventa_cantidad']*$rs_datoventa['TX_datoventa_precio'];
			$descuento=($rs_datoventa['TX_datoventa_descuento']*$base4product)/100;
			$base_descuento=$base4product-$descuento;
			$impuesto=($rs_datoventa['TX_datoventa_impuesto']*$base_descuento)/100;
			$impuesto4facturaf += $impuesto;
			if ($impuesto == 0) {	$base_ni4facturaf += $base_descuento;	}else{	$base_i4facturaf += $base_descuento;	}
			$precio4product=$base_descuento+$impuesto;
			$total4facturaf += $precio4product;
		}
		$total_impuesto+=$impuesto4facturaf;
		$total_base_i+=$base_i4facturaf;
		$total_base_ni+=$base_ni4facturaf;
		$total+=$total4facturaf;

	?>
		<tr>
			<td><?php echo $rs_facturaf['TX_facturaf_fecha']; ?></td>
			<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
			<td><?php echo substr($rs_facturaf['TX_cliente_nombre'], 0, 30); ?></td>
			<td><?php echo number_format($base_ni4facturaf,4); ?></td>
			<td><?php echo number_format($base_i4facturaf,4); ?></td>
			<td><?php echo number_format($impuesto4facturaf,4); ?></td>
			<td><?php echo number_format($total4facturaf,4); ?></td>
		</tr>
<?php } ?>
		<tr style="border:solid;">
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo number_format($total_base_ni,2) ?></td>
			<td><?php echo number_format($total_base_i,2) ?></td>
			<td><?php echo number_format($total_impuesto,2); ?></td>
			<td><strong>B/</strong> <?php echo number_format($total,2); ?></td>
		</tr>
	</tbody>
		</table>


  </td>
</tr>
</table>
	<table class="table-bordered table-striped"  style="margin:0 auto; width:720px; page-break-before:always; text-align:center; font-size:30px;">
		<tbody>
			<tr>
				<td><strong>TOTALES DE VENTAS</strong></td>
			</tr>
			<tr>
				<td><strong>Base No Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_ni,2) ?></td>
			</tr>
			<tr>
				<td><strong>Base Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_i,2) ?></td>
			</tr>
			<tr>
				<td><strong>Impuesto</strong><br /><strong>B/</strong> <?php echo number_format($total_impuesto,2); ?></td>
			</tr>
			<tr>
				<td><strong>Total</strong><br /><strong>B/</strong> <?php echo number_format($total,2); ?></td>
			</tr>
			<tr>
				<td><strong>TOTALES DE NOTAS DE CREDITO</strong></td>
			</tr>
			<tr>
				<td><strong>Monto</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc,2); ?></td>
			</tr>
			<tr>
				<td><strong>Devolucion de Impuestos</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc_impuesto,2); ?></td>
			</tr>
			<tr>
				<td valign="center" style="text-align:center;">
					<p><h4>FACTURAS DE VENTAS</h4></p>
					<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
					<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>
        </td>
			</tr>
		</tbody>
	</table>*/
