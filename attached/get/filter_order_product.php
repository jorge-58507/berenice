<?php
require '../../bh_conexion.php';
$link = conexion();
function transform_quote($str){
	$str = str_replace("'","&apos;",$str);
	echo $str;
}
$value=$_GET['a'];

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_cantidad FROM bh_producto WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' ";
	}else{
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
		$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' ";
	}else{
		$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product.=" ORDER BY TX_producto_value ASC LIMIT 50";

$qry_product=$link->query($txt_product);

if($qry_product->num_rows > 0){
	while ($rs_product=$qry_product->fetch_array()){
?>
<tr onclick="set_order_info('<?php echo $rs_product[0]; ?>','<?php echo $rs_product[1]; ?>','<?php echo str_replace("'","\'",$rs_product[2]); ?>')">
	<td><?php echo $rs_product['TX_producto_codigo']; ?></td>
	<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
	<td><?php echo $rs_product['TX_producto_cantidad']; ?></td>
</tr>

<?php
	}
}else{
?>
<tr>
	<td>&nbsp;</td>
	<td></td>
	<td></td>
</tr>
<?php } ?>
