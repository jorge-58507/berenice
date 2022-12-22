<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];
if ($limit == "") {
	$line_limit = "";
}else{
	$line_limit = " LIMIT ".$limit;
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

$txt_product .= " ORDER BY TX_producto_value ASC".$line_limit;
$qry_product=mysql_query($txt_product)or die(mysql_error());
$rs_product=mysql_fetch_assoc($qry_product);
$nr_product=mysql_num_rows($qry_product);

?>

	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
        <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Nombre</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
	</tr>
	</thead>
	<tfoot class="bg-primary">
	<tr>	<td></td><td></td><td></td>	</tr>
	</tfoot>
	<tbody>
	<?php
	if($nr_product > 0){
    do{
        ?>
    <tr onclick="filter_purchasebyproduct('<?php echo $rs_product['AI_producto_id']; ?>');">
        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
        <td><?php echo $rs_product['TX_producto_value'] ?></td>
        <td>
        <?php
        if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
            echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
        }elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
            echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
        }else{
            echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
        }
        ?>
        </td>
    </tr>
	<?php
    }while($rs_product=mysql_fetch_assoc($qry_product));
	}else{
	?>
	<tr>	<td></td><td></td><td></td>	</tr>
	<?php
	}?>
    </tbody>
    </table>
