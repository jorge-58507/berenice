<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$_GET['a'];


$prep_precio=$link->prepare("SELECT AI_precio_id, TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' AND precio_AI_medida_id = ? ORDER BY TX_precio_fecha DESC LIMIT 1")or die($link->error);
$prep_checkfacturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_producto.TX_producto_referencia,
bh_producto.TX_producto_activo, bh_producto.TX_producto_minimo, bh_producto.TX_producto_maximo, bh_producto.TX_producto_cantidad,
bh_producto.TX_producto_rotacion, bh_producto.TX_producto_medida, bh_producto.TX_producto_inventariado
FROM (((bh_proveedor
INNER JOIN bh_facturacompra ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id)
WHERE AI_proveedor_id = '$value'";

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 30")or die($link->error);

$raw_producto=array(); $i=0;
$raw_producto=array(); $i=0;

while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
		$prep_precio->bind_param("ii", $rs_product['AI_producto_id'], $rs_product['TX_producto_medida']); $prep_precio->execute(); $qry_precio=$prep_precio->get_result();
		$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
		$prep_checkfacturaventa->bind_param("i",$rs_product['AI_producto_id']); $prep_checkfacturaventa->execute(); $qry_checkfacturaventa = $prep_checkfacturaventa->get_result();
		$prep_facturacompra->bind_param("i", $rs_product['AI_producto_id']); $prep_facturacompra->execute(); $qry_facturacompra=$prep_facturacompra->get_result();

		$raw_producto[$i]=$rs_product;
		$raw_producto[$i]['precio']=$rs_precio['TX_precio_cuatro'];
		if ($qry_checkfacturaventa->num_rows < 1) {
			$raw_producto[$i]['btn_delete']=$qry_facturacompra->num_rows;
		}else{
			$raw_producto[$i]['btn_delete']=$qry_checkfacturaventa->num_rows;
		}
	$i++;
}
if ($qry_product->num_rows > 0) {
	foreach ($raw_producto as $key => $rs_product) {
		$style=''; $title='';
		if($rs_product['TX_producto_activo'] === '1') { $style = 'color:#c67250; font-weight: bolder;'; $title='INACTIVO'; }
		if($rs_product['TX_producto_inventariado'] === '1') { $style .= ' background-color:#CFFEBB'; }	?>
		<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');"  style="<?php echo $style; ?>" title="<?php echo $title; ?>">
			<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
			<td><?php echo $rs_product['TX_producto_referencia'] ?></td>
			<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
			<?php	$style_cantidad='style="color:#000000"';
			if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
				$style_cantidad='style="color:#51AA51"';
			}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
				$style_cantidad='style="color:#C63632"';
			}
			?><td <?php echo $style_cantidad; ?>><?php echo $rs_product['TX_producto_cantidad'] ?></td>
			<td><?php echo $rs_product['precio']; ?></td>
			<td><button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')"><i class="fa fa-rotate-right" aria-hidden="true"></i><?php echo $rs_product['TX_producto_rotacion']; ?></button></td>
			<td><button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">Modificar</button></td>
			<td><?php if($rs_product['btn_delete'] < 1){ ?>
				<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">Eliminar</button>
			<?php	} ?></td>
		</tr>
	<?php }
}else{?>
	<tr><td colspan="8"></td></tr>
<?php } ?>

			<!-- $res_cantidad=($qry_product->num_rows > 200)? '+200' : $qry_product->num_rows;
			$caption = 'Se Encontr&oacute;: '.$res_cantidad.' Resultado(s) para "'.$r_function->url_replace_special_character($_GET['a']).'".';
			$raw_result = array($caption,$raw_producto);
			echo json_encode($raw_result); -->
