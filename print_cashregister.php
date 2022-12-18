<?php 
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$fecha_actual = date('d-m-Y');

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$qry_metododepago=$link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
$raw_metododepago=array();
while ($rs_metododepago = $qry_metododepago->fetch_array()) {
	$raw_metododepago[$rs_metododepago['AI_metododepago_id']] = $rs_metododepago['TX_metododepago_value'];
}

function ObtenerIP(){
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
$ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
$ip = $_SERVER['REMOTE_ADDR'];
else
$ip = "IP desconocida";
return($ip);
}
$ip   = ObtenerIP();
$host_name = gethostbyaddr($ip);

if (isset($_SESSION['arqueo_id'])) {
	$arqueo_id=$_SESSION['arqueo_id'];
}else if(isset($_GET['a'])){
	$arqueo_id=$_GET['a'];
}else{
	$arqueo_id =	"";
}

//$arqueo_id='6';

$txt_cashregister="SELECT bh_arqueo.TX_arqueo_fecha, bh_arqueo.TX_arqueo_hora, bh_arqueo.TX_arqueo_pago, bh_arqueo.TX_arqueo_debito, bh_arqueo.TX_arqueo_ventabruta, bh_arqueo.TX_arqueo_ventaneta, bh_arqueo.TX_arqueo_devolucion, bh_arqueo.TX_arqueo_totalni, bh_arqueo.TX_arqueo_totalci, bh_arqueo.TX_arqueo_totalci_nc, bh_arqueo.TX_arqueo_impuesto, bh_arqueo.TX_arqueo_impuesto_nc, bh_arqueo.TX_arqueo_descuento, bh_arqueo.TX_arqueo_cantidadff, bh_arqueo.TX_arqueo_entrada, bh_arqueo.TX_arqueo_salida, bh_arqueo.TX_arqueo_anulado,
bh_user.TX_user_seudonimo, bh_arqueo.arqueo_AI_impresora_id
FROM (bh_arqueo
INNER JOIN bh_user ON bh_user.AI_user_id = bh_arqueo.arqueo_AI_user_id)
WHERE bh_arqueo.AI_arqueo_id = '$arqueo_id'";
$qry_cashregister = $link->query($txt_cashregister)or die($link->error);
$rs_cashregister = $qry_cashregister->fetch_array();
$raw_pago = json_decode($rs_cashregister['TX_arqueo_pago'],true);
$raw_debito = json_decode($rs_cashregister['TX_arqueo_debito'],true);
$raw_nc_anulated = json_decode($rs_cashregister['TX_arqueo_anulado'],true);

$qry_chkarqueo = $link->query("SELECT AI_arqueo_id FROM bh_arqueo WHERE TX_arqueo_fecha = '{$rs_cashregister['TX_arqueo_fecha']}'");
$nr_chkarqueo=$qry_chkarqueo->num_rows;
$impresora_id = $rs_cashregister['arqueo_AI_impresora_id'];
$qry_impresora = $link->query("SELECT TX_impresora_cliente FROM bh_impresora WHERE AI_impresora_id = '$impresora_id'")or die($link->error);
$rs_impresora = $qry_impresora->fetch_array(MYSQLI_ASSOC);


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Arqueo de Caja, TRILLI, S.A.</title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />

</head>
<body style="font-family:Arial" onLoad="window.print()">

<?php
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($rs_cashregister['TX_arqueo_fecha']));
$fecha_dia = $dias[$d_number];
$time=strtotime($rs_cashregister['TX_arqueo_fecha']);
$date=date('d-m-Y',$time);
?>
<table id="tbl_body" align="center" cellpadding="0" cellspacing="0" border="0" style="height: 760px;width: 1001px; <?php  echo 'transform: rotate(90deg);
margin-left: -122px'; ?>; margin-top: 105px;">
<tr>
<td style="width:50%;">
<!-- ##################        LADO IZQUIERDO     ################################-->
<table id="tbl_print" align="center" cellpadding="0" cellspacing="0" border="0" style="height:760px; width:470px; font-size:14px; padding:0 30px 0 0; ">
<tr style="height:1px">
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
<tr style="height:80px" align="right">
	<td colspan="2" style="text-align:left">
  </td>
 	<td valign="top" colspan="6" style="text-align:center">
		<img width="200px" height="65px" src="attached/image/logo_factura.png">
		<br />
		<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
  </td>
    <td valign="top" colspan="2" class="optmayuscula">
