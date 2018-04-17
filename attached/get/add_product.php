<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$codigo=$_GET['a'];
$value=$r_function->url_replace_special_character($_GET['b']);
$value=$r_function->replace_regular_character($value);
$medida=$_GET['c'];
$cantidad=$_GET['d'];
$minimo=$_GET['f'];
$maximo=$_GET['e'];

$exento=$_GET['g'];
$p_5=$_GET['h'];
$p_4=$_GET['i'];
$p_3=$_GET['j'];
$p_2=$_GET['k'];
$p_1=$_GET['l'];

$referencia=$_GET['m'];
$letra=$_GET['n'];

$fecha_actual=date('Y-m-d');

$qry_checkproduct=$link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_codigo = '$codigo'");
$nr_checkproduct=$qry_checkproduct->num_rows;
if($nr_checkproduct < 1){
	$bh_insert="INSERT INTO bh_producto (TX_producto_codigo, TX_producto_value, TX_producto_medida, TX_producto_cantidad, TX_producto_minimo, TX_producto_maximo, TX_producto_exento, TX_producto_referencia, producto_AI_letra_id) VALUES ('$codigo','$value','$medida','$cantidad','$minimo','$maximo','$exento','$referencia','$letra')";
	$link->query($bh_insert) or die($link->error);

	$rs = $link->query("SELECT MAX(AI_producto_id) AS id FROM bh_producto");
	if ($row = $rs->fetch_array()) {
		$lastid = trim($row[0]);
	}
	$bh_insprecio="INSERT INTO bh_precio (precio_AI_producto_id, precio_AI_medida_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha) VALUES ('$lastid','$medida','$p_1','$p_2','$p_3','$p_4','$p_5','$fecha_actual')";
	$link->query($bh_insprecio) or die($link->error);

	$link->query("INSERT INTO rel_producto_medida (productomedida_AI_medida_id, productomedida_AI_producto_id, TX_rel_productomedida_cantidad) VALUES ('1','$lastid','1')")or die($link->error);

// #################################   ANSWER     ##################

	$qry_product=$link->query("SELECT AI_producto_id, TX_producto_value, TX_producto_minimo, TX_producto_codigo, TX_producto_medida, TX_producto_alarma, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_referencia FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 20 ");
	$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
	$nr_product=$qry_product->num_rows;

	$prep_precio=$link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' ORDER BY AI_precio_id DESC LIMIT 1");
	$prep_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
	$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

	if($nr_product=$qry_product->num_rows != '0'){ ?>
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
	<?php			do{		?>
					<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">
						<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
						<td><?php echo $rs_product['TX_producto_referencia'] ?></td>
						<td><?php echo $rs_product['TX_producto_value'] ?></td>
						<td>
	<?php				if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
								echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
							}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
								echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
							}else{
								echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
							}
	?>				</td>
						<td>
	<?php 			$prep_precio->bind_param("i",$rs_product['AI_producto_id']); $prep_precio->execute(); $qry_precio=$prep_precio->get_result();
							$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
							echo $rs_precio['TX_precio_cuatro'];
	?>				</td>
						<td><button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')"><i class="fa fa-rotate-right" aria-hidden="true"></i><?php echo $rs_product['TX_producto_rotacion']; ?></button></td>
						<td><button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">Modificar</button></td>
						<td>
	<?php 			$prep_facturaventa->bind_param("i", $rs_product['AI_producto_id']); $prep_facturaventa->execute(); $qry_facturaventa=$prep_facturaventa->get_result();
							if($qry_facturaventa->num_rows < 1){
								$prep_facturacompra->bind_param("i", $rs_product['AI_producto_id']); $prep_facturacompra->execute(); $qry_facturacompra=$prep_facturacompra->get_result();
								if ($qry_facturacompra->num_rows < 1) {  ?>
									<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">Eliminar</button>
	<?php					}
							} ?>
						</td>
					</tr>
<?php		}while($rs_product=$qry_product->fetch_array()); ?>
			</tbody>
		</table><?php
	}
}else{
		echo "<center><strong>
No se agrego el producto, El codigo se encuentra repetido.
</strong></center>";
}

?>
