<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';
?>
<?php
$notadecredito_id=$_GET['a'];

$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$qry_user=mysql_query("SELECT TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'");
$rs_user=mysql_fetch_array($qry_user);
?>
<?php 
$txt_nc="SELECT bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_ticket, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto) as total, bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.TX_notadecredito_retencion, bh_notadecredito.TX_notadecredito_motivo, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono
FROM ((bh_notadecredito 
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_cliente ON bh_notadecredito.notadecredito_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_notadecredito.AI_notadecredito_id = '$notadecredito_id'";
$qry_nc=mysql_query($txt_nc);
$rs_nc=mysql_fetch_assoc($qry_nc);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recibo: <?php echo $rs_nc['TX_cliente_nombre']?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<body style="font-family:Arial" onLoad="window.print()">

<?php 
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number]; 
?>
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
    <h3>RECIBO DE NOTA DE CR&Eacute;DITO</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:45%">
            <strong>Nombre: </strong><br /><?php echo ucfirst($rs_nc['TX_cliente_nombre']); ?>
            </td>
            <td style="width:25%">
            <strong>RUC: </strong><br /><?php echo $rs_nc['TX_cliente_cif']; ?>
            </td>
            <td style="width:25%">
            <strong>Telefono: </strong><br /><?php echo $rs_nc['TX_cliente_telefono']; ?>
            </td>
    	</tr>
	</table>
    </td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
    <table id="tbl_notadecredito" class="table table-bordered table-striped">
        <tbody>
        <tr>
        	<td><strong>FECHA</strong></td>
        	<td>
            <?php echo date('d-m-Y', strtotime($rs_nc['TX_notadecredito_fecha'])); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>NOTA DE CR&Eacute;DITO N&deg;</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_numero']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>NOTA DE CR&Eacute;DITO TICKET</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_ticket']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>RETENCI&Oacute;N</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_retencion']."%"; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>MONTO</strong></td>
        	<td>
            <?php echo number_format($rs_nc['total'],2); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>SALDO</strong></td>
        	<td>
            <?php echo number_format($rs_nc['TX_notadecredito_exedente'],2); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>MOTIVO</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_motivo']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>FACTURA</strong></td>
        	<td>
            <?php echo $rs_nc['TX_facturaf_numero']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>TICKET</strong></td>
        	<td>
            <?php echo $rs_nc['TX_facturaf_ticket']; ?>
            </td>
        </tr>
        </tbody>
	</table>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################        FIN LADO IZQUIERDO   ######################### --->
</td>
<td style="width:50%;">
<!-- ###############################        LADO DERECHO   ######################### --->
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
    <h3>RECIBO DE NOTA DE CR&Eacute;DITO</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:45%">
            <strong>Nombre: </strong><br /><?php echo ucfirst($rs_nc['TX_cliente_nombre']); ?>
            </td>
            <td style="width:25%">
            <strong>RUC: </strong><br /><?php echo $rs_nc['TX_cliente_cif']; ?>
            </td>
            <td style="width:25%">
            <strong>Telefono: </strong><br /><?php echo $rs_nc['TX_cliente_telefono']; ?>
            </td>
    	</tr>
	</table>
    </td>
</tr>
<tr style="height:445px">
	<td valign="top" colspan="10" class="optmayuscula" style="line-height:3;">
    <div style="height:375px; width:375px; margin:0px 56px 0px 56px; position:absolute; background:url(attached/image/logo_factura.png) no-repeat; opacity:0.2;"> </div>
    <table id="tbl_notadecredito" class="table table-bordered table-striped">
        <tbody>
        <tr>
        	<td><strong>FECHA</strong></td>
        	<td>
            <?php echo date('d-m-Y', strtotime($rs_nc['TX_notadecredito_fecha'])); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>NOTA DE CR&Eacute;DITO N&deg;</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_numero']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>NOTA DE CR&Eacute;DITO TICKET</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_ticket']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>RETENCI&Oacute;N</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_retencion']."%"; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>MONTO</strong></td>
        	<td>
            <?php echo number_format($rs_nc['total'],2); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>SALDO</strong></td>
        	<td>
            <?php echo number_format($rs_nc['TX_notadecredito_exedente'],2); ?>
            </td>
        </tr>
        <tr>
        	<td><strong>MOTIVO</strong></td>
        	<td>
            <?php echo $rs_nc['TX_notadecredito_motivo']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>FACTURA</strong></td>
        	<td>
            <?php echo $rs_nc['TX_facturaf_numero']; ?>
            </td>
        </tr>
        <tr>
        	<td><strong>TICKET</strong></td>
        	<td>
            <?php echo $rs_nc['TX_facturaf_ticket']; ?>
            </td>
        </tr>
        </tbody>
	</table>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    </td>
    <td colspan="3">
</tr>
</table>
<!-- ###############################      FIN LADO DERECHO   ######################### --->
</td>
</tr>
</table>
<?php
unset($_SESSION['arqueo_id']);
?>
</body>
</html>