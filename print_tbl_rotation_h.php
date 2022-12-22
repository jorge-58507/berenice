<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';
?>
<?php
$date_i=date('Y-m',strtotime($_GET['a']));
$date_i=date('Y-m-d',strtotime($date_i));
$date_f=date('Y-m',strtotime($_GET['b']));
$date_f=date('Y-m-d',strtotime($date_f));
$product_id=$_GET['c'];

$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
?>
<?php
$qry_product=mysql_query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_referencia, TX_producto_value, TX_producto_minimo, TX_producto_maximo FROM bh_producto WHERE AI_producto_id = '$product_id'");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recibo: <?php echo $rs_facturaf['TX_cliente_nombre']?></title>
</head>
<body style="font-family:Arial" onLoad="window.print()">

<?php 
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number]; 
?>
<table align="center" cellpadding="0" cellspacing="0" border="0" style="height: 760px;width: 1001px;transform: rotsate(90deg);
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
        <?php do{  ?>
        <tr>
        	<td>
            <?php echo $rs_facturaf['TX_facturaf_ticket']; ?>
            </td>
        	<td>
            <?php 
			$prefecha=strtotime($rs_facturaf['TX_facturaf_fecha']);
			echo date('d-m-Y',$prefecha); ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_facturaf['TX_facturaf_total'],2); ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_facturaf['TX_facturaf_deficit'],2); ?>
            </td>
        </tr>
        <?php }while($rs_facturaf=mysql_fetch_assoc($qry_facturaf)); ?>
        </tbody>
	</table>
	<p>
    <strong>Efectivo:</strong> B/ <?php echo number_format($total_efectivo+$cambio,2); ?>&nbsp;
    <strong>Tarjeta:</strong> B/ <?php echo number_format($total_tarjeta,2); ?>&nbsp;
    <strong>Cheque:</strong> B/ <?php echo number_format($total_cheque,2); ?>&nbsp;
    </p>
    <strong>Total:</strong> B/ <?php echo number_format($total_total,2); ?><br />
    <strong>Cambio:</strong> B/ <?php echo $cambio; ?><br />

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
        	<th style="width:25%">
			<strong>Nº Factura</strong>
			</th>
            <th style="width:25%">
            <strong>Fecha</strong>
            </th>
            <th style="width:25%">
            <strong>Total: </strong>
            </th>
            <th style="width:25%">
            <strong>Saldo: </strong>
            </th>
    	</tr>
        </thead>
        <tbody>
        <?php do{  ?>
        <tr>
        	<td>
            <?php echo $rs_facturaf_d['TX_facturaf_ticket']; ?>
            </td>
        	<td>
            <?php 
			$prefecha=strtotime($rs_facturaf_d['TX_facturaf_fecha']);
			echo date('d-m-Y',$prefecha); ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_facturaf_d['TX_facturaf_total'],2); ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_facturaf_d['TX_facturaf_deficit'],2); ?>
            </td>
        </tr>
        <?php }while($rs_facturaf_d = mysql_fetch_array($qry_facturaf_d)); ?>
        </tbody>
	</table>
	<p>
    <strong>Efectivo:</strong> B/ <?php echo number_format($total_efectivo+$cambio,2); ?>&nbsp;
    <strong>Tarjeta:</strong> B/ <?php echo number_format($total_tarjeta,2); ?>&nbsp;
    <strong>Cheque:</strong> B/ <?php echo number_format($total_cheque,2); ?>&nbsp;
    </p>
    <strong>Total:</strong> B/ <?php echo number_format($total_total,2); ?><br />
    <strong>Cambio:</strong> B/ <?php echo number_format($cambio,2); ?><br />
   

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
unset($_SESSION['arqueo_id']);
?>
</body>
</html>