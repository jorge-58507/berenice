<?php
require '../../bh_conexion.php';
$link = conexion();

$value = $_GET['a'];
$proveedor_id = $_GET['b'];
$fecha_i = date('Y-m-d',strtotime($_GET['c']));
$fecha_f = date('Y-m-d',strtotime($_GET['d']));

$raw_value = explode(' ', $value);
$txt_fc="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_facturacompra.TX_facturacompra_ordendecompra, bh_facturacompra.TX_facturacompra_status FROM bh_facturacompra WHERE ";
foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_fc .= " TX_facturacompra_numero LIKE '%$value%' AND TX_facturacompra_fecha >= '$fecha_i' AND TX_facturacompra_fecha <= '$fecha_f' OR";
	}else {
		$txt_fc .= " TX_facturacompra_numero LIKE '%$value%' AND TX_facturacompra_fecha >= '$fecha_i' AND TX_facturacompra_fecha <= '$fecha_f' OR";
	}
}

foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_fc .= " TX_facturacompra_ordendecompra LIKE '%$value%' AND TX_facturacompra_fecha >= '$fecha_i' AND TX_facturacompra_fecha <= '$fecha_f' ";
	}else {
		$txt_fc .= " TX_facturacompra_ordendecompra LIKE '%$value%' AND TX_facturacompra_fecha >= '$fecha_i' AND TX_facturacompra_fecha <= '$fecha_f' OR";
	}
}
$qry_facturacompra=$link->query($txt_fc." ORDER BY TX_facturacompra_fecha DESC")or die($link->error);

$qry_saldo = $link->prepare("SELECT AI_facturacompra_id, TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento FROM (bh_facturacompra INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) WHERE AI_facturacompra_id = ?") or die($link->error);

while ($rs_facturacompra=$qry_facturacompra->fetch_array()) {
	$saldo_total=0;
	$qry_saldo->bind_param('i', $rs_facturacompra['AI_facturacompra_id']);
	$qry_saldo->execute();
	$result = $qry_saldo->get_result();
	while ($rs_saldo = $result->fetch_array()) {
		$descuento = ($rs_saldo['TX_datocompra_descuento']*$rs_saldo['TX_datocompra_precio'])/100;
		$precio_descuento = $rs_saldo['TX_datocompra_precio']-$descuento;
		$impuesto = ($rs_saldo['TX_datocompra_impuesto']*$precio_descuento)/100;
		$precio_impuesto = $precio_descuento+$impuesto;
		$precio_producto = $precio_impuesto *$rs_saldo['TX_datocompra_cantidad'];
		$saldo_total += $precio_producto;
	}
?>
	<tr>
		<td><?php echo date('d-m-Y',strtotime($rs_facturacompra['TX_facturacompra_fecha'])); ?></td>
		<td><?php echo $rs_facturacompra['TX_facturacompra_numero'] ?></td>
		<td><?php echo $rs_facturacompra['TX_facturacompra_ordendecompra'] ?></td>
		<td><?php echo number_format($saldo_total,2) ?></td>
	</tr>
<?php } ?>
