<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$proveedor_id=$_GET['a'];

	$bh_del="DELETE FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'";
	$link->query($bh_del) or die($link->error);

// ############################# ANSWER ####################

$qry_proveedor = $link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion, TX_proveedor_telefono FROM bh_proveedor ORDER BY TX_proveedor_nombre ASC LIMIT 10");

$qry_saldo = $link->prepare("SELECT AI_facturacompra_id, TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento FROM (bh_facturacompra INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) WHERE facturacompra_AI_proveedor_id = ? AND TX_facturacompra_status = 'POR PAGAR'") or die($link->error);

while ($rs_proveedor = $qry_proveedor->fetch_array()){
	$saldo_total=0;
	$doc_counter=0;
	$last_doc =	"";

	$qry_saldo->bind_param('i', $rs_proveedor['AI_proveedor_id']);
	$qry_saldo->execute();
	$result = $qry_saldo->get_result();
	while ($rs_saldo = $result->fetch_array()) {
		if ($rs_saldo['AI_facturacompra_id'] != $last_doc) {
			$doc_counter++;
			$last_doc=$rs_saldo['AI_facturacompra_id'];
		}

		$descuento = ($rs_saldo['TX_datocompra_descuento']*$rs_saldo['TX_datocompra_precio'])/100;
		$precio_descuento = $rs_saldo['TX_datocompra_precio']-$descuento;
		$impuesto = ($rs_saldo['TX_datocompra_impuesto']*$precio_descuento)/100;
		$precio_impuesto = $precio_descuento+$impuesto;
		$precio_producto = $precio_impuesto *$rs_saldo['TX_datocompra_cantidad'];
		$saldo_total += $precio_producto;
	}

?>
<tr>
	<td><?php echo $rs_proveedor['TX_proveedor_nombre']; ?></td>
	<td><?php echo $rs_proveedor['TX_proveedor_cif']; ?></td>
	<td><?php echo $rs_proveedor['TX_proveedor_telefono']; ?></td>
	<td><?php echo $rs_proveedor['TX_proveedor_direccion']; ?></td>
	<td><?php echo $doc_counter; ?></td>
	<td><?php echo number_format($saldo_total,2); ?></td>
	<td class="al_center">
		<button type="button" class="btn btn-info btn-sm" onclick="document.location.href='provider_info.php?a=<?php echo $rs_proveedor['AI_proveedor_id']; ?>'"><i class="fa fa-search" aria-hidden="true"></i></button>
	</td>
<?php 	$qry_facturacompra=$link->query("SELECT AI_facturacompra_id FROM bh_facturacompra WHERE facturacompra_AI_proveedor_id = '{$rs_proveedor['AI_proveedor_id']}'") ?>
	<td class="al_center"><?php if ($qry_facturacompra->num_rows < 1) { ?>
		<button type="button" id="btn_del" class="btn btn-danger btn-sm" onclick="del_provider('<?php echo $rs_proveedor['AI_proveedor_id']; ?>');"><i class="fa fa-times" aria-hidden="true"></i></button>
<?php 	} ?></td>
</tr>
<?php }; ?>
