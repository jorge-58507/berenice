<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$arqueo_id=$_GET['a'];

$qry_metododepago = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
$raw_metododepago=array();
while ($rs_metododepago=$qry_metododepago->fetch_array()) {
	$raw_metododepago[$rs_metododepago['AI_metododepago_id']] = $rs_metododepago['TX_metododepago_value'];
}

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$qry_arqueo_facturaf = $link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_nombre
	FROM ((((bh_facturaf
		INNER JOIN bh_arqueo ON bh_arqueo.AI_arqueo_id = bh_facturaf.facturaf_AI_arqueo_id)
		INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
		INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
		WHERE bh_arqueo.AI_arqueo_id = '$arqueo_id' ORDER BY TX_facturaf_fecha DESC, AI_facturaf_id DESC")or die($link->error);

$prep_payment = $link->prepare("SELECT AI_datopago_id, TX_datopago_monto, datopago_AI_metododepago_id FROM bh_datopago WHERE datopago_AI_facturaf_id = ?")or die($link->error);
$qry_arqueo = $link->query("SELECT bh_arqueo.AI_arqueo_id, bh_arqueo.TX_arqueo_hora, bh_arqueo.TX_arqueo_fecha, bh_user.TX_user_seudonimo, bh_impresora.TX_impresora_seudonimo FROM ((bh_arqueo INNER JOIN bh_user ON bh_user.AI_user_id = bh_arqueo.arqueo_AI_user_id) INNER JOIN bh_impresora ON bh_impresora.AI_impresora_id = bh_arqueo.arqueo_AI_impresora_id) WHERE AI_arqueo_id = '$arqueo_id'")or die($link->error);
$rs_arqueo = $qry_arqueo->fetch_array(MYSQLI_ASSOC);
$qry_count_arqueo = $link->query("SELECT AI_arqueo_id FROM bh_arqueo WHERE TX_arqueo_fecha = '{$rs_arqueo['TX_arqueo_fecha']}'")or die($link->error);
$raw_count_arqueo = array(); $i=1;
while ($rs_count_arqueo = $qry_count_arqueo->fetch_array()) {
	$raw_count_arqueo[$rs_count_arqueo['AI_arqueo_id']] = $i;
	$i++;
}
$qry_arqueo_debito = $link->query("SELECT bh_notadebito.AI_notadebito_id, bh_notadebito.TX_notadebito_numero, bh_cliente.TX_cliente_nombre, bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_hora, bh_notadebito.TX_notadebito_total FROM (bh_notadebito INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadebito.notadebito_AI_cliente_id) WHERE notadebito_AI_arqueo_id = '$arqueo_id' ORDER BY TX_notadebito_fecha DESC, AI_notadebito_id DESC")or die($link->error);
$prep_datodebito = $link->prepare("SELECT AI_datodebito_id, TX_datodebito_monto, datodebito_AI_metododepago_id FROM bh_datodebito WHERE datodebito_AI_notadebito_id = ?")or die($link->error);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Arqueo de <?php echo $rs_arqueo['TX_user_seudonimo']." - ".$raw_count_arqueo[$arqueo_id]; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
</head>
<script type="text/javascript">
</script>

<body style="font-family:Arial" onLoad="window.print()">
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
?>	</div>
	</div>
	<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 80px;">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
			<h3>Detalle de Arqueo - #0<?php echo $raw_count_arqueo[$arqueo_id]; ?></h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
			<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Cajera(o): </strong><?php echo strtoupper($rs_arqueo['TX_user_seudonimo']); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Caja: </strong><?php echo $rs_arqueo['TX_impresora_seudonimo']; ?></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>Fecha: </strong><?php echo date('d-m-Y',strtotime($rs_arqueo['TX_arqueo_fecha'])); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Hora: </strong><?php echo strtoupper($rs_arqueo['TX_arqueo_hora']); ?></div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
	<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
			Facturas Relacionadas
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>Nº</strong></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>Nombre</strong></div>
			<div class="col-xs-2 col-sm-1 col-md-1 col-lg-1"><strong>Fecha</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>Total</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>Deficit</strong></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>Vendedor</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Met. de Pago</strong></div>
		</div><?php
		while ($rs_arqueo_facturaf = $qry_arqueo_facturaf->fetch_array(MYSQLI_ASSOC)) {  ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo $rs_arqueo_facturaf['TX_facturaf_numero'] ?></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $rs_arqueo_facturaf['TX_cliente_nombre'] ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo date('d-m-y', strtotime($rs_arqueo_facturaf['TX_facturaf_fecha']))."<br />".$rs_arqueo_facturaf['TX_facturaf_hora']; ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_right"><?php echo number_format($rs_arqueo_facturaf['TX_facturaf_total'],2) ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_right"><?php echo number_format($rs_arqueo_facturaf['TX_facturaf_deficit'],2) ?></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $rs_arqueo_facturaf['TX_user_seudonimo'] ?></div>
<?php 	$prep_payment->bind_param('i', $rs_arqueo_facturaf['AI_facturaf_id']); $prep_payment->execute(); $qry_payment=$prep_payment->get_result();
				$str_payment = '';
				while($rs_payment=$qry_payment->fetch_array(MYSQLI_ASSOC)){
					$str_payment .= "<strong>".$raw_metododepago[$rs_payment['datopago_AI_metododepago_id']]."</strong>: B/ ".number_format($rs_payment['TX_datopago_monto'],2)."<br />";
				}; ?>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $str_payment; ?></div>
			</div><?php
		} ?>
	</div>

	<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
			Debitos Relacionados
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Nº</strong></div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>Cliente</strong></div>
			<div class="col-xs-2 col-sm-1 col-md-1 col-lg-1"><strong>Fecha</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Total</strong></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>Met. de Pago</strong></div>
		</div><?php
		while ($rs_arqueo_debito = $qry_arqueo_debito->fetch_array(MYSQLI_ASSOC)) {  ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_arqueo_debito['TX_notadebito_numero'] ?></div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><?php echo $rs_arqueo_debito['TX_cliente_nombre'] ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo date('d-m-y', strtotime($rs_arqueo_debito['TX_notadebito_fecha']))."<br />".$rs_arqueo_debito['TX_notadebito_hora']; ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_right"><?php echo number_format($rs_arqueo_debito['TX_notadebito_total'],2) ?></div>
<?php 	$prep_datodebito->bind_param('i', $rs_arqueo_debito['AI_notadebito_id']); $prep_datodebito->execute(); $qry_datodebito=$prep_datodebito->get_result();
				$str_payment = '';
				while($rs_datodebito=$qry_datodebito->fetch_array(MYSQLI_ASSOC)){
					$str_payment .= "<strong>".$raw_metododepago[$rs_datodebito['datodebito_AI_metododepago_id']]."</strong>: B/ ".number_format($rs_datodebito['TX_datodebito_monto'],2)."<br />";
				}; ?>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $str_payment; ?></div>
			</div><?php
		} ?>
	</div>
	<!-- #####################         BODY          #################   -->
</div>
</body>
</html>
