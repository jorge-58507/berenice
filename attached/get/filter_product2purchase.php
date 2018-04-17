<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$r_function->replace_regular_character($_GET['a']);

$arr_value = (explode(' ',$value));
$txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_activo, TX_producto_codigo, TX_producto_exento, TX_producto_medida, TX_producto_cantidad, TX_producto_referencia FROM bh_producto WHERE ";
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_value LIKE '%$value%' OR " : "TX_producto_value LIKE '%$value%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_codigo LIKE '%$value%' OR " : "TX_producto_codigo LIKE '%$value%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_referencia LIKE '%$value%'" : "TX_producto_referencia LIKE '%$value%' AND ";
}
$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 100")or die($link->error);
$rs_product=$qry_product->fetch_array();
$nr_product=$qry_product->num_rows;
?>
<table id="tbl_product" class="table table-bordered table-condensed table-striped table-hover">
  <tbody>
<?php
    do{
			$color = ($rs_product['TX_producto_activo'] === '1') ? '#f84c4c; font-weight: bolder;' : '#000';
			$title = ($rs_product['TX_producto_activo'] === '1') ? 'INACTIVO' : '';
?>   	<tr style="color:<?php echo $color; ?>" title="<?php echo $title; ?>">
				<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_codigo'] ?></td>
				<td class="col-xs-7 col-sm-7 col-md-7 col-lg-7" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
				<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_cantidad'] ?></td>
				<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><button type="button" class="btn btn-warning btn-xs" onclick="open_popup('popup_updproduct.php?a=<?php echo $rs_product['AI_producto_id'] ?>', '_popup','1010','654')"><i class="fa fa-wrench"></i></button></td>
      </tr>
<?php
		}while($rs_product=$qry_product->fetch_array());
?>
  </tbody>
</table>
