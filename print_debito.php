<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

 $debito_id=$_SESSION['debito_id'];
 // $debito_id='15';
$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion = $qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$qry_user=$link->query("SELECT TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$rs_user=$qry_user->fetch_array();

$txt_facturaf="SELECT bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_ticket,
bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_facturaf.AI_facturaf_id
FROM (((bh_facturaf
INNER JOIN rel_facturaf_notadebito ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id)
INNER JOIN bh_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = bh_notadebito.AI_notadebito_id)
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_notadebito.AI_notadebito_id = '$debito_id'";

$qry_facturaf = $link->query($txt_facturaf)or die($link->error);
$rs_facturaf = $qry_facturaf->fetch_array();

$qry_facturaf_d = $link->query($txt_facturaf)or die($link->error);
$rs_facturaf_d = $qry_facturaf_d->fetch_array();

$txt_datodebito="SELECT bh_notadebito.TX_notadebito_cambio, bh_datodebito.TX_datodebito_monto, bh_datodebito.datodebito_AI_metododepago_id, bh_metododepago.TX_metododepago_value
FROM ((bh_notadebito
INNER JOIN bh_datodebito ON bh_notadebito.AI_notadebito_id = bh_datodebito.datodebito_AI_notadebito_id)
INNER JOIN bh_metododepago ON bh_datodebito.datodebito_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
WHERE bh_datodebito.datodebito_AI_notadebito_id = '$debito_id'";
$qry_datodebito=$link->query($txt_datodebito)or die($link->error);
$total_efectivo=0;
$total_cheque=0;
$total_tarjeta_credito=0;
$total_tarjeta_debito=0;
$total_nota_credito=0;
while($rs_datodebito=$qry_datodebito->fetch_array()){
	if($rs_datodebito['datodebito_AI_metododepago_id'] == '1'){
		$total_efectivo+=$rs_datodebito['TX_datodebito_monto'];
	}
	if($rs_datodebito['datodebito_AI_metododepago_id'] == '2'){
		$total_cheque+=$rs_datodebito['TX_datodebito_monto'];
	}
	if($rs_datodebito['datodebito_AI_metododepago_id'] == '3'){
		$total_tarjeta_credito+=$rs_datodebito['TX_datodebito_monto'];
	}
	if($rs_datodebito['datodebito_AI_metododepago_id'] == '4'){
		$total_tarjeta_debito+=$rs_datodebito['TX_datodebito_monto'];
	}
	if($rs_datodebito['datodebito_AI_metododepago_id'] == '7'){
		$total_nota_credito+=$rs_datodebito['TX_datodebito_monto'];
	}
	$cambio=$rs_datodebito['TX_notadebito_cambio'];
}
if(empty($cambio)){ $cambio=0; }
$total_total=$total_efectivo+$total_tarjeta_debito+$total_tarjeta_credito+$total_nota_credito+$total_cheque+$cambio;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recibo: <?php echo $rs_facturaf['TX_cliente_nombre']?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">

</head>
<body style="font-family:Arial" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];

if ($qry_facturaf->num_rows < 8) {
?>
<div style="text-align:center" class="container-fluid no_print">
	<button type="button"onclick="window.document.location.href='print_debito_v.php?a=<?php echo $debito_id; ?>'" name="button" class="btn btn-lg btn-default">Impresion con Historial</button>
</div>

<table align="center" cellpadding="0" cellspacing="0" border="0" style="height: 760px;width: 1001px;transform: rotate(90deg);
margin-top: 105px;margin-left: -130px;">
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
<tr style="height:123px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="65px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$time=strtotime($fecha_actual);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha_dia.",<br />"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <h3>RECIBO DE PAGO</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:45%">
            <strong>Nombre: </strong><br /><?php echo ucfirst($rs_facturaf['TX_cliente_nombre']); ?>
            </td>
            <td style="width:25%">
            <strong>RUC: </strong><br /><?php echo $rs_facturaf['TX_cliente_cif']; ?>
            </td>
            <td style="width:25%">
            <strong>Telefono: </strong><br /><?php echo $rs_facturaf['TX_cliente_telefono']; ?>
            </td>
    	</tr>
	</table>
    </td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
    	<thead>
        <tr>
        	<th style="width:25%"><strong>Nº Factura</strong></th>
          <th style="width:25%"><strong>Fecha</strong></th>
          <th style="width:25%"><strong>Total: </strong></th>
          <th style="width:25%"><strong>Saldo: </strong></th>
    	</tr>
        </thead>
        <tbody>
<?php 	$raw_facturaf=array();
				do{
					$raw_facturaf[]=$rs_facturaf;?>
        <tr>
        	<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
        	<td><?php $prefecha=strtotime($rs_facturaf['TX_facturaf_fecha']); echo date('d-m-Y',$prefecha); ?></td>
        	<td><?php echo "B/ ".number_format($rs_facturaf['TX_facturaf_total'],2); ?></td>
        	<td><?php echo "B/ ".number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
        </tr>
			<?php }while($rs_facturaf=$qry_facturaf->fetch_array());
			?>
        </tbody>
	</table>
<?php
	$qry_nd_afected=$link->prepare("SELECT rel_AI_notadebito_id from rel_facturaf_notadebito WHERE rel_AI_facturaf_id = ?")or die($link->error);
	$raw_ff_nd=array();
	$it=0;
	foreach ($raw_facturaf as $key => $value) {
		$qry_nd_afected->bind_param("i",$value['AI_facturaf_id']);
		$qry_nd_afected->execute();
		$result = $qry_nd_afected->get_result();
		$raw_nd=array();
		$i=0;
		while ($rs_nd_afected=$result->fetch_array()) {
			$raw_nd[$i]=$rs_nd_afected['rel_AI_notadebito_id'];
			$i++;
		}

		$raw_ff_nd[$value['AI_facturaf_id']]['nd_id']=$raw_nd;
		$raw_ff_nd[$value['AI_facturaf_id']]['ff_numero']=$value['TX_facturaf_numero'];
	}
	$qry_datodebito = $link->prepare("SELECT datodebito_AI_metododepago_id, TX_notadebito_numero, TX_notadebito_fecha, TX_datodebito_monto
		FROM (bh_datodebito
			INNER JOIN bh_notadebito ON bh_notadebito.AI_notadebito_id = bh_datodebito.datodebito_AI_notadebito_id)
			WHERE bh_notadebito.AI_notadebito_id = ? AND bh_datodebito.datodebito_AI_metododepago_id = '7'")or die($link->error);

?>
	<table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
		<caption>Notas de Credito Aplicadas</caption>
		<thead>
			<tr>
				<th style="width:30%">Fact. Afectada</th>
				<th style="width:30%">N&deg; Nota Cred.</th>
				<th style="width:20%">Fecha</th>
				<th style="width:20%">Monto</th>
			</tr>
		</thead>
		<tbody>
<?php
foreach ($raw_ff_nd as $key => $ff_id) {
	foreach ($ff_id['nd_id'] as $index => $nd_id) {
		$qry_datodebito->bind_param("i", $nd_id); $qry_datodebito->execute(); $result=$qry_datodebito->get_result();
		while ($rs_datodebito=$result->fetch_array()) {
			if ($rs_datodebito['datodebito_AI_metododepago_id'] === 7) {
?>
			<tr>
				<td><?php echo $ff_id['ff_numero']; ?></td>
				<td><?php echo $rs_datodebito['TX_notadebito_numero']; ?></td>
				<td><?php echo $rs_datodebito['TX_notadebito_fecha']; ?></td>
				<td><?php echo $rs_datodebito['TX_datodebito_monto']; ?></td>
			</tr>
<?php
			}
		}
	}
}
?>
		</tbody>
	</table>
  <p>
		<?php
			if($total_efectivo > 0){
				echo "<strong>Efectivo: B/ </strong>".number_format($total_efectivo+$cambio,2);
			}
			if($total_cheque > 0){
				echo "<strong>Cheque: B/ </strong>".number_format($total_cheque,2);
			}
			if($total_tarjeta_credito > 0){
				echo "<strong>TDC: B/ </strong>".number_format($total_tarjeta_credito,2);
			}
			if($total_tarjeta_debito > 0){
				echo "<strong>TDD: B/ </strong>".number_format($total_tarjeta_debito,2);
			}
			if($total_nota_credito > 0){
				echo "<strong>Nota de C.: B/ </strong>".number_format($total_nota_credito,2);
			}
		 ?>
	 </p>
    <strong>Total:</strong> B/ <?php echo number_format($total_total,2); ?><br />
<?php
			if($cambio > 0){
				echo "<strong>Cambio: B/ </strong>".number_format($cambio,2);
			}
?>

    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
    <font style="font-size:12px"><strong>
    <?php
	echo $rs_user[0];
	?>
    </strong></font>
    <br />
    Recib&iacute; conforme
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################        FIN LADO IZQUIERDO   ######################### --->
</td>
<td style="width:50%;">
<!-- ###############################        LADO DERECHO   ######################### --->
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
<tr style="height:123px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="65px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$time=strtotime($fecha_actual);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha_dia.", <br />"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <h3>RECIBO DE PAGO</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:45%">
            <strong>Nombre: </strong><br /><?php echo ucfirst($rs_facturaf_d['TX_cliente_nombre']); ?>
            </td>
            <td style="width:25%">
            <strong>RUC: </strong><br /><?php echo $rs_facturaf_d['TX_cliente_cif']; ?>
            </td>
            <td style="width:25%">
            <strong>Telefono: </strong><br /><?php echo $rs_facturaf_d['TX_cliente_telefono']; ?>
            </td>
    	</tr>
	</table>
    </td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
    <table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
    	<thead>
        <tr>
        	<th style="width:25%"><strong>Nº Factura</strong></th>
          <th style="width:25%"><strong>Fecha</strong></th>
          <th style="width:25%"><strong>Total: </strong></th>
          <th style="width:25%"><strong>Saldo: </strong></th>
      	</tr>
      </thead>
      <tbody>
        <?php

        do{  ?>
        <tr>
        	<td><?php echo $rs_facturaf_d['TX_facturaf_numero']; ?></td>
        	<td><?php	$prefecha=strtotime($rs_facturaf_d['TX_facturaf_fecha']);	echo date('d-m-Y',$prefecha); ?></td>
        	<td><?php echo "B/ ".number_format($rs_facturaf_d['TX_facturaf_total'],2); ?></td>
        	<td><?php echo "B/ ".number_format($rs_facturaf_d['TX_facturaf_deficit'],2); ?></td>
        </tr>
      <?php }while($rs_facturaf_d=$qry_facturaf_d->fetch_array()); ?>
      </tbody>
  	</table>
  <?php
  	$qry_nd_afected=$link->prepare("SELECT rel_AI_notadebito_id from rel_facturaf_notadebito WHERE rel_AI_facturaf_id = ?")or die($link->error);
  	$raw_ff_nd=array();
  	$it=0;
  	foreach ($raw_facturaf as $key => $value) {
  		$qry_nd_afected->bind_param("i",$value['AI_facturaf_id']);
  		$qry_nd_afected->execute();
  		$result = $qry_nd_afected->get_result();
  		$raw_nd=array();
  		$i=0;
  		while ($rs_nd_afected=$result->fetch_array()) {
  			$raw_nd[$i]=$rs_nd_afected['rel_AI_notadebito_id'];
  			$i++;
  		}
  		$raw_ff_nd[$value['AI_facturaf_id']]['nd_id']=$raw_nd;
  		$raw_ff_nd[$value['AI_facturaf_id']]['ff_numero']=$value['TX_facturaf_numero'];
  	}
  	$qry_datodebito = $link->prepare("SELECT datodebito_AI_metododepago_id, TX_notadebito_numero, TX_notadebito_fecha, TX_datodebito_monto
  		FROM (bh_datodebito
  			INNER JOIN bh_notadebito ON bh_notadebito.AI_notadebito_id = bh_datodebito.datodebito_AI_notadebito_id)
  			WHERE bh_notadebito.AI_notadebito_id = ? AND bh_datodebito.datodebito_AI_metododepago_id = '7'")or die($link->error);

  ?>
  	<table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
  		<caption>Notas de Credito Aplicadas</caption>
  		<thead>
  			<tr>
  				<th style="width:30%">Fact. Afectada</th>
  				<th style="width:30%">N&deg; Nota Cred.</th>
  				<th style="width:20%">Fecha</th>
  				<th style="width:20%">Monto</th>
  			</tr>
  		</thead>
  		<tbody>
  <?php
  foreach ($raw_ff_nd as $key => $ff_id) {
  	foreach ($ff_id['nd_id'] as $index => $nd_id) {
  		$qry_datodebito->bind_param("i", $nd_id); $qry_datodebito->execute(); $result=$qry_datodebito->get_result();
  		while ($rs_datodebito=$result->fetch_array()) {
  			if ($rs_datodebito['datodebito_AI_metododepago_id'] === 7) {
  ?>
  			<tr>
  				<td><?php echo $ff_id['ff_numero']; ?></td>
  				<td><?php echo $rs_datodebito['TX_notadebito_numero']; ?></td>
  				<td><?php echo $rs_datodebito['TX_notadebito_fecha']; ?></td>
  				<td><?php echo $rs_datodebito['TX_datodebito_monto']; ?></td>
  			</tr>
  <?php
  			}
  		}
  	}
  }
  ?>
  		</tbody>
  	</table>
  <p>
		<?php
			if($total_efectivo > 0){
				echo "<strong>Efectivo: B/ </strong>".number_format($total_efectivo+$cambio,2);
			}
			if($total_cheque > 0){
				echo "<strong>Cheque: B/ </strong>".number_format($total_cheque,2);
			}
			if($total_tarjeta_credito > 0){
				echo "<strong>TDC: B/ </strong>".number_format($total_tarjeta_credito,2);
			}
			if($total_tarjeta_debito > 0){
				echo "<strong>TDD: B/ </strong>".number_format($total_tarjeta_debito,2);
			}
			if($total_nota_credito > 0){
				echo "<strong>Nota de C.: B/ </strong>".number_format($total_nota_credito,2);
			}
		 ?>
	 </p>
    <strong>Total:</strong> B/ <?php echo number_format($total_total,2); ?><br />
<?php
			if($cambio > 0){
				echo "<strong>Cambio: B/ </strong>".number_format($cambio,2);
			}
?>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
    <font style="font-size:12px"><strong>
    <?php
	echo $rs_user[0];
	?>
    </strong></font>
    <br />
    Recib&iacute; conforme
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################      FIN LADO DERECHO   ######################### --->
</td>
</tr>
</table>
<?php
}else{
//  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@       FORMAT VERTICA @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
?>
<div style="text_align:center" class="container-fluid no_print">
	<button type="button"onclick="window.document.href='print_debito_v.php?a=<?php echo $debito_id; ?>'" name="button" class="btn btn-lg btn-primary">Impresion con Historial</button>
</div>
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
<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo date('d-m-Y', strtotime($fecha_actual)); ?>
    </td>
</tr>
<tr style="height:40px" align="center">
	<td valign="top" colspan="10">
		<h2>RECIBO DE PAGO</h2>
  </td>
</tr>
<tr style="height:44px">
	<td valign="top" colspan="10">
		<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
			<tr>
				<td style="width:45%">
					<strong>Nombre: </strong><br /><?php echo ucfirst($rs_facturaf['TX_cliente_nombre']); ?>
				</td>
				<td style="width:25%">
					<strong>RUC: </strong><br /><?php echo $rs_facturaf['TX_cliente_cif']; ?>
				</td>
				<td style="width:25%">
					<strong>Telefono: </strong><br /><?php echo $rs_facturaf['TX_cliente_telefono']; ?>
				</td>
			</tr>
		</table>
  </td>
</tr>
<tr style="height:754px;">
	<td valign="top" colspan="10" style="padding-top:2px;">

		<table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">

			<thead>
      <tr>
				<th style="width:20%"><strong>Nº Factura</strong></th>
				<th style="width:20%"><strong>Nº Ticket</strong></th>
	      <th style="width:10%"><strong>Fecha</strong></th>
	      <th style="width:25%"><strong>Total: </strong></th>
	      <th style="width:25%"><strong>Saldo: </strong></th>
    	</tr>
    </thead>

      <tbody>
<?php 	$index = 1;
				$pager = 0;
				do{
					$pager++;
					if($index === 1){
						if($pager === 40){
							$pager = 0;
							$index++;
?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr style="height:580px;">
					<td valign="top" colspan="10" style="padding-top:2px;">
						<table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
							<thead>
				      <tr>
								<th style="width:20%"><strong>Nº Factura</strong></th>
								<th style="width:20%"><strong>Nº Ticket</strong></th>
					      <th style="width:10%"><strong>Fecha</strong></th>
					      <th style="width:25%"><strong>Total: </strong></th>
					      <th style="width:25%"><strong>Saldo: </strong></th>
				    	</tr>
				    </thead>
			      	<tbody>
<?php
							}
						}else{
							if($pager === 60){
								$pager = 0;
								$index++;
?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr style="height:580px;">
				<td valign="top" colspan="10" style="padding-top:2px;">
					<table align="center" border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px; text-align:center;">
						<thead>
						<tr>
							<th style="width:20%"><strong>Nº Factura</strong></th>
							<th style="width:20%"><strong>Nº Ticket</strong></th>
				      <th style="width:10%"><strong>Fecha</strong></th>
				      <th style="width:25%"><strong>Total: </strong></th>
				      <th style="width:25%"><strong>Saldo: </strong></th>
						</tr>
					</thead>
						<tbody>
<?php }
		}
?>
			<tr>
				<td>
<?php 		echo $rs_facturaf['TX_facturaf_numero']; ?>
        </td>
				<td>
<?php 		echo $rs_facturaf['TX_facturaf_ticket']; ?>
        </td>
        <td>
<?php			echo date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha'])); ?>
        </td>
        <td>
<?php 		echo "B/ ".number_format($rs_facturaf['TX_facturaf_total'],2); ?>
        </td>
        <td>
<?php 		echo "B/ ".number_format($rs_facturaf['TX_facturaf_deficit'],2); ?>
        </td>
      </tr>
<?php 		}while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
    </tbody>
		</table>
    <p>
<?php
  			if($total_efectivo > 0){
  				echo "<strong>Efectivo: B/ </strong>".number_format($total_efectivo+$cambio,2);
  			}
  			if($total_cheque > 0){
  				echo "<strong>Cheque: B/ </strong>".number_format($total_cheque,2);
  			}
  			if($total_tarjeta_credito > 0){
  				echo "<strong>TDC: B/ </strong>".number_format($total_tarjeta_credito,2);
  			}
  			if($total_tarjeta_debito > 0){
  				echo "<strong>TDD: B/ </strong>".number_format($total_tarjeta_debito,2);
  			}
  			if($total_nota_credito > 0){
  				echo "<strong>Nota de C.: B/ </strong>".number_format($total_nota_credito,2);
  			}
?>
  	 </p>
      <strong>Total:</strong> B/ <?php echo number_format($total_total,2); ?><br />
<?php
  			if($cambio > 0){
  				echo "<strong>Cambio: B/ </strong>".number_format($cambio,2);
  			}
?>
<?php
}
unset($_SESSION['debito_id']);
?>
</body>
</html>
