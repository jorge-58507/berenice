<?php
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
$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha >= '$date_i' AND TX_facturaf_fecha <= '$date_f'";
$line_deficit="";
if(isset($_GET['d'])){
	if($_GET['d'] == "deficit"){ $line_deficit=" AND TX_facturaf_deficit > 0"; } else{ $line_deficit = ""; }
}
$line_order=" ORDER BY AI_facturaf_id";
$qry_facturaf=$link->query($txt_facturaf.$line_deficit.$line_order) or die($link->error);


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
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
			<h3>FACTURAS</h3>
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
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>NUMERO</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>TICKET</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>BASE</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>DESC</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>IMP</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>TOTAL</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></div>
		</div><?php
		$total=0; $saldo=0;
		while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
			$total+=round($rs_facturaf['TX_facturaf_total'],2);
			$saldo+=round($rs_facturaf['TX_facturaf_deficit'],2);?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><?php echo date('d-m-Y', strtotime($rs_facturaf['TX_facturaf_fecha']))."<br />".$rs_facturaf['TX_facturaf_hora']; ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_facturaf['TX_facturaf_numero']; ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo $rs_facturaf['TX_facturaf_ticket']; ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format($base_ni = round($rs_facturaf['TX_facturaf_subtotalni'],2)+round($rs_facturaf['TX_facturaf_subtotalci'],2),2); ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo number_format($base_ni = round($rs_facturaf['TX_facturaf_descuentoni'],2)+round($rs_facturaf['TX_facturaf_descuento'],2),2); ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo number_format($rs_facturaf['TX_facturaf_impuesto'],2); ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></div>
			</div><?php
		} ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"> </div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>TOTAL:</strong><br /><?php echo "B/ ".number_format($total,2); ?></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>SALDO:</strong><br /><?php echo "B/ ".number_format($saldo,2); ?></div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
</div>
</body>
</html>
