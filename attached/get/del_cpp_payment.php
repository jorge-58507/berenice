<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$payment_id=$_GET['a'];

$qry_cpp = $link->query("SELECT AI_cpp_id FROM (bh_cpp INNER JOIN bh_datocpp ON bh_cpp.AI_cpp_id = bh_datocpp.datocpp_AI_cpp_id) WHERE AI_datocpp_id = '$payment_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array();

$link->query("DELETE FROM bh_datocpp WHERE AI_datocpp_id = '$payment_id'")or die($link->error);

// ######################### ANSWER

$qry_datocpp = $link->query("SELECT bh_user.TX_user_seudonimo, bh_datocpp.AI_datocpp_id,bh_datocpp.TX_datocpp_monto,bh_datocpp.TX_datocpp_numero,bh_datocpp.TX_datocpp_fecha,bh_datocpp.datocpp_AI_metododepago_id FROM (bh_datocpp INNER JOIN bh_user ON bh_user.AI_user_id = bh_datocpp.datocpp_AI_user_id) WHERE datocpp_AI_cpp_id =	'{$rs_cpp['AI_cpp_id']}'")or die($link->error);

$metododepago = ['','Efectivo','Cheque','T. de Cr&eacute;dito','T. Clave','','','Nota de Cr&eacute;dito','Otro'];
while ($rs_datocpp = $qry_datocpp->fetch_array()) {	?>
	<tr>
		<td><?php echo $rs_datocpp['TX_datocpp_fecha']; ?></td>
		<td><?php echo $metododepago[$rs_datocpp['datocpp_AI_metododepago_id']]; ?></td>
		<td><?php echo $rs_datocpp['TX_datocpp_numero']; ?></td>
		<td><?php echo $rs_datocpp['TX_datocpp_monto']; ?></td>
		<td><?php echo $rs_datocpp['TX_user_seudonimo']; ?></td>
		<td><button type="button" class="btn btn-danger btn-sm" onclick="del_cpp_payment('<?php echo $rs_datocpp['AI_datocpp_id']; ?>')"><i class="fa fa-times"></i></button></td>
	</tr>
<?php 	} ?>