<?php echo date('d-m-Y',strtotime($fecha_actual)); ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <span style="font-size:14px; font-weight: bolder;"><?php echo strtoupper($rs_impresora['TX_impresora_cliente']); ?> Arqueo de Caja #<?php echo $nr_chkarqueo. " (".$arqueo_id.")"; ?></span>
		<?php
		echo "<br />".$fecha_dia.",&nbsp;"; ?><?php echo $date." "; ?><?php echo $rs_cashregister['TX_arqueo_hora']; ?>
  </td>
</tr>
<tr style="height:28px">
	<td valign="top" colspan="10">
  <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
	<tr>
  	<td style="width:100%">
      <strong>Operador(a): </strong><?php echo ucfirst($Operador=$rs_cashregister['TX_user_seudonimo']); ?>
    </td>
	</tr>
	</table>
    </td>
</tr>
<tr style="height:606px">
	<td valign="top" colspan="5" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
    	<thead>
        <tr>
        	<th style="width:100%" colspan="2" class="al_center"><strong>TOTALES</strong></th>
    	</tr>
        </thead>
        <tbody>
        <tr>
        	<td style="width:60%; text-align:right;">Venta Bruta:&nbsp;</td>
            <td style="width:40%; text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_ventabruta'],2); ?></td>
        </tr>
        <tr>
        	<td style="width:60%; text-align:right;">Descuentos:&nbsp;</td>
            <td style="width:40%; text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_descuento'],2); ?></td>
        </tr>
        <tr>
        	<td style="text-align:right;">Venta Neta:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_ventaneta'],2); ?></td>
        </tr>
        <tr>
        	<td style="text-align:right;">Documentos:&nbsp;</td>
          <td style="text-align:left;">&nbsp;<?php echo $rs_cashregister['TX_arqueo_cantidadff']; ?></td>
        </tr>
        <tr>
        	<td style="text-align:right;">Venta Real:&nbsp;</td>
            <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_ventaneta']+$rs_cashregister['TX_arqueo_devolucion']+array_sum(json_decode($rs_cashregister['TX_arqueo_anulado'],true)),2); ?></td>
        </tr>
        <tr>
          <td style="text-align:right;">Devoluci&oacute;n:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_devolucion'],2); ?></td>
        </tr>
        <tr>
          <td colspan="2"><strong>DESGLOSE ITBMS</strong></td>
        </tr>
				<tr>
          <td style="text-align:right;">Base No Imponible:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_totalni'],2); ?></td>
        </tr>
        <tr>
          <td style="text-align:right;">Base Imponible:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_totalci'],2); ?></td>
        </tr>
				<tr>
          <td style="text-align:right;">Base Imponible N.C.:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_totalci_nc'],2); ?></td>
        </tr>
				<tr>
          <td style="text-align:right;">Impuesto:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_impuesto'],2); ?></td>
        </tr>
				<tr>
          <td style="text-align:right;">Impuesto N.C.:&nbsp;</td>
          <td style="text-align:left;">&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_impuesto_nc'],2); ?></td>
        </tr>
        </tbody>
	</table>
    </td>
	<td valign="top" colspan="5" class="optmayuscula" style="line-height:3;">
    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
    	<thead>
            <tr>
                <th style="width:100%" class="al_center"><strong>MOVIMIENTOS</strong></th>
            </tr>
        </thead>
    	<tbody>
			<tr>
				<td><strong>Caja Menuda</strong> <br />
					Entradas:&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_entrada'],2); ?><br />
