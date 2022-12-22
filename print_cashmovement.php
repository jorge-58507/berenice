<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';

$raw_cajamenudaid=array();
if(!empty($_SESSION['efectivo_id'])){
	$raw_cajamenudaid[]=$_SESSION['efectivo_id'];
}else{
	$raw_cajamenudaid=explode(",",$_GET['a']);
}

?>
<?php
$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
?>
<?php
$txt_cajamenuda="SELECT bh_efectivo.AI_efectivo_id, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_efectivo.TX_efectivo_fecha,
bh_efectivo.TX_efectivo_status, bh_user.TX_user_seudonimo
FROM (bh_efectivo INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE";
$line_cmid="";
$length_raw=count($raw_cajamenudaid);
for($i=0;$i<$length_raw;$i++){
	if($i == $length_raw-1){
		$line_cmid.=" bh_efectivo.AI_efectivo_id = '$raw_cajamenudaid[$i]'";
	}else{
		$line_cmid.=" bh_efectivo.AI_efectivo_id = '$raw_cajamenudaid[$i]' OR";
	}
}
$txt_cajamenuda .= $line_cmid;
//echo $txt_cajamenuda;
$qry_cajamenuda=mysql_query($txt_cajamenuda);
$rs_cajamenuda=mysql_fetch_array($qry_cajamenuda);

$qry_cajamenuda_d=mysql_query($txt_cajamenuda);
$rs_cajamenuda_d=mysql_fetch_array($qry_cajamenuda_d);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Caja Menuda, TRILLI, S.A.</title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	// setTimeout()
</script>
</head>
<body style="font-family:Arial" onLoad="window.print(); setTimeout('self.close()',10000)">

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
    <h3><?php echo strtoupper($rs_cajamenuda['TX_efectivo_tipo']); ?> CAJA MENUDA</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:100%">
            <strong>Nombre: </strong><?php echo ucfirst($rs_cajamenuda['TX_user_seudonimo']); ?>
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
        	<th style="width:25%"><strong>Nº Movimiento</strong></th>
            <th style="width:25%"><strong>Fecha </strong></th>
            <th style="width:25%"><strong>Motivo</strong></th>
            <th style="width:25%"><strong>Monto </strong></th>
    	</tr>
        </thead>
        <tbody>
        <?php do{  ?>
        <tr>
        	<td>
            <?php echo $rs_cajamenuda['AI_efectivo_id']; ?>
            </td>
        	<td>
            <?php
			$prefecha=strtotime($rs_cajamenuda['TX_efectivo_fecha']);
			echo date('d-m-Y',$prefecha); ?>
            </td>
        	<td>
            <?php echo $rs_cajamenuda['TX_efectivo_motivo']; ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_cajamenuda['TX_efectivo_monto'],2); ?>
            </td>
        </tr>
        <?php }while($rs_cajamenuda=mysql_fetch_assoc($qry_cajamenuda)); ?>
        </tbody>
	</table>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
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
<?php echo $fecha_dia.",<br />"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:45px" align="center">
	<td valign="top" colspan="10">
    <h3><?php echo strtoupper($rs_cajamenuda_d['TX_efectivo_tipo']); ?> CAJA MENUDA</h3>
    </td>
</tr>
<tr style="height:58px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:12px;">
    	<tr>
        	<td style="width:100%">
            <strong>Nombre: </strong><?php echo ucfirst($rs_cajamenuda_d['TX_user_seudonimo']); ?>
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
        	<th style="width:25%"><strong>Nº Movimiento</strong></th>
            <th style="width:25%"><strong>Fecha </strong></th>
            <th style="width:25%"><strong>Motivo</strong></th>
            <th style="width:25%"><strong>Monto </strong></th>
    	</tr>
        </thead>
        <tbody>
        <?php do{  ?>
        <tr>
        	<td>
            <?php echo $rs_cajamenuda_d['AI_efectivo_id']; ?>
            </td>
        	<td>
            <?php
			$prefecha=strtotime($rs_cajamenuda_d['TX_efectivo_fecha']);
			echo date('d-m-Y',$prefecha); ?>
            </td>
        	<td>
            <?php echo $rs_cajamenuda_d['TX_efectivo_motivo']; ?>
            </td>
        	<td>
            <?php echo "$ ".number_format($rs_cajamenuda_d['TX_efectivo_monto'],2); ?>
            </td>
        </tr>
        <?php }while($rs_cajamenuda_d=mysql_fetch_assoc($qry_cajamenuda_d)); ?>
        </tbody>
	</table>
    </td>
</tr>
<tr style="height:88px">
	<td colspan="3">
	<td valign="bottom" colspan="4" style="text-align:center">
    <strong>_____________________________</strong><br />
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
unset($_SESSION['efectivo_id']);
?>
</body>
</html>
