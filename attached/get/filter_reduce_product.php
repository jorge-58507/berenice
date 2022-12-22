<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$r_function->url_replace_special_character($_GET['a']);
$value=$r_function->replace_regular_character($value);

$arr_value = (explode(' ',$value));
$txt_producto="SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE ";
foreach ($arr_value as $key => $value) {
	if($value === end($arr_value)){
		$txt_producto=$txt_producto."TX_producto_value LIKE '%{$value}%' OR ";
	}else{
		$txt_producto=$txt_producto."TX_producto_value LIKE '%{$value}%' AND ";
	}
}
foreach ($arr_value as $key => $value) {
	if($value === end($arr_value)){
		$txt_producto=$txt_producto."TX_producto_codigo LIKE '%{$value}%'";
	}else{
		$txt_producto=$txt_producto."TX_producto_codigo LIKE '%{$value}%' AND ";
	}
}
$qry_producto=$link->query($txt_producto." ORDER BY TX_producto_value ASC LIMIT 100");
while($rs_producto=$qry_producto->fetch_array(MYSQLI_ASSOC)){ ?>
	<tr onclick="select_item();">
		<td><?php echo $rs_producto['TX_producto_codigo']; ?></td>
		<td><?php echo $rs_producto['TX_producto_value']; ?></td>
		<td><?php echo $rs_producto['TX_producto_cantidad']; ?></td>
	</tr>
<?php }  ?>
