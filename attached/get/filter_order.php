<?php
require '../../bh_conexion.php';
$link = conexion();
function transform_quote($str){
	$str = str_replace("'","&apos;",$str);
	echo $str;
}
$value=$_GET['a'];
$limit=$_GET['b'];
if($limit != ""){
	$line_limit = "LIMIT ".$limit;
}else{
	$line_limit = "";
}

$txt_order="SELECT bh_user.TX_user_seudonimo, bh_proveedor.TX_proveedor_nombre, bh_pedido.AI_pedido_id, bh_pedido.TX_pedido_numero, bh_pedido.TX_pedido_fecha, bh_pedido.TX_pedido_status FROM ((bh_pedido INNER JOIN bh_user ON bh_user.AI_user_id = bh_pedido.pedido_AI_user_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_pedido.pedido_AI_proveedor_id) WHERE ";

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

foreach ($arr_value as $key => $value) {
	if ($key === $size_value-1) {
		$txt_order=$txt_order."bh_user.TX_user_seudonimo LIKE '%{$value}%' OR ";
	}else{
		$txt_order=$txt_order."bh_user.TX_user_seudonimo LIKE '%{$value}%' AND ";
	}
}

foreach ($arr_value as $key => $value) {
	if ($key === $size_value-1) {
		$txt_order=$txt_order."bh_proveedor.TX_proveedor_nombre LIKE '%{$value}%' OR ";
	}else{
		$txt_order=$txt_order."bh_proveedor.TX_proveedor_nombre LIKE '%{$value}%' AND ";
	}
}

foreach ($arr_value as $key => $value) {
	if ($key === $size_value-1) {
		$txt_order=$txt_order."bh_pedido.TX_pedido_numero LIKE '%{$value}%'";
	}else{
		$txt_order=$txt_order."bh_pedido.TX_pedido_numero LIKE '%{$value}%' AND ";
	}
}

echo $txt_order.=" ORDER BY TX_pedido_numero DESC ".$line_limit;

$qry_order=$link->query($txt_order);

if($qry_order->num_rows > 0){
	$qry_datopedido = $link->prepare("SELECT bh_producto.TX_producto_value, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.TX_datopedido_precio, bh_datopedido.datopedido_AI_pedido_id, bh_producto.TX_producto_exento
		FROM (bh_datopedido
			INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id)
		WHERE bh_datopedido.datopedido_AI_pedido_id = ? ");
	while ($rs_order=$qry_order->fetch_array()) {
?>

<tr onclick="show_datopedido('order_<?php echo $rs_order[2] ?>')">
	<td><?php echo $rs_order[0]; ?></td>
	<td><?php echo $rs_order[3]; ?></td>
	<td><?php echo date('d-m-Y',strtotime($rs_order[4])); ?></td>
	<td><?php echo $rs_order[1]; ?></td>
	<td><?php
	switch ($rs_order[5]) {
		case 'ACTIVO':
			$font_color="#e9ca2f";
			break;
		case 'ENVIADO':
			$font_color="#57afdb";
			break;
		case 'RECIBIDO':
			$font_color="#67b847";
			break;
		case 'CANCELADO':
			$font_color="#b54a4a";
			break;
	}
	 echo "<span style='color:".$font_color.";font-weight: bolder;'>".$rs_order[5]."</span>"; ?>
	</td>
	<td>
		<?php switch ($rs_order[5]) {
			case 'ACTIVO':
				$button = '<button type="button" id="btn_sended" name="'.$rs_order[2].'" onclick="upd_order_sended(this.name);" class="btn btn-info btn-xs">
				<i class="fa fa-upload" aria-hidden="true"></i>
				Enviado</button>';
				break;
			case 'ENVIADO':
				$button =	'<button type="button" id="btn_process" name="'.$rs_order[2].'" onclick="open_process_order(this.name);" class="btn btn-success btn-xs">
				<i class="fa fa-upload fa-flip-vertical" aria-hidden="true"></i>
				Recibido</button>';
				break;
			default:
				$button = '<button type="button" id="" class="btn btn-default btn-xs" disabled="disabled"><i  class="fa fa-ban" aria-hidden="true"></i>	Recibido</button>';
		}
		echo $button;
		?>
	</td>
	<td>
		<button type="button" id="btn_upd" name="" title="Duplicar" onclick="upd_order('<?php echo $rs_order[2]; ?>');" class="btn btn-warning btn-sm"><i class="fa fa-copy" aria-hidden="true"></i></button>
		<button type="button" id="btn_process" name="" title="Imprimir"  onclick="print_html('print_order_html.php?a=<?php echo $rs_order[2]; ?>');" class="btn btn-info btn-sm"><i class="fa fa-print" aria-hidden="true"></i></button>
<?php 	if($rs_order[5] === 'ACTIVO'){	?>
			<button type="button" id="btn_delete" name="" title="Eliminar" onclick="del_order('<?php echo $rs_order[2]; ?>');" class="btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
<?php 	} ?>
	</td>
</tr>

<tr id="order_<?php echo $rs_order[2] ?>" style="	display: none;">
	<td></td>
	<td colspan="4">

		<table id="tbl_datopedido" class="table table-bordered tbl-padding-0" style="border:solid 2px #ccc;">
			<thead class="bg-info">
			<tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
				<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Detalle</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
			</tr>
			</thead>
			<tbody>
<?php
	$total=0;
	$qry_datopedido->bind_param("i",$rs_order[2]);
	$qry_datopedido->execute();
	$result = $qry_datopedido->get_result();
	while ($rs_datopedido=$result->fetch_array()) {
		$impuesto4product = ($rs_datopedido['TX_producto_exento']*$rs_datopedido[2])/100;
		$precio_impuesto=$rs_datopedido[2]+$impuesto4product;
		$precio4product = $precio_impuesto*$rs_datopedido[1];
		$total+=$precio4product;
?>
	<tr>
		<td><?php echo $rs_datopedido['TX_datopedido_cantidad']; ?></td>
		<td><?php echo $r_function->replace_special_character($rs_datopedido['TX_producto_value']); ?></td>
		<td><?php echo number_format($precio_impuesto,4); ?></td>
	</tr>
<?php
	}
?>
			</tbody>
			<tfoot class="bg-info">
			<tr>
				<td></td>
				<td></td>
				<td><strong><?php echo number_format($total,2); ?></strong></td>
			</tr>
			</tfoot>
		</table>

	</td>
</tr>
	<?php
	}
}else{	?>
<tr>
	<td>&nbsp;</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php
}
?>
