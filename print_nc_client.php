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

$client_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

$qry_client=mysql_query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$rs_client=mysql_fetch_array($qry_client);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nota de Credito: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" >
<!-- onLoad="window.print()"  -->
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
<tr style="height:91px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%;
font-size: 12px;
border: solid;
border-radius: 3px;
border-bottom-right-radius: 0;
border-bottom-left-radius: 0;
">
		<tbody style="background-color:#dddddd">
    	<tr>
        	<td valign="top" style="width:50%">
            <strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?>
            </td>
            <td valign="top" style="width:20%">
            <strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?>
            </td>
            <td valign="top" style="width:30%">
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
			<h4>NOTAS DE CR&Eacute;DITO</h4><br />
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr style="height:724px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_notadebito" class="table table-striped table-bordered">
    <thead style="border:solid; background-color:#DDDDDD">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<strong>FECHA</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>HORA</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>NUMERO</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>TICKET</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>RET.</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>MONTO</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>SALDO</strong>
            </th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>MOTIVO</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>FACTURA</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>TICKET</strong>
            </th>
		</tr>
	</thead>
    <tfoot></tfoot>
    <tbody>
	    <?php
		$date_i=date('Y-m-d',strtotime($date_i));
		$date_f=date('Y-m-d',strtotime($date_f));
			$txt_notadecredito="SELECT bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_hora, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_destino, bh_notadecredito.TX_notadecredito_retencion, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto) as total, bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.TX_notadecredito_motivo, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket FROM (bh_notadecredito INNER JOIN bh_facturaf ON bh_notadecredito.notadecredito_AI_facturaf_id = bh_facturaf.AI_facturaf_id) WHERE notadecredito_AI_cliente_id = '$client_id' AND TX_notadecredito_fecha >= '$date_i' AND TX_notadecredito_fecha <= '$date_f'";
			$qry_notadecredito=mysql_query($txt_notadecredito);
			$total=0;
			$saldo=0;
		?>
        <?php while($rs_notadecredito=mysql_fetch_array($qry_notadecredito)){
			$total+=round($rs_notadecredito['total'],2);
			$saldo+=round($rs_notadecredito['TX_notadecredito_exedente'],2);
		?>

    	<tr style="height:30px;">
        	<td>
				<?php echo $rs_notadecredito['TX_notadecredito_fecha']; ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_notadecredito_hora']; ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_notadecredito_numero']; ?>
            </td>
        	<td>
				<?php echo $rs_notadecredito['TX_notadecredito_destino']; ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_notadecredito_retencion']."%"; ?>
            </td>
            <td>
				<?php echo number_format($rs_notadecredito['total'],2); ?>
            </td>
        	<td>
				<?php echo number_format($rs_notadecredito['TX_notadecredito_exedente'],2); ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_notadecredito_motivo']; ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_facturaf_numero']; ?>
            </td>
            <td>
				<?php echo $rs_notadecredito['TX_facturaf_ticket']; ?>
            </td>
		</tr>
        <?php }; ?>
 	</tbody>
    <tfoot style="border:solid; background-color:#DDDDDD">
    <tr>
    <td></td><td></td><td></td><td></td><td></td>
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
