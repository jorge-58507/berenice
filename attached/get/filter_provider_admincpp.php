<?php
require '../../bh_conexion.php';
$link = conexion();

$value = $_GET['a'];
$fecha_i = date('Y-m-d', strtotime($_GET['b']));
$fecha_f = date('Y-m-d', strtotime($_GET['c']));

$raw_value = explode(' ', $value);
$txt_cpp="SELECT bh_cpp.AI_cpp_id,bh_cpp.TX_cpp_saldo,bh_cpp.TX_cpp_total,bh_cpp.TX_cpp_fecha, bh_proveedor.TX_proveedor_nombre, bh_proveedor.AI_proveedor_id FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id) WHERE ";
foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_cpp .= " TX_proveedor_nombre LIKE '%$value%' AND TX_cpp_fecha >= '$fecha_i' AND TX_cpp_fecha <= '$fecha_f' AND TX_cpp_status = 'ACTIVA'";
	}else {
		$txt_cpp .= " TX_proveedor_nombre LIKE '%$value%' AND TX_cpp_fecha >= '$fecha_i' AND TX_cpp_fecha <= '$fecha_f' AND TX_cpp_status = 'ACTIVA' OR";
	}
}
$qry_expired_cpp=$link->query($txt_cpp."ORDER BY TX_proveedor_nombre ASC");

$qry_cpp_facturacompra = $link->prepare("SELECT AI_facturacompra_id,TX_facturacompra_numero FROM (bh_facturacompra INNER JOIN bh_cpp ON bh_facturacompra.AI_facturacompra_id = bh_cpp.cpp_AI_facturacompra_id) WHERE AI_cpp_id = ?");
$qry_cpp_pedido = $link->prepare("SELECT AI_pedido_id,TX_pedido_numero FROM (bh_pedido INNER JOIN bh_cpp ON bh_pedido.AI_pedido_id = bh_cpp.cpp_AI_pedido_id) WHERE AI_cpp_id = ?");


while ($rs_expired_cpp = $qry_expired_cpp->fetch_array()) { ?>
	<tr>
		<td><?php echo $rs_expired_cpp['TX_cpp_fecha']; ?></td>
		<td><?php echo $rs_expired_cpp['TX_proveedor_nombre']; ?></td>
		<td><?php echo "B/ ".number_format($rs_expired_cpp['TX_cpp_total'],2); ?></td>
		<td><?php echo "B/ ".number_format($rs_expired_cpp['TX_cpp_saldo'],2); ?></td>
		<td><?php
			$qry_cpp_facturacompra->bind_param("i",$rs_expired_cpp['AI_cpp_id']); $qry_cpp_facturacompra->execute();
			$result = $qry_cpp_facturacompra->get_result(); $rs_cpp_facturacompra=$result->fetch_array(MYSQLI_ASSOC);
			if (!empty($rs_cpp_facturacompra['TX_facturacompra_numero'])) { ?>
				<a onclick="open_popup('popup_show_contentcpp.php?a=fc&b=<?php echo $rs_cpp_facturacompra['AI_facturacompra_id']; ?>','_popup','920','420'); return false;"><?php echo $rs_cpp_facturacompra['TX_facturacompra_numero']; ?></a>
<?php					}
			$qry_cpp_pedido->bind_param("i",$rs_expired_cpp['AI_cpp_id']); $qry_cpp_pedido->execute();
			$result = $qry_cpp_pedido->get_result(); $rs_cpp_pedido=$result->fetch_array(MYSQLI_ASSOC);
			if (!empty($rs_cpp_pedido['TX_pedido_numero'])) { ?>
				<a onclick="open_popup('popup_show_contentcpp.php?a=oc&b=<?php echo $rs_cpp_pedido['AI_pedido_id']; ?>','_popup','920','420'); return false;"><?php echo $rs_cpp_pedido['TX_pedido_numero']; ?></a>
<?php					}
		 ?></td>
		 <td class="al_center">
			 <button type="button" class="btn btn-info btn-sm" onclick="document.location.href='provider_info.php?a=<?php echo $rs_expired_cpp['AI_proveedor_id']; ?>'"><i class="fa fa-search" aria-hidden="true"></i></button>
		 </td>
	</tr>
<?php 	} ?>
