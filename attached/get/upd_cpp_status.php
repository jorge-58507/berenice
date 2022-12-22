<?php
require '../../bh_conexion.php';
$link = conexion();

$cpp_id=$_GET['a'];
$fecha_actual = date('Y-m-d');
$qry_datocpp = $link->prepare("SELECT AI_datocpp_id FROM bh_datocpp WHERE datocpp_AI_cpp_id = ?")or die($link->error);
$qry_cheque = $link->prepare("SELECT AI_cheque_id FROM bh_cheque WHERE cheque_AI_cpp_id = ?")or die($link->error);
$ins_datocpp = $link->prepare("INSERT INTO bh_datocpp (TX_datocpp_monto,TX_datocpp_numero,TX_datocpp_fecha,datocpp_AI_cpp_id,datocpp_AI_user_id,datocpp_AI_metododepago_id) VALUES (?,?,'$fecha_actual','$cpp_id','{$_COOKIE['coo_iuser']}','2')")or die($link->error);
$qry_cpp = $link->query("SELECT AI_cpp_id, cpp_AI_proveedor_id, TX_cpp_saldo FROM bh_cpp WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array(MYSQLI_ASSOC);

$qry_datocpp = $link->query("SELECT TX_datocpp_monto FROM bh_datocpp WHERE datocpp_AI_cpp_id =	'$cpp_id'")or die($link->error);
$total_datocpp = 0;
while ($rs_datocpp = $qry_datocpp->fetch_array(MYSQLI_ASSOC)) {
	$total_datocpp += $rs_datocpp['TX_datocpp_monto'];
}
$cpp_saldo = $rs_cpp['TX_cpp_saldo']*1.00;

$qry_datocheck = $link->query("SELECT AI_cheque_id, TX_cheque_monto, TX_cheque_numero FROM bh_cheque WHERE cheque_AI_cpp_id = '$cpp_id'")or die($link->error);
$total_check = 0;	$raw_check = array();
while ($rs_datocheck = $qry_datocheck->fetch_array(MYSQLI_ASSOC)) {
	$raw_check[] = $rs_datocheck;
	$total_check += $rs_datocheck['TX_cheque_monto'];
}
echo "saldo: ".gettype($cpp_saldo)." cheque: ".gettype($total_check);
if ($total_check === $cpp_saldo) {
	echo "son iguales";
	$ins_datocpp->bind_param('ds',$check_amount,$check_number);
	foreach ($raw_check as $key => $rs_check) {
		$check_amount = $rs_check['TX_cheque_monto'];
		$check_number = $rs_check['TX_cheque_numero'];
		$ins_datocpp->execute();
	}
}

// $qry_cpp = $link->query("SELECT AI_cpp_id, cpp_AI_proveedor_id FROM bh_cpp WHERE AI_cpp_id = '$cpp_id' AND TX_cpp_saldo > 0")or die($link->error);
echo 'finish';
return false;
if ($qry_cpp->num_rows < 1) {
	$link->query("UPDATE bh_cpp SET TX_cpp_status = 'CANCELADA' WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
}

$qry_provider=$link->query("SELECT bh_proveedor.AI_proveedor_id FROM (bh_proveedor INNER JOIN bh_cpp ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id) WHERE bh_cpp.AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_provider=$qry_provider->fetch_array();
$proveedor_id = $rs_provider['AI_proveedor_id'];
$qry_cpp = $link->query("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo, bh_cpp.TX_cpp_fecha FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id) WHERE cpp_AI_proveedor_id = '$proveedor_id' AND TX_cpp_status = 'ACTIVA' ORDER BY TX_cpp_fecha DESC") or die($link->error);
while ($rs_cpp=$qry_cpp->fetch_array()) {
?>
	<tr>
		<td><?php echo date('d-m-Y',strtotime($rs_cpp['TX_cpp_fecha'])); ?></td>
		<td class="al_center"><?php echo "B/ ".number_format($rs_cpp['TX_cpp_total'],2); ?></td>
		<td class="al_center"><?php echo "B/ ".number_format($rs_cpp['TX_cpp_saldo'],2); ?></td>
<?php
$qry_facturanumero=$link->query("SELECT TX_facturacompra_numero FROM (bh_facturacompra INNER JOIN bh_cpp ON bh_facturacompra.AI_facturacompra_id = bh_cpp.cpp_AI_facturacompra_id) WHERE AI_cpp_id = '{$rs_cpp['AI_cpp_id']}' ")or die($link->error);
$rs_facturanumero = $qry_facturanumero->fetch_array();
$qry_pedido=$link->query("SELECT TX_pedido_numero FROM (bh_pedido INNER JOIN bh_cpp ON bh_pedido.AI_pedido_id = bh_cpp.cpp_AI_pedido_id) WHERE AI_cpp_id = '{$rs_cpp['AI_cpp_id']}' ")or die($link->error);
$rs_pedido = $qry_pedido->fetch_array();
?>
		<td><?php
		if (!empty($rs_facturanumero[0])) {	echo "<em>".$rs_facturanumero[0]."</em>"; }
		if (!empty($rs_pedido[0])) {	echo "<strong>".$rs_pedido[0]."</strong>"; }
		?></td>
		<td class=" al_center"><button type="button" class="btn btn-success btn-sm" onclick="document.location.href='admin_pay_cpp.php?a=<?php echo $rs_cpp['AI_cpp_id']; ?>'"><i class="fa fa-money" aria-hidden="true"></i></button></td>
		<td class=" al_center"><button type="button" class="btn btn-info btn-sm" onclick="open_popup('popup_cpp_confirm.php?a=<?php echo $rs_cpp['AI_cpp_id']; ?>','_popup','580','420')"><i class="fa fa-check" aria-hidden="true"></i></button></td>
		<td class=" al_center"><?php
		$qry_datocpp->bind_param("i", $rs_cpp['AI_cpp_id']); $qry_datocpp->execute(); $result_datocpp = $qry_datocpp->get_result();
		$qry_cheque->bind_param("i", $rs_cpp['AI_cpp_id']); $qry_cheque->execute(); $result_cheque = $qry_cheque->get_result();
		if ($result_datocpp->num_rows < 1 && $result_cheque->num_rows < 1) { ?>
			<button type="button" class="btn btn-danger btn-sm" onclick="del_provider_cpp('<?php echo $rs_cpp['AI_cpp_id']; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
<?php		}
		?></td>
	</tr>
<?php } ?>