<?php 		$qry_cambio = $link->query("SELECT TX_efectivo_monto FROM bh_efectivo WHERE TX_efectivo_motivo LIKE '%CAMBIO CHEQUE%' AND efectivo_AI_arqueo_id = '$arqueo_id'")or die($link->error);
					$ttl_cambio_cheque=0;
					while($rs_cambio = $qry_cambio->fetch_array()){
						$ttl_cambio_cheque += $rs_cambio['TX_efectivo_monto'];
					};
					$ttl_cambio_cheque = round($ttl_cambio_cheque,2);
 ?>
					Salidas:&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_salida']-$rs_cashregister['TX_arqueo_devolucion']-$ttl_cambio_cheque,2); ?><br />
					Salidas P/devoluci&oacute;n:&nbsp;B/ <?php echo number_format($rs_cashregister['TX_arqueo_devolucion'],2); ?><br />
					Cambio a Cheques:&nbsp;B/ <?php echo number_format($ttl_cambio_cheque,2); ?><br />
					<em>Total:</em>&nbsp;B/ <?php echo number_format($total= $rs_cashregister['TX_arqueo_entrada']-$rs_cashregister['TX_arqueo_salida'],4); ?>
				</td>
			</tr>
<?php 	foreach ($raw_metododepago as $index => $value): 				?>
			<tr>
				<td><strong><?php echo $value; ?></strong> <br />
				Ventas:&nbsp;B/ <?php echo number_format($raw_pago[$index],2); ?>&nbsp;|&nbsp;
				Cobros:&nbsp;B/ <?php echo number_format($raw_debito[$index],2); ?><br />
<?php if ($raw_nc_anulated[$index] > 0): ?>
				Anulados:&nbsp;B/ -<?php echo number_format($raw_nc_anulated[$index],2); ?><br />
<?php endif; ?>
				<em>Total:</em>&nbsp;B/ <?php echo number_format($total= $raw_pago[$index]+$raw_debito[$index]-$raw_nc_anulated[$index],4); ?>
			</td>
			</tr>
<?php 	endforeach; 				?>
        </tbody>
	</table>
    </td>
</tr>
</table>
<!-- ###############################        FIN LADO IZQUIERDO   ######################### -->
</td>
<td style="width:50%;">
<!-- ###############################        LADO DERECHO   ######################### -->
<table id="tbl_print" align="center" cellpadding="0" cellspacing="0" border="0" style="height:760px; width:470px; font-size:14px; padding:0 0 0 30px; ">
<tr style="height:1px">
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
<tr style="height:80px" align="right">
	<td colspan="2" style="text-align:left">
  </td>

 	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="65px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
  </td>
  <td valign="top" colspan="2" class="optmayuscula">
		<?php date('d-m-Y',strtotime($fecha_actual)); ?>
  </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <span style="font-size:14px; font-weight: bolder;"><?php echo strtoupper($host_name); ?> Arqueo de Caja #<?php echo $nr_chkarqueo. " (".$arqueo_id.")"; ?></span>
		<?php echo "<br />".$fecha_dia.", "; ?><?php echo $date." "; ?><?php echo $rs_cashregister['TX_arqueo_hora']; ?>
  </td>
</tr>
<tr style="height:28px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:100%">
            <strong>Operador(a): </strong><?php echo ucfirst($Operador); ?>
            </td>
    	</tr>
	</table>
    </td>
