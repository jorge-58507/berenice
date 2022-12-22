<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];


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

$qry_product=mysql_query($txt_product."LIMIT 50");
$rs_product=mysql_fetch_assoc($qry_product);

$nr_product=mysql_num_rows($qry_product);


if($nr_product > 0){
?>
        <select id="sel_productlist" name="sel_productlist" class="form-control" size="3">
	<?php	do{ ?>	
			<option value="<?php echo $rs_product['AI_producto_id'] ?>" onclick="set_txtproducto(this);"><?php echo $rs_product['TX_producto_value'] ?></option>
        <?php }while($rs_product=mysql_fetch_assoc($qry_product)); ?>
        </select>
<?php
}else{
?>
			<select name="sel_product" id="sel_product" class="form-control" size="3">
    <option value=""></option>
        </select>
<?php
}
?>

    
    
    
    

