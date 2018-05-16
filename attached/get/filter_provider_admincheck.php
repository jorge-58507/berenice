<?php
require '../../bh_conexion.php';
$link = conexion();

$value = $_GET['a'];
$fecha_i = date('Y-m-d', strtotime($_GET['b']));
$fecha_f = date('Y-m-d', strtotime($_GET['c']));

$raw_value = explode(' ', $value);
$txt_cheque="SELECT bh_cheque.AI_cheque_id, bh_cheque.TX_cheque_fecha, bh_proveedor.TX_proveedor_nombre, bh_cheque.cheque_AI_cpp_id, bh_cheque.TX_cheque_numero, bh_cheque.TX_cheque_monto, bh_cheque.TX_cheque_observacion, bh_cheque.cheque_AI_proveedor_id FROM (bh_cheque  INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cheque.cheque_AI_proveedor_id) WHERE";
foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_cheque .= " TX_proveedor_nombre LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f' OR";
	}else {
		$txt_cheque .= " TX_proveedor_nombre LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f' OR";
	}
}

foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_cheque .= " TX_cheque_numero LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f' OR";
	}else {
		$txt_cheque .= " TX_cheque_numero LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f' OR";
	}
}

foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_cheque .= " TX_cheque_observacion LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f'";
	}else {
		$txt_cheque .= " TX_cheque_observacion LIKE '%$value%' AND TX_cheque_fecha >= '$fecha_i' AND TX_cheque_fecha <= '$fecha_f' OR";
	}
}
$qry_cheque=$link->query($txt_cheque."ORDER BY TX_cheque_fecha DESC, TX_proveedor_nombre ASC")or die($link->error);

while($rs_cheque = $qry_cheque->fetch_array()){ ?>
	<tr>
		<td><?php echo date('d-m-Y',strtotime($rs_cheque['TX_cheque_fecha'])); ?></td>
		<td><button type="button" class="btn btn-link" onclick="document.location='provider_info.php?a=<?php echo $rs_cheque['cpp_AI_proveedor_id']; ?>'"><?php echo $rs_cheque['TX_proveedor_nombre']; ?></button></td>
		<td class="al_center"><button type="button" class="btn btn-link" onclick="document.location='admin_pay_cpp.php?a=<?php echo $rs_cheque['cheque_AI_cpp_id']; ?>'"><?php echo substr("0000000".$rs_cheque['cheque_AI_cpp_id'],-8); ?></button></td>
		<td><?php echo $rs_cheque['TX_cheque_numero']; ?></td>
		<td>B/ <?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?></td>
		<td><?php echo $r_function->replace_special_character($rs_cheque['TX_cheque_observacion']); ?></td>
		<td class="al_center"><button type="button" class="btn btn-info btn-sm" onclick="print_html('print_check.php?a=<?php echo $rs_cheque['AI_cheque_id']; ?>')"><i class="fa fa-search"></i></button></td>
	</tr>
<?php } ?>