</tr>
<tr style="height:518px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>

    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    <tbody>
    <tr>
        <td>Saldo Inicial: ______________</td>
    </tr>
    <tr>
        <td><center><strong>EXISTENCIAS DE EFECTIVO</strong></center></td>
    </tr>
    </tbody>
    </table>

    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
    <tbody>
    <tr>
        <td style="width:30%"></td>
        <td style="width:40%"><strong>Efectivo Total:</strong></td>
        <td style="width:30%"></td>
    </tr>
    <tr>
        <td><strong>Cantidad</strong></td>
        <td><strong>Valor</strong></td>
        <td><strong>Total</strong></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(100,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(50,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(20,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(10,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(5,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(1,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(0.5,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(0.25,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(0.1,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(0.05,2); ?></td><td></td>
    </tr>
    <tr>
        <td></td><td>B/ <?php echo number_format(0.01,2); ?></td><td></td>
    </tr>
    </tbody>
    </table>
    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center; padding-top:10px;">
    <tbody>
    <tr>
    	<td style="width:30%"></td>
        <td style="width:40%"><strong>Cheques Total:</strong></td>
        <td style="width:30%"></td>
    </tr>
    <tr>
    	<td><strong>Numero</strong></td>
        <td><strong>Banco</strong></td>
        <td><strong>Total</strong></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    <tr>
    	<td>&nbsp;</td><td></td><td></td>
    </tr>
    </tbody>
	</table>
    <strong>Observaciones:</strong>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
    <br />
    Firma Conformidad
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################      FIN LADO DERECHO   ######################### -->
</td>
</tr>
</table>

<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" style="page-break-after: always; height: 150px;"></div>
<?php 
$txt_cajamenuda="SELECT bh_efectivo.AI_efectivo_id, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_efectivo.TX_efectivo_fecha,
bh_efectivo.TX_efectivo_status, bh_user.TX_user_seudonimo, bh_efectivo.efectivo_AI_arqueo_id
FROM (bh_efectivo INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE bh_efectivo.efectivo_AI_arqueo_id = '$arqueo_id' ORDER BY efectivo_AI_arqueo_id ASC, TX_efectivo_tipo ASC";
$qry_cajamenuda=$link->query($txt_cajamenuda)or die($link->error);

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
<tr style="height:34px">
	<td valign="top" colspan="10"  style="text-align:center">
	    <h4>Caja Menuda - Arqueo #<?php echo $arqueo_id; ?></h4><br />
    </td>
</tr>
<tr style="height:781px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table border="0" id="tbl_notadebito" class="table table-striped table-bordered">
        <thead style="border:solid; background-color:#DDDDDD">
            <tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>FECHA</strong>
				</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>USUARIO</strong>
				</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<strong>ARQUEO</strong>
				</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<strong>TIPO</strong>
				</th>
				<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<strong>MOTIVO</strong>
				</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>MONTO</strong>
				</th>
			</tr>
		</thead>
    <tbody>
<?php
$index = 1;
$pager = 0;
		while ($rs_cajamenuda=$qry_cajamenuda->fetch_array()) {
			$pager++;
			if($index === 1){
				if($pager === 20){
					$pager = 0;
					$index++;
?>
					</tbody>
					</table>
				</td>
			</tr>
			<tr style="height:781px;">
				<td valign="top" colspan="10" style="padding-top:2px;">
					<table id="tbl_notadebito" class="table table-striped table-bordered">
						<thead style="border:solid; background-color:#DDDDDD">
							<tr>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>FECHA</strong>
								</th>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>USUARIO</strong>
								</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<strong>ARQUEO</strong>
								</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<strong>TIPO</strong>
								</th>
								<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
									<strong>MOTIVO</strong>
								</th>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>MONTO</strong>
								</th>
							</tr>
						</thead>
					<tbody>
<?php
}
}else{
if($pager === 26){
$pager = 0;
$index++;
 ?>
				</tbody>
				</table>
			</td>
		</tr>
		<tr style="height:781px;">
			<td valign="top" colspan="10" style="padding-top:2px;">
				<table id="tbl_notadebito" class="table table-striped table-bordered">
					<thead style="border:solid; background-color:#DDDDDD">
						<tr>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>FECHA</strong>
							</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>USUARIO</strong>
							</th>
							<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								<strong>ARQUEO</strong>
							</th>
							<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								<strong>TIPO</strong>
							</th>
							<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<strong>MOTIVO</strong>
							</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>MONTO</strong>
							</th>
						</tr>
					</thead>
				<tbody>
<?php
					}
				}
?>
				<tr style="height:30px;">
					<td><?php echo $rs_cajamenuda['TX_efectivo_fecha']; ?></td>
					<td><?php echo substr($rs_cajamenuda['TX_user_seudonimo'],0,11); ?></td>
					<td><?php echo $rs_cajamenuda['efectivo_AI_arqueo_id']; ?></td>
					<td><?php echo $rs_cajamenuda['TX_efectivo_tipo']; ?></td>
					<td><?php echo substr($rs_cajamenuda['TX_efectivo_motivo'],0,45); echo ($rs_cajamenuda['efectivo_AI_arqueo_id'] === '-1') ?  " (ANULADO)" : ""; ?></td>
					<td><?php echo $rs_cajamenuda['TX_efectivo_monto']; ?></td>
				</tr>
<?php
			}
?>
 		</tbody>
	</table>

    </td>
</tr>
</table>

<?php
//unset($_SESSION['arqueo_id']);
?>
</body>
</html>
