<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$factura_numero=$_GET['a'];

$qry_titulo=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'TITULO'", $link);
$rs_titulo=mysql_fetch_array($qry_titulo);
$titulo=$rs_titulo['TX_opcion_value'];

$qry_ruc=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'RUC'", $link);
$rs_ruc=mysql_fetch_array($qry_ruc);
$ruc=$rs_ruc['TX_opcion_value'];

$qry_dv=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'DV'", $link);
$rs_dv=mysql_fetch_array($qry_dv);
$dv=$rs_dv['TX_opcion_value'];

$qry_direccion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'DIRECCION'", $link);
$rs_direccion=mysql_fetch_array($qry_direccion);
$direccion=$rs_direccion['TX_opcion_value'];

$qry_telefono=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'TELEFONO'", $link);
$rs_telefono=mysql_fetch_array($qry_telefono);
$telefono=$rs_telefono['TX_opcion_value'];

$qry_fax=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'FAX'", $link);
$rs_fax=mysql_fetch_array($qry_fax);
$fax=$rs_fax['TX_opcion_value'];

$qry_email=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'EMAIL'", $link);
$rs_email=mysql_fetch_array($qry_email);
$email=$rs_email['TX_opcion_value'];

$client_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

$qry_client=mysql_query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$rs_client=mysql_fetch_array($qry_client);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
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
<font style="font-size:10px">RUC: <?php echo $rs_ruc['TX_opcion_value']; ?> DV: <?php echo $rs_dv['TX_opcion_value']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $rs_direccion['TX_opcion_value']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $rs_telefono['TX_opcion_value']." "
.$rs_fax['TX_opcion_value']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $rs_email['TX_opcion_value']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo $fecha; ?>
    </td>
</tr>
<tr style="height:21px" align="center">
	<td valign="top" colspan="10">
    </td>
</tr>
<tr style="height:119px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table table-bordered">
		<tbody style="border:solid; background-color:#dddddd">
    	<tr>
        	<td valign="top" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?>
            </td>
            <td valign="top"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?>
            </td>
            <td valign="top"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top" colspan="2">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_client['TX_cliente_direccion']); ?>
            </td>
            <td ><?php echo "<strong>Desde:</strong> ".$date_i." <strong>Hasta:</strong> ".$date_f; ?></td>
        </tr>
        </tbody>
    </table>
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
        <tr>
        	<td valign="top" style="text-align:center;">
            <h4>ABONOS A CREDITOS</h4>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr style="height:695px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_notadebito" class="table table-striped table-bordered">
    <thead style="border:solid; background-color:#DDDDDD">
    	<tr>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<strong>FECHA</strong>
            </th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <strong>HORA</strong>
            </th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <strong>N&Uacute;MERO</strong>
            </th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>FACTURA</strong>
            </th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <strong>TOTAL</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>CAMBIO</strong>
            </th>
		</tr>
	</thead>
    <tfoot></tfoot>
    <tbody>
	    <?php
			$date_i=date('Y-m-d',strtotime($date_i));
			$date_f=date('Y-m-d',strtotime($date_f));
			$txt_notadebito="SELECT AI_notadebito_id, TX_notadebito_fecha, TX_notadebito_hora, TX_notadebito_numero, TX_notadebito_total, TX_notadebito_cambio FROM bh_notadebito WHERE notadebito_AI_cliente_id = '$client_id' AND TX_notadebito_fecha >= '$date_i' AND TX_notadebito_fecha <= '$date_f'";
			$qry_notadebito=mysql_query($txt_notadebito)or die (mysql_error());

			$total=0;
		?>
        <?php while($rs_notadebito=mysql_fetch_array($qry_notadebito)){

			$qry_ff=mysql_query("SELECT TX_facturaf_numero
			FROM ((bh_notadebito
			INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id = rel_facturaf_notadebito.rel_AI_notadebito_id)
			INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id) WHERE AI_notadebito_id = '{$rs_notadebito['AI_notadebito_id']}'")or die(mysql_error());
			$ff="";
			while($rs_ff=mysql_fetch_array($qry_ff)){	$ff .=	$rs_ff[0]." ";	}

			$qry_datodebito=mysql_query("SELECT bh_datodebito.datodebito_AI_metododepago_id, bh_datodebito.TX_datodebito_monto FROM bh_datodebito WHERE bh_datodebito.datodebito_AI_notadebito_id = '{$rs_notadebito['AI_notadebito_id']}'");
			$raw_datodebito=array();
			$i=0;$efectivo=0;$tarjeta=0;$cheque=0;
			while($rs_datodebito=mysql_fetch_assoc($qry_datodebito)){
		if($rs_datodebito['datodebito_AI_metododepago_id'] == 1){ $efectivo=$rs_datodebito['TX_datodebito_monto']; }
		if($rs_datodebito['datodebito_AI_metododepago_id'] == 2){ $tarjeta=$rs_datodebito['TX_datodebito_monto']; }
		if($rs_datodebito['datodebito_AI_metododepago_id'] == 3){ $cheque=$rs_datodebito['TX_datodebito_monto']; }
			}
			$total+=round($rs_notadebito['TX_notadebito_total'],2);
		?>

    	<tr style="height:30px;">
        	<td>
				<?php echo $rs_notadebito['TX_notadebito_fecha']; ?>
            </td>
            <td>
				<?php echo $rs_notadebito['TX_notadebito_hora']; ?>
            </td>
            <td>
				<?php echo $rs_notadebito['TX_notadebito_numero']; ?>
            </td>
            <td>
				<?php echo $ff; ?>
            </td>
            <td>
				<?php
					echo number_format($rs_notadebito['TX_notadebito_total'],2);
				?>
            </td>
            <td>
				<?php
					if($rs_notadebito['TX_notadebito_cambio'] != ""){
					echo number_format($rs_notadebito['TX_notadebito_cambio'],2);
					}else{
						echo number_format("0",2);
					}
				?>
            </td>
		</tr>
        <tr>
        	<td></td>
        	<td><strong>Efectivo:</strong> <?php echo number_format($efectivo,2); ?></td>
        	<td><strong>Tarjeta:</strong> <?php echo number_format($tarjeta,2); ?></td>
        	<td><strong>Cheque:</strong> <?php echo number_format($cheque,2); ?></td>
        	<td></td>
        	<td></td>
        </tr>
        <?php }; ?>
 	</tbody>
    <tfoot style="border:solid; background-color:#DDDDDD">
    <tr>
    <td></td><td></td><td></td><td></td>
    <td style="text-align:center;">
    	<strong>TOTAL:</strong><br />
		<?php
		echo "B/ ".number_format($total,2);
		?>
	</td>
    <td></td>
    </tr>
    </tfoot>
	</table>
    </td>
</tr>
</table>
</body>
</html>
