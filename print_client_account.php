<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$fecha_i = date('Y-m-d', strtotime($_GET['b']));
$fecha_f = date('Y-m-d', strtotime($_GET['c']));

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$client_id = $_GET['a'];
$txt_client="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, bh_cliente.TX_cliente_direccion, SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, SUM(bh_facturaf.TX_facturaf_subtotalni) AS subtotal_ni, SUM(bh_facturaf.TX_facturaf_subtotalci) AS subtotal_ci, SUM(bh_facturaf.TX_facturaf_total) AS total, SUM(bh_facturaf.TX_facturaf_impuesto) AS impuesto FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id'";
$qry_client=$link->query($txt_client)or die($link->error);
$rs_client=$qry_client->fetch_array();

$qry_facturaf = $link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero,
	 bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit FROM bh_facturaf WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > 0  AND bh_facturaf.TX_facturaf_fecha >= '$fecha_i' AND bh_facturaf.TX_facturaf_fecha <= '$fecha_f' ORDER BY AI_facturaf_id DESC")or die($link->error);
$raw_ff_included = array();
while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
		$raw_ff_included[$rs_facturaf['AI_facturaf_id']]=$rs_facturaf;
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">
	<div class="container-fluid no_print al_center">
		<button type="button" onclick="window.document.location.href='print_client_account_historical.php?a=<?php echo $client_id; ?>'" name="button" class="btn btn-lg btn-default">Impresion con Historial</button>
		&nbsp;&nbsp;
		<button type="button" onclick="window.document.location.href='print_client_accountwpayment.php?a=<?php echo $client_id; ?>'" name="button" class="btn btn-lg btn-default">Impresion con Pagos</button>
	</div>
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
			<h3>Estado de Cuenta</h3>
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
			Facturas con Saldo
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>FECHA</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>DESCRIPCION</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>NUMERO</strong></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>IMPORTE</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></div>
		</div><?php $total_saldo=0;

		foreach ($raw_ff_included as $key => $rs_facturaf_debito) {
			$total_saldo+=$rs_facturaf_debito['TX_facturaf_deficit'];  ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo date('d-m-Y', strtotime($rs_facturaf_debito['TX_facturaf_fecha']))."-".$rs_facturaf_debito['TX_facturaf_hora']; ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo "FACTURA"; ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_facturaf_debito['TX_facturaf_numero']; ?></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center"><?php echo "B/ ".number_format($rs_facturaf_debito['TX_facturaf_total'],2); ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo "B/ ".number_format($rs_facturaf_debito['TX_facturaf_deficit'],2); ?></div>
			</div><?php
		} ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> </div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> </div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> </div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> </div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>Total:</strong><br /><?php echo "B/ ".number_format($total_saldo,2); ?></div>
		</div>
	</div>
	<!-- #####################         BODY          #################   -->
</div>
</body>
</html>
