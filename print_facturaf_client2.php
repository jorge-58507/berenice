<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$qry_paymentmethod=mysql_query("SELECT AI_metododepago_id,TX_metododepago_value FROM bh_metododepago");
$nr_paymentmethod=mysql_num_rows($qry_paymentmethod);
$raw_paymentmethod=array();
while ($rs_paymentmethod=mysql_fetch_assoc($qry_paymentmethod)) {
	$raw_paymentmethod[$rs_paymentmethod['AI_metododepago_id']]=$rs_paymentmethod['TX_metododepago_value'];
}


$client_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

$qry_client=mysql_query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$rs_client=mysql_fetch_array($qry_client);

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
<tr style="height:21px" align="center">
	<td valign="top" colspan="10">
    </td>
</tr>
<tr style="height:134px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table table-print">
		<tbody style="background-color:#dddddd; border:solid;">
    	<tr>
        	<td valign="top" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?>
            </td>
            <td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?>
            </td>
            <td valign="top" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_client['TX_cliente_direccion']); ?>
            </td>
            <td colspan="2"><?php echo "<strong>Desde:</strong> ".$date_i." <strong>Hasta:</strong> ".$date_f; ?></td>
        </tr>
        </tbody>
    </table>
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
        <tr>
        	<td valign="top" style="text-align:center;">
			<h4>FACTURAS</h4><br />
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr style="height:680px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_notadebito" class="table table-striped table-bordered">
    <thead style="border:solid; background-color:#DDDDDD">
    	<tr>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<strong>FECHA</strong>
        </th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
          <strong>NUMERO</strong>
        </th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
          <strong>TICKET</strong>
        </th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <strong>BASE</strong>
        </th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
          <strong>DESC.</strong>
        </th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <strong>IMP.</strong>
        </th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <strong>TOTAL</strong>
        </th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <strong>SALDO</strong>
        </th>
			</tr>
		</thead>
    <tfoot> <tr><td colspan="8"></td></tr></tfoot>
    <tbody>
	    <?php
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
		$line_order=" ORDER BY TX_facturaf_fecha";
$qry_facturaf=mysql_query($txt_facturaf.$line_deficit.$line_order) or die(mysql_error());
			$total=0;
			$saldo=0;
		?>
        <?php while($rs_facturaf=mysql_fetch_array($qry_facturaf)){
			$total+=round($rs_facturaf['TX_facturaf_total'],2);
			$saldo+=round($rs_facturaf['TX_facturaf_deficit'],2);
		?>

    	<tr style="height:30px;">
        	<td>
				<?php echo $rs_facturaf['TX_facturaf_fecha']; ?>
                <br />
				<?php echo $rs_facturaf['TX_facturaf_hora']; ?>
            </td>
            <td>
				<?php echo $rs_facturaf['TX_facturaf_numero']; ?>
            </td>
        	<td>
				<?php echo $rs_facturaf['TX_facturaf_ticket']; ?>
            </td>
            <td>
				<?php echo number_format($base_ni = round($rs_facturaf['TX_facturaf_subtotalni'],2)+round($rs_facturaf['TX_facturaf_subtotalci'],2),2); ?>
            </td>
            <td>
				<?php echo number_format($rs_facturaf['TX_facturaf_descuentoni']+$rs_facturaf['TX_facturaf_descuento'],2); ?>
            </td>
            <td>
				<?php echo number_format($rs_facturaf['TX_facturaf_impuesto'],2); ?>
            </td>
        		<td>
				<?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?>
            </td>
            <td><strong>B/
				<?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?>
			</strong></td>
		</tr>
        <?php
		$qry_datopago = mysql_query("SELECT datopago_AI_metododepago_id, TX_datopago_monto FROM bh_datopago WHERE datopago_AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'");
		$raw_pago=array();
		while($rs_datopago = mysql_fetch_array($qry_datopago)){
			$raw_pago[$rs_datopago['datopago_AI_metododepago_id']]=$rs_datopago['TX_datopago_monto'];
		};
		$count_pago=count($raw_pago);
		$td_empty=$nr_paymentmethod-$count_pago;
		?>
        <tr>
        <td colspan="10" style="padding:0;">
        <table id="tbl_datopago" class="table table-bordered table-print tbl-padding-0">
        <tbody>
        <tr>
        <?php for($i=0;$i<$td_empty;$i++){ ?>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">&nbsp;</td>
        <?php } ?>
				<?php foreach ($raw_pago as $metodo => $monto) { ?>
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo "<strong>".$raw_paymentmethod[$metodo]."</strong> ".number_format($monto,2); ?></td>
				<?php } ?>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
<!-- #################  ABONOS   #############################-->
<?php
				$qry_datodebito=mysql_query("SELECT bh_metododepago.TX_metododepago_value,bh_notadebito.TX_notadebito_numero, bh_datodebito.datodebito_AI_metododepago_id, bh_datodebito.TX_datodebito_monto, bh_datodebito.TX_datodebito_numero
			FROM ((((bh_datodebito
			INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datodebito.datodebito_AI_metododepago_id)
			INNER JOIN rel_facturaf_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = datodebito_AI_notadebito_id)
			INNER JOIN bh_facturaf ON rel_facturaf_notadebito.rel_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
			INNER JOIN bh_notadebito ON bh_notadebito.AI_notadebito_id = rel_facturaf_notadebito.rel_AI_notadebito_id)
			WHERE bh_facturaf.AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'");
				$raw_datodebito=array();
				$debito_numero="";
				while($rs_datodebito = mysql_fetch_array($qry_datodebito)){
					$raw_datodebito[$rs_datodebito['datodebito_AI_metododepago_id']]=$rs_datodebito['TX_datodebito_monto'];
					$debito_numero=$rs_datodebito['TX_notadebito_numero'];
				};
				$count_datodebito=count($raw_datodebito);
				$td_empty=$nr_paymentmethod-$count_datodebito;
				if(mysql_num_rows($qry_datodebito) > 0){
?>
				<tr>
				<td colspan="10" style="padding:0;">
				<table id="tbl_datodebito" class="table table-bordered table-print tbl-padding-0">
				<caption>Abono/N&deg; de Control: <?php echo $debito_numero; ?></caption>
				<tbody>
				<tr>
				<?php for($i=1;$i<$td_empty+1;$i++){ ?>
				<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">&nbsp;</td>
				<?php } ?>
				<?php foreach ($raw_datodebito as $metodo => $monto) { ?>
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo "<strong>".$raw_paymentmethod[$metodo]."</strong> ".number_format($monto,2); ?></td>
				<?php } ?>
				</tr>
				</tbody>
				</table>
				</td>
				</tr>

			<?php } }; ?>
 	</tbody>
    <tfoot style="border:solid; background-color:#DDDDDD; text-align: left;">
    <tr>
    <td></td><td></td><td></td><td></td>
    <td></td>
    <td colspan="2">
    	<strong>TOTAL:</strong><br />
		<?php
		echo "B/ ".number_format($total,2);
		?>
	</td>
    <td colspan="2">
    	<strong>SALDO:</strong><br />
		<?php
		echo "B/ ".number_format($saldo,2);
		?>

    </td>
    </tr>
    </tfoot>
	</table>
    </td>
</tr>
</table>
</body>
</html>
