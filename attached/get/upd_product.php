<?php
require '../../bh_con.php';
$link = conexion();
function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "&deg;", "#");
return $n_str = str_replace($pat, $rep, $str);
}
$codigo=$_GET['a'];
$value=edit_quote($_GET['b']);
$medida=$_GET['c'];
$impuesto=$_GET['l'];
$cantidad=$_GET['d'];
$minimo=$_GET['f'];
$maximo=$_GET['e'];
$alarm=$_GET['m'];
$activo=$_GET['n'];
$reference=$_GET['o'];
$letra=$_GET['p'];

$product_id=$_GET['q'];

$p1=$_GET['g'];
$p2=$_GET['h'];
$p3=$_GET['i'];
$p4=$_GET['j'];
$p5=$_GET['k'];

$fecha_actual=date('Y-m-d');

	$qry_checkproduct=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'")or die(mysql_error());
	$nr_checkproduct=mysql_num_rows($qry_checkproduct);
	if($nr_checkproduct > 0){
		$rs_checkproduct=mysql_fetch_assoc($qry_checkproduct);
		$id=$rs_checkproduct['AI_producto_id'];
		$bh_update="UPDATE bh_producto SET TX_producto_value='$value', TX_producto_medida='$medida', TX_producto_cantidad='$cantidad', TX_producto_minimo='$minimo', TX_producto_maximo='$maximo', TX_producto_exento='$impuesto', TX_producto_alarma='$alarm', TX_producto_activo = '$activo', TX_producto_referencia = '$reference', producto_AI_letra_id= '$letra', TX_producto_codigo = '$codigo' WHERE AI_producto_id = '$id'";
		mysql_query($bh_update, $link) or die (mysql_error());

		$qry_precio = mysql_query("SELECT AI_precio_id FROM bh_precio WHERE precio_AI_producto_id = '$id' AND TX_precio_uno = '$p1' AND TX_precio_dos = '$p2' AND TX_precio_tres = '$p3' AND TX_precio_cuatro = '$p4' AND TX_precio_cinco = '$p5'AND TX_precio_inactivo = '0' ")or die(mysql_error());
		if($nr_precio = mysql_num_rows($qry_precio) < 1){
			mysql_query("UPDATE bh_precio SET TX_precio_inactivo='1' WHERE precio_AI_producto_id = '$product_id'")or die(mysql_error());
			$txt_insert_precio="INSERT INTO bh_precio (precio_AI_producto_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha ) VALUES ('$product_id','$p1','$p2','$p3','$p4','$p5','$fecha_actual')";
			mysql_query($txt_insert_precio)or die(mysql_error());
		}

}

//   ###########################    ANSWER     ##########################
$value=edit_quote($_GET['r']);

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


$qry_product=mysql_query($txt_product." ORDER BY TX_producto_value ASC LIMIT 50");
$rs_product=mysql_fetch_assoc($qry_product);

$nr_product=mysql_num_rows($qry_product);

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
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
</tfoot>
<tbody>
<?php
	do{
			?>
	<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">
			<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
			<td><?php echo $rs_product['TX_producto_referencia'] ?></td>
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
			<td>
				<?php
				$qry_precio=mysql_query("SELECT * FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC LIMIT 1", $link);
				$rs_precio=mysql_fetch_assoc($qry_precio);
				echo $rs_precio['TX_precio_cuatro'];
				?>

			</td>
			<td>
			<button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')">
	<i class="fa fa-rotate-right" aria-hidden="true"></i>
	<?php echo $rs_product['TX_producto_rotacion']; ?></button>
			</td>
			<td>
<button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">
			Modificar</button>
			</td>
			<td>
			<?php
	$qry_checkfacturaventa=mysql_query("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = '{$rs_product['AI_producto_id']}'");
	$nr_checkfacturaventa=mysql_num_rows($qry_checkfacturaventa);
	if($nr_checkfacturaventa < 1){
	 ?>
<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">
			Eliminar</button>
			<?php } ?>
			</td>
	</tr>
			<?php
	}while($rs_product=mysql_fetch_assoc($qry_product));
	?>
	</tbody>
	</table>
