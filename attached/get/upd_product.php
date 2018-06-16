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
$reference=$_GET['o'];
$letra=$_GET['p'];
$descontable=$_GET['s'];
$inventariado=$_GET['t'];

$product_id=$_GET['q'];

$fecha_actual=date('Y-m-d');

	$qry_checkproduct=$link->query("SELECT AI_producto_id FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$nr_checkproduct=$qry_checkproduct->num_rows;
	if($nr_checkproduct > 0){
		$rs_checkproduct=$qry_checkproduct->fetch_array();
		$id=$rs_checkproduct['AI_producto_id'];
		$bh_update="UPDATE bh_producto SET TX_producto_value='$value', TX_producto_medida='$medida', TX_producto_cantidad='$cantidad', TX_producto_minimo='$minimo', TX_producto_maximo='$maximo', TX_producto_exento='$impuesto', TX_producto_alarma='$alarm', TX_producto_activo = '$activo', TX_producto_referencia = '$reference', producto_AI_letra_id= '$letra', TX_producto_codigo = '$codigo', TX_producto_descontable = '$descontable', TX_producto_inventariado = '$inventariado' WHERE AI_producto_id = '$id'";
		$link->query($bh_update) or die ($link->error);
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, date('d-m-Y H:i:s')." ".$_COOKIE['coo_suser'].": ".$value." (".$id.")"." Medida: ".$medida." Cantidad ".$cantidad ." Min: ".$minimo." Max: ".$maximo." Imp: ".$impuesto." Alarma: ".$alarm." Activo: ".$activo." Letra: ".$letra." Codigo: ".$codigo." Descontable: ".$descontable." Inventario: ".$inventariado.PHP_EOL );
		fclose($file);

	}

//   ###########################    ANSWER     ##########################
$value=$r_function->url_replace_special_character($_GET['r']);
$value=$r_function->replace_regular_character($value);

$prep_precio=$link->prepare("SELECT AI_precio_id, TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC LIMIT 1")or die($link->error);
$prep_checkfacturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

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
$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 50");
$rs_product=$qry_product->fetch_array();
$nr_product=$qry_product->num_rows;
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
	do{
		$font_color='#000'; $title='';
		if ($rs_product['TX_producto_activo'] === '1'){ $font_color = '#fb1414'; $title='INACTIVO'; } ?>
		<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');"  style="color:<?php echo $font_color ?>" title="<?php echo $title; ?>">
			<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
			<td><?php echo $rs_product['TX_producto_referencia'] ?></td>
			<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
			<?php	$style='style="color:#000000"';
			if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){	$style='style="color:#51AA51"';	}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){	$style='style="color:#C63632"';	}			?>
			<td <?php echo $style; ?>>
				<?php echo $rs_product['TX_producto_cantidad'] ?>
			</td>
<?php $prep_precio->bind_param("i", $rs_product['AI_producto_id']); $prep_precio->execute(); $qry_precio=$prep_precio->get_result();
			$rs_precio=$qry_precio->fetch_array(); ?>
			<td><?php	echo $rs_precio['TX_precio_cuatro'];	?></td>
			<td>
				<button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')"><i class="fa fa-rotate-right" aria-hidden="true"></i><?php echo $rs_product['TX_producto_rotacion']; ?></button>
			</td>
			<td>
				<button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">Modificar</button>
			</td>
			<td><?php
				$prep_checkfacturaventa->bind_param("i",$rs_product['AI_producto_id']); $prep_checkfacturaventa->execute(); $qry_checkfacturaventa = $prep_checkfacturaventa->get_result();
				if($qry_checkfacturaventa->num_rows < 1){
					$prep_facturacompra->bind_param("i", $rs_product['AI_producto_id']); $prep_facturacompra->execute(); $qry_facturacompra=$prep_facturacompra->get_result();
					if ($qry_facturacompra->num_rows < 1) { ?>
						<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">Eliminar</button>
<?php			}
				} ?>
			</td>
	</tr>
<?php }while($rs_product=$qry_product->fetch_array());	?>
	</tbody>
	</table>
