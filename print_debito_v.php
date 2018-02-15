<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

 $debito_id=$_GET['a'];
// $debito_id='11';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion = $qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$qry_user=$link->query("SELECT TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$rs_user=$qry_user->fetch_array();
?>
<?php
$txt_facturaf="SELECT bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_ticket,
bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_deficit
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

$qry_nd = $link->prepare("SELECT bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_motivo, bh_notadebito.TX_notadebito_numero, rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe
	FROM ((bh_notadebito
		INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id =	rel_facturaf_notadebito.rel_AI_notadebito_id)
		INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id =	rel_facturaf_notadebito.rel_AI_facturaf_id)
		WHERE bh_facturaf.AI_facturaf_id = ? ORDER BY TX_notadebito_fecha ASC, TX_notadebito_hora ASC")or die($link->error);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_facturaf['TX_cliente_nombre']." - ".$rs_facturaf['TX_facturaf_numero']; ?></title>
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
<tr style="height:21px" align="center">
	<td valign="top" colspan="10"><h4>RECIBO DE PAGO</h4></td>
</tr>
<tr style="height:184px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table">
		<tbody style="background-color:#DDDDDD; border:solid;">
    	<tr>
        	<td valign="top"  class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_facturaf['TX_cliente_nombre']); ?>
            </td>
            <td valign="top"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_facturaf['TX_cliente_cif']); ?>
            </td>
            <td valign="top"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_facturaf['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top" colspan="3">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper(substr($rs_facturaf['TX_cliente_direccion'],0,70)); ?>
          </td>
        </tr>
    </table>
		<table id="tbl_afectedff" class="table table-bordered table-condensed">
			<caption class="caption">Facturas Afectadas</caption>
			<tbody style="border:solid;">
				<tr>
<?php $raw_facturaf=array(); $i=0;
			do{
				$raw_facturaf[]=$rs_facturaf;
?>
					<td style="border: solid 1px #000;"><?php echo "<strong>N&deg;: </strong>".$rs_facturaf['TX_facturaf_numero']; ?>&nbsp;&nbsp;<?php echo "<strong>Saldo:</strong> B/ ".number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
<?php 	$i++;
				if ($i===2) {
					echo '</tr><tr>';
				}
			}while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
				</tr>
			</tbody>
		</table>
    <table id="tbl_facturaf" class="table">
			<caption class="caption">Formas de Pago</caption>
		<tbody style="border:solid;">
    	<tr>
<?php
	if($total_efectivo > 0){
		echo "<td><strong>Efectivo: B/ </strong>".number_format($total_efectivo+$cambio,2)."</td>";
	}
	if($total_cheque > 0){
		echo "<td><strong>Cheque: B/ </strong>".number_format($total_cheque,2)."</td>";
	}
	if($total_tarjeta_credito > 0){
		echo "<td><strong>TDC: B/ </strong>".number_format($total_tarjeta_credito,2)."</td>";
	}
	if($total_tarjeta_debito > 0){
		echo "<td><strong>TDD: B/ </strong>".number_format($total_tarjeta_debito,2)."</td>";
	}
	if($total_nota_credito > 0){
		echo "<td><strong>Nota de C.: B/ </strong>".number_format($total_nota_credito,2)."</td>";
	}
	if($cambio > 0){
		echo "<td><strong>Cambio: B/ </strong>".number_format($cambio,2)."</td>";
	}
 ?>
        </tr>
      </tbody>
		</table>
  </td>
</tr>
<tr style="height:580px; page-break-before: always;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_billhistory" cellpadding="0" cellspacing="0" class="table table-bordered table-condensed" style="font-size:12px; text-align:center;">
    	<caption class="caption">Historal de Facturas</caption>
			<thead style="border: solid 2px #000;">
        <tr>
        	<th style="width:25%;"><strong>FECHA</strong></th>
          <th style="width:25%;"><strong>DOCUMENTO</strong></th>
          <th style="width:25%;"><strong>NUMERO</strong></th>
          <th style="width:25%;"><strong>IMPORTE</strong></th>
    		</tr>
      </thead>
      <tbody>
<?php
				$qry_datopago=$link->prepare("SELECT bh_datopago.TX_datopago_monto FROM bh_datopago WHERE datopago_AI_facturaf_id = ? AND datopago_AI_metododepago_id = '5'")or die($link->error);
				foreach ($raw_facturaf as $key => $rs_facturaf) {
					$qry_datopago->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_datopago->execute(); $result=$qry_datopago->get_result();
					$rs_datopago = $result->fetch_array();
?>
        <tr>
        	<td><?php $prefecha=strtotime($rs_facturaf['TX_facturaf_fecha']); echo date('d-m-Y',$prefecha); ?></td>
        	<td><?php echo "Factura"; ?></td>
        	<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
        	<td><?php echo "B/ ".number_format($rs_datopago['TX_datopago_monto'],2); ?></td>
        </tr>
<?php
			$qry_nd->bind_param("i", $rs_facturaf['TX_facturaf_numero']); $qry_nd->execute(); $result = $qry_nd->get_result();
			$importe=0;
			while ($rs_nd = $result->fetch_array()) {
				$importe += $rs_nd['TX_rel_facturafnotadebito_importe'];
				if ($rs_nd['TX_rel_facturafnotadebito_importe'] > 0) {
?>
					<tr>
						<td><?php $prefecha=strtotime($rs_nd['TX_notadebito_fecha']); echo date('d-m-Y',$prefecha); ?></td>
						<td><?php echo $rs_nd['TX_notadebito_motivo']; ?></td>
						<td><?php echo $rs_nd['TX_notadebito_numero']; ?></td>
						<td><?php echo "- B/ ".number_format($rs_nd['TX_rel_facturafnotadebito_importe'],2); ?></td>
					</tr>
<?php
				}
			}
?>
			<tr style="border:solid 2px #000000;"> <td></td><td></td><td style="text-align:right;"><strong>SALDO:</strong></td><td><?php echo "B/ ".number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td> </tr>
			<?php };
			?>
      </tbody>
		</table>
	</td>
</tr>


			 	</tbody>
			</table>
	  </td>
	</tr>
</table>
</body>
</html>
