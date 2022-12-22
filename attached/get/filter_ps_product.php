<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$r_function->url_replace_special_character($_GET['a']);
$value=$r_function->replace_regular_character($value);
if(!empty($_GET['b'])){
	$line_limit=" LIMIT ".$_GET['b'];
}else{
	$line_limit="";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_product="SELECT * FROM bh_producto WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND ";
	}
}
$txt_product.=" ORDER BY TX_producto_value ASC".$line_limit;
$qry_product=$link->query($txt_product) or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$nr_product=$qry_product->num_rows;

?>


	<?php
	if($nr_product > 0){
    do{        ?>
    	<tr onclick="filter_psbyproduct('<?php echo $rs_product['AI_producto_id']; ?>');" ondblclick="open_popup('popup_updproduct.php?a=<?php echo $rs_product['AI_producto_id']; ?>');">
        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
        <td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
        <td>
<?php 		if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
            echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
        	}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
            echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
        	}else{
            echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
        	}  ?>
        </td>
    	</tr><?php
		}while($rs_product=$qry_product->fetch_array());
	}else{
?>	<tr><td colspan="3"></td></tr><?php
	} 	?>
