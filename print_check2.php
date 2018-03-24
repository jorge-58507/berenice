<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
 	$qry_cheque = $link->query("SELECT bh_cheque.AI_cheque_id, bh_cheque.TX_cheque_numero, bh_cheque.TX_cheque_monto, bh_cheque.TX_cheque_montoletra, bh_cheque.TX_cheque_observacion, bh_proveedor.TX_proveedor_nombre
		FROM (bh_cheque INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cheque.cheque_AI_proveedor_id)
		WHERE AI_cheque_id = '{$_GET['a']}'")or die($link->error);
	if ($qry_cheque->num_rows < 1) {
		$qry_cheque = $link->query("SELECT bh_cheque.AI_cheque_id,bh_cheque.TX_cheque_numero,bh_cheque.TX_cheque_monto,bh_cheque.TX_cheque_montoletra,bh_cheque.TX_cheque_observacion, bh_proveedor.TX_proveedor_nombre
			FROM ((bh_cheque INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id)
			INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id)
			WHERE AI_cheque_id = '{$_GET['a']}'")or die($link->error);
	}
	$rs_cheque = $qry_cheque->fetch_array(MYSQLI_ASSOC);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cheque: <?php echo $rs_cheque['TX_cheque_numero']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>

<script type="text/javascript">
// function cap_fl(str){
// 	  return string.charAt(0).toUpperCase() + string.slice(1);
// }
//setTimeout("self.close()", 10000);
</script>

<body style="font-family:Arial" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>
<table cellpadding="0" cellspacing="0" border="0" style="height:585px; width:1263px; font-size:12pt;">
<tr style="height:11px">
<td width="10%"><img src="attached\image\scan.jpg" width='1263px' style="position: absolute; z-index: -2; margin-top: -2px; display: none;"></img></td>
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
<tr style="height:79px;" align="right">
	<td valign="top" colspan="10" class="optmayuscula" style="vertical-align: bottom;">
		<div style="width:848px; float:left;"> &nbsp;</div>
		<div style="width:345px; float:left; letter-spacing: 27px;"><?php echo date('dmY',strtotime($fecha)); ?></div>
  </td>
</tr>
<tr style="height:57px" align="center">
	<td valign="top" colspan="10" style="vertical-align:bottom;">
		<div style="width:191px; float:left;"> &nbsp;</div>
		<div style="width:692px; float:left; letter-spacing: 6px;">**<?php echo substr($rs_cheque['TX_proveedor_nombre'],0,41); ?>**</div>
		<div style="width:272px; float:left; padding-top:10px; letter-spacing: 6px; text-align:right;"><?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?></div>
  </td>
</tr>
<tr style="height:25px" align="center">
	<td valign="bottom" colspan="10">
		<div style="width:193px; float:left;"> &nbsp;</div>
		<div style="width:878px; float:left; letter-spacing: 6.2px;">**<?php echo $rs_cheque['TX_cheque_montoletra']; ?>**</div>
  </td>
</tr>
<tr style="height:230px">
	<td valign="top" colspan="10">&nbsp;</td>
</tr>
<tr style="height:188px">
	<td valign="top" colspan="10">
		<div style="width:193px; float:left; text-align:right; letter-spacing: 5px;"><?php echo date('d-M-y',strtotime($fecha_actual)); ?></div>
		<div style="width:629px; float:left; letter-spacing: 6px;">&nbsp;&nbsp;<?php echo $rs_cheque['TX_cheque_observacion']; ?></div>
		<div style="width:163px; float:left;">&nbsp;</div>
		<div style="width:181px; float:left; letter-spacing: 6px; text-align:right;"><?php echo $rs_cheque['TX_cheque_monto']; ?>&nbsp;&nbsp;</div>
  </td>
</tr>
</table>
</body>
</html>
