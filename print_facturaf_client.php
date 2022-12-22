<?php
// CANTIDAD DE MOVIMIENTOS
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';
$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array(MYSQLI_ASSOC)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$client_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

$qry_client=$link->query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client=$qry_client->fetch_array(MYSQLI_ASSOC);
 
$date_i=date('Y-m-d',strtotime($date_i));
$date_f=date('Y-m-d',strtotime($date_f));

$prep_payment = $link->prepare("SELECT datopago_AI_metododepago_id, TX_datopago_monto FROM bh_datopago WHERE datopago_AI_facturaf_id = ?")or die($link->error);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha >= '$date_i' AND TX_facturaf_fecha <= '$date_f' ORDER BY AI_facturaf_id";
$qry_facturaf=$link->query($txt_facturaf) or die($link->error);
$raw_facturaf = array();
$prep_payment->bind_param('i', $facturaf_id);
$total_credit = 0; $cant_doc = 0;
$facturaf_id = '';
while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
	if ($facturaf_id != $rs_facturaf['AI_facturaf_id']) {
		$cant_doc++;
		$index = date('Ymd',strtotime($rs_facturaf['TX_facturaf_fecha'])).date('H:i',strtotime($rs_facturaf['TX_facturaf_hora'])).$rs_facturaf['AI_facturaf_id'];
		$raw_facturaf[$index] = $rs_facturaf;
		$raw_facturaf[$index]['imprimir'] = ($rs_facturaf['TX_facturaf_deficit'] > 0.00) ? 'print' : 'no' ;
		if ($_GET['d'] === 'todas') {
			$raw_facturaf[$index]['imprimir'] = 'print';
		}
		$raw_facturaf[$index]['tipo'] = 'FACTURA';
		$facturaf_id = $rs_facturaf['AI_facturaf_id'];
		$prep_payment->execute(); $qry_payment = $prep_payment->get_result();
		$amount = 0;
		while($rs_payment = $qry_payment->fetch_array()){
			if($rs_payment['datopago_AI_metododepago_id'] === 5 || $rs_payment['datopago_AI_metododepago_id'] === 8){
				$amount += $rs_payment['TX_datopago_monto'];
			}
		}
		$total_credit += $amount;
		$raw_facturaf[$index]['cargo'] = $amount;
	}

}
$qry_debito = $link->query("SELECT AI_notadebito_id, TX_notadebito_total, TX_notadebito_fecha, TX_notadebito_hora, TX_notadebito_numero FROM bh_notadebito WHERE notadebito_AI_cliente_id = '$client_id' AND TX_notadebito_fecha >= '$date_i' AND TX_notadebito_fecha <= '$date_f' AND TX_notadebito_status != 1")or die($link->error);
$total_payed = 0;
while ($rs_notadebito = $qry_debito->fetch_array(MYSQLI_ASSOC)) {
	$index = date('Ymd',strtotime($rs_notadebito['TX_notadebito_fecha'])).date('H:i',strtotime($rs_notadebito['TX_notadebito_hora'])).$rs_notadebito['AI_notadebito_id'];
	$amount = $rs_notadebito['TX_notadebito_total']*(-1);
	$raw_facturaf[$index]['TX_facturaf_fecha'] = $rs_notadebito['TX_notadebito_fecha'];
	$raw_facturaf[$index]['TX_facturaf_hora'] = $rs_notadebito['TX_notadebito_hora'];
	$raw_facturaf[$index]['TX_facturaf_numero'] = $rs_notadebito['TX_notadebito_numero'];
	$raw_facturaf[$index]['TX_facturaf_total'] = 0.00;
	$raw_facturaf[$index]['TX_facturaf_deficit'] = 0.00;
	$raw_facturaf[$index]['tipo'] = 'DEBITO';
	$raw_facturaf[$index]['cargo'] = $amount;
	$raw_facturaf[$index]['imprimir'] = 'print';
	if ($_GET['d'] != 'todas') {
		$raw_facturaf[$index]['imprimir'] = 'no';
	}
	$total_payed += $amount;
}
// ###########################				PREVIOS				###################################

