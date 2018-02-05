<?php
require '../../bh_conexion.php';
$link=conexion();
require '../php/req_login_admin.php';
$fecha_actual = date('Y-m-d');
$proveedor_id = $_GET['a'];

$qry_datocpp = $link->prepare("SELECT AI_datocpp_id FROM bh_datocpp WHERE datocpp_AI_cpp_id = ?")or die($link->error);
$qry_cheque = $link->prepare("SELECT AI_cheque_id FROM bh_cheque WHERE cheque_AI_cpp_id = ?")or die($link->error);

$qry_cpp= $link->query("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo, bh_cpp.TX_cpp_fecha FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = cpp_AI_proveedor_id) WHERE cpp_AI_proveedor_id = '$proveedor_id' AND TX_cpp_status = 'ACTIVA' AND bh_cpp.TX_cpp_fecha <= '$fecha_actual' ORDER BY TX_cpp_fecha DESC") or die($link->error);

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
