<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$codigo=$_GET['a'];
$value=$r_function->url_replace_special_character($_GET['b']);
$value=$r_function->replace_regular_character($value);
$medida=$_GET['c'];
$impuesto=$_GET['l'];
$cantidad=$_GET['d'];
$minimo=$_GET['f'];
$maximo=$_GET['e'];
$alarm=$_GET['m'];
$activo=$_GET['n'];
$reference=$r_function->url_replace_special_character($_GET['o']);
$reference=$r_function->replace_regular_character($reference);
$letra=$_GET['p'];
$descontable=$_GET['s'];
$inventariado=$_GET['t'];
$ubicacion=$_GET['u'];
$subfamilia=$_GET['v'];
$product_id=$_GET['q'];
$fecha_actual=date('Y-m-d');

	$qry_checkproduct=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$nr_checkproduct=$qry_checkproduct->num_rows;
	if($nr_checkproduct > 0){
		$rs_checkproduct=$qry_checkproduct->fetch_array();
		$id=$rs_checkproduct['AI_producto_id'];

		$product_info = date('d-m-Y H:i:s')." ".$_COOKIE['coo_suser'].": /*".$rs_checkproduct['TX_producto_value']." (".$id.")"." Codigo: ".$rs_checkproduct['TX_producto_codigo']." Medida: ".$rs_checkproduct['TX_producto_medida']." Cantidad ".$rs_checkproduct['TX_producto_cantidad']." Min: ".$rs_checkproduct['TX_producto_minimo']." Max: ".$rs_checkproduct['TX_producto_maximo']." Imp: ".$rs_checkproduct['TX_producto_exento']." Alarma: ".$rs_checkproduct['TX_producto_alarma']." Activo: ".$rs_checkproduct['TX_producto_alarma']." Letra: ".$rs_checkproduct['producto_AI_letra_id']." Descontable: ".$rs_checkproduct['TX_producto_descontable']." Inventario: ".$rs_checkproduct['TX_producto_inventariado']."*/";

		$bh_update="UPDATE bh_producto SET TX_producto_value='$value', TX_producto_medida='$medida', TX_producto_cantidad='$cantidad', TX_producto_minimo='$minimo', TX_producto_maximo='$maximo', TX_producto_exento='$impuesto', TX_producto_alarma='$alarm', TX_producto_activo = '$activo', TX_producto_referencia = '$reference', producto_AI_letra_id= '$letra', TX_producto_codigo = '$codigo', TX_producto_descontable = '$descontable', TX_producto_inventariado = '$inventariado', producto_AI_area_id = '$ubicacion', producto_AI_subfamilia_id = '$subfamilia' WHERE AI_producto_id = '$id'";

		$link->query($bh_update) or die ($link->error);
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, $product_info." ---> ".$value." (".$id.")"." Medida: ".$medida." Cantidad ".$cantidad ." Min: ".$minimo." Max: ".$maximo." Imp: ".$impuesto." Alarma: ".$alarm." Activo: ".$activo." Letra: ".$letra." Codigo: ".$codigo." Descontable: ".$descontable." Inventario: ".$inventariado." Ubicacion: ".$ubicacion.PHP_EOL );
		fclose($file);
		// ##################   CREAR NOTIFICACION
		$raw_user = $r_function->read_user();
		$qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE TX_user_type = '2' AND TX_user_activo = '1' or TX_user_type = '5'  AND TX_user_activo = '1'")or die($link->error);
		while($rs_user=$qry_user->fetch_array(MYSQLI_ASSOC)) {
			$content =		$raw_user[$_COOKIE['coo_iuser']].' actualiz&oacute; la informacion de '.$value.' ('.$codigo.')';
			$r_function->method_message('create', $_COOKIE['coo_iuser'], $rs_user['AI_user_id'], 'Producto Actualizado', $content, 'notification', date('H:i:s'), date('d-m-Y'));
		}		
	}
//   ###########################    ANSWER     ##########################
$value=$r_function->url_replace_special_character($_GET['r']);
$value=$r_function->replace_regular_character($value);
$limite=20;

$prep_precio=$link->prepare("SELECT AI_precio_id, TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' AND precio_AI_medida_id = ? ORDER BY TX_precio_fecha DESC LIMIT 1")or die($link->error);
$prep_checkfacturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

$arr_value = (explode(' ',$value));
$arr_value = array_values(array_unique($arr_value));
$txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_codigo, TX_producto_referencia, TX_producto_activo, TX_producto_minimo, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_medida FROM bh_producto WHERE ";
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_value LIKE '%{$value}%' OR " : "TX_producto_value LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_codigo LIKE '%{$value}%' OR " : "TX_producto_codigo LIKE '%{$value}%' AND ";
}
foreach ($arr_value as $key => $value) {
	$txt_product .= ($value === end($arr_value)) ? "TX_producto_referencia LIKE '%{$value}%'" : "TX_producto_referencia LIKE '%{$value}%' AND ";
}
$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT ".$limite);
$raw_producto=array(); $i=0;
while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
	if ($i < $limite) {
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
	}else{
		break;
	}
	$i++;
}
?>
<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
<thead class="bg-primary">
<tr>
	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Referencia</th>
	<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
</tr>
</thead>
<tfoot class="bg-primary">
	<tr><td colspan="8"></td></tr>
</tfoot>
<tbody>
<?php
if ($qry_product->num_rows > 0) {
	foreach ($raw_producto as $key => $rs_product) {
		if($rs_product['TX_producto_activo'] === '1') { $style = 'color:#c67250; font-weight: bolder'; $title='INACTIVO'; }else{ $style='#000'; $title=''; }	?>
		<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');"  style="<?php echo $style; ?>" title="<?php echo $title; ?>">
			<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
			<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_referencia']); ?></td>
			<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
<?php		$style_cantidad='style="color:#000000"';
				if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
					$style_cantidad='style="color:#51AA51"';
				}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
					$style_cantidad='style="color:#C63632"';
				}
?>		<td <?php echo $style_cantidad; ?>><?php echo $rs_product['TX_producto_cantidad'] ?></td>
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
	</tbody>
	</table>