$txt_previusff="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha < '$date_i' ORDER BY AI_facturaf_id";
$qry_previusff=$link->query($txt_previusff) or die($link->error);
$saldo_retenido = 0;
$total = 0;
while ($rs_previusff = $qry_previusff->fetch_array(MYSQLI_ASSOC)) {
	// $cant_doc++;
	// $total+=round($rs_previusff['TX_facturaf_total'],2);
	$facturaf_id = $rs_previusff['AI_facturaf_id'];
	$prep_payment->execute(); $qry_payment = $prep_payment->get_result();
	$amount = 0;
	while($rs_payment = $qry_payment->fetch_array(MYSQLI_ASSOC)){
		if($rs_payment['datopago_AI_metododepago_id'] === 5 || $rs_payment['datopago_AI_metododepago_id'] === 8){
			$amount += $rs_payment['TX_datopago_monto'];
		}
	}
	// $total_credit += $amount;
	$saldo_retenido += $amount;
}
$qry_previusdebito = $link->query("SELECT AI_notadebito_id, TX_notadebito_total, TX_notadebito_fecha, TX_notadebito_hora, TX_notadebito_numero FROM bh_notadebito WHERE notadebito_AI_cliente_id = '$client_id' AND TX_notadebito_fecha < '$date_i' AND TX_notadebito_status != 1 AND TX_notadebito_status != 1")or die($link->error);
while ($rs_previusdebito = $qry_previusdebito->fetch_array(MYSQLI_ASSOC)) {
	$amount = $rs_previusdebito['TX_notadebito_total'];
	$saldo_retenido -= $amount;
	// $total_payed += $amount;
}

ksort($raw_facturaf);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>

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
	<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 110px;">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
			<h3 style="margin:0;">Historial</h3>
			Movimientos del <?php echo date('d-m-Y', strtotime($date_i)); ?> al <?php echo date('d-m-Y', strtotime($date_f)); ?>.
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
			<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?></div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>Telefono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?></div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_client['TX_cliente_direccion']); ?>
			</div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
	<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
			Facturas Procesadas
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>FECHA</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>DOCUMENTO</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>DESCRIPCI&Oacute;N</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>TOTAL</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>DEFICIT</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>CARGO</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></div>
		</div><?php
		// $total=0; 
		$saldo=$saldo_retenido; $deficit = 0;
		foreach ($raw_facturaf as $key => $rs_facturaf) {
			$total+=round($rs_facturaf['TX_facturaf_total'],2);
			$saldo+=round($rs_facturaf['cargo'],2);
			$deficit += round($rs_facturaf['TX_facturaf_deficit'],2);
			if ($rs_facturaf['imprimir'] === 'print') { ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo date('d-m-Y', strtotime($rs_facturaf['TX_facturaf_fecha']))."<br />".$rs_facturaf['TX_facturaf_hora']; ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_facturaf['tipo']; ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_facturaf['TX_facturaf_numero']; ?></div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format($rs_facturaf['cargo'],2); ?></div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">B/.<?php echo ($_GET['d'] === 'todas') ? number_format($saldo,2) : number_format($deficit,2) ; ?></div>
				</div>
			<?php			
			} 
		} ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>DOCUMENTOS:</strong><br /><?php echo $cant_doc; ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center br_1"><strong>TOTAL FACTURADO:</strong><br /><?php echo "B/ ".number_format($total,2); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>SALDO ANTERIOR</strong>:</strong><br />B/.<?php echo number_format($saldo_retenido,2); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>TOTAL CR&Eacute;DITO:</strong><br /><?php echo "B/ ".number_format($total_credit,2); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>TOTAL ABONADO:</strong><br /><?php echo "B/ ".number_format($total_payed,2); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>SALDO:</strong><br /><?php echo "B/ ".number_format($saldo,2); ?></div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
</div>
</body>
</html>
