<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$r_function->url_replace_special_character($_GET['a']);
$value = $r_function->replace_regular_character($value);

$arr_value = (explode(' ',$value));
$txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_activo, TX_producto_codigo, TX_producto_exento, TX_producto_medida, TX_producto_cantidad, TX_producto_referencia, TX_producto_inventariado FROM bh_producto WHERE ";
end($arr_value);
$last_key = key($arr_value);
foreach ($arr_value as $key => $value) {
	$txt_product .= ($key === $last_key) ? "TX_producto_value LIKE '%{$value}%' OR " : "TX_producto_value LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($key === $last_key) ? "TX_producto_codigo LIKE '%{$value}%' OR " : "TX_producto_codigo LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($key === $last_key) ? "TX_producto_referencia LIKE '%{$value}%' " : "TX_producto_referencia LIKE '%{$value}%' AND ";
}
	// echo $txt_product; 
	// return false;
$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 100")or die($link->error);
$rs_product=$qry_product->fetch_array();
$nr_product=$qry_product->num_rows;
?>
<table id="tbl_purchase_product" class="table table-bordered table-condensed table-striped table-hover">
  <tbody>
<?php
		$prep_provider = $link->prepare("SELECT bh_proveedor.TX_proveedor_nombre FROM bh_datocompra 
		INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id
		INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id
		WHERE bh_datocompra.datocompra_AI_producto_id = ? ORDER BY bh_facturacompra.AI_facturacompra_id DESC LIMIT 1")or die($link->error);
		if ($qry_product->num_rows > 0) {
			do{
				$prep_provider->bind_param('i',$rs_product['AI_producto_id']); $prep_provider->execute(); $qry_provider = $prep_provider->get_result();
				$rs_provider = $qry_provider->fetch_array();
				$color = ($rs_product['TX_producto_activo'] === '1') ? '#f84c4c; font-weight: bolder;' : '#000';
				$title = $rs_provider['TX_proveedor_nombre'];
				$background = ($rs_product['TX_producto_inventariado'] === '1') ? '#cffebb' : '';
?>   		<tr style="color:<?php echo $color; ?>; background:<?php echo $background; ?>" title="<?php echo $title; ?>">
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_codigo'] ?></td>
					<td class="col-xs-7 col-sm-7 col-md-7 col-lg-7" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)">
						<?php echo $r_function->replace_special_character($rs_product['TX_producto_value']);
						if (!empty($rs_product['TX_producto_referencia'])) { ?>
							<br/>
							<font style="font-size: 8pt;">(REF: <?php echo $r_function->replace_special_character($rs_product['TX_producto_referencia']); ?>)</font>
						<?php } ?>
					</td>
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_cantidad'] ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><button type="button" class="btn btn-warning btn-xs" onclick="open_popup('popup_updproduct.php?a=<?php echo $rs_product['AI_producto_id'] ?>', '_popup','1010','654')"><i class="fa fa-wrench"></i></button></td>
				</tr>
<?php
			}while($rs_product=$qry_product->fetch_array());
		}else{	?>
			<tr>
				<td colspan="4">Vacio</td>
			</tr>
<?php			
		}
			
?>
  </tbody>
</table>
