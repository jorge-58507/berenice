<?php
require '../../bh_con.php';
$link = conexion();

function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "°", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$value=edit_quote($_GET['a']);
$type=$_GET['b'];


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

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_referencia LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_referencia LIKE '%{$arr_value[$it]}%' AND ";
	}
}


$qry_product=mysql_query($txt_product." ORDER BY TX_producto_value ASC LIMIT 100");
$rs_product=mysql_fetch_assoc($qry_product);

$nr_product=mysql_num_rows($qry_product);
?>
	<table id="tbl_product" class="table table-bordered table-condensed table-striped table-hover">
        <tbody>
        <?php
        do{
        ?>
        <tr onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)">
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_product['TX_producto_codigo'] ?></td>
        	<td class="col-xs-6 col-sm-6 col-md-6 col-lg-6"><?php echo $rs_product['TX_producto_value'] ?></td>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_product['TX_producto_referencia'] ?></td>
        </tr>
        <?php
        }while($rs_product=mysql_fetch_assoc($qry_product));
        ?>
        </tbody>
	</table>
