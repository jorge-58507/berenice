<?php
require '../../bh_conexion.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$id=$_GET['a'];
$cantidad=$_GET['h'];
$precio=$_GET['c'];
$impuesto=$_GET['e'];
$descuento=$_GET['d'];
$facturaventa_id=$_GET['f'];
$str_factid=$_GET['g'];
$arr_factid = explode(",",$str_factid);
$arr_length=count($arr_factid);
$medida = $_GET['i'];
$desc = ($precio*$descuento)/100;
$precio_desc = $precio-$desc;
$imp = ($precio_desc*$impuesto)/100;
$precio_imp = $precio_desc+$imp;
$new_total=$cantidad*$precio_imp;

	$txt_datoventa="SELECT AI_datoventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_datoventa=$txt_datoventa." datoventa_AI_producto_id = '$id' AND bh_facturaventa.AI_facturaventa_id = '$value'";
	}else{
		$txt_datoventa=$txt_datoventa." datoventa_AI_producto_id = '$id' AND bh_facturaventa.AI_facturaventa_id = '$value' OR";
	}
}
	$qry_datoventa=$link->query($txt_datoventa)or die($link->error);
	if($qry_datoventa->num_rows < 1){
		$qry_checkfacturaventa=$link->query("SELECT TX_facturaventa_total, facturaventa_AI_cliente_id FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'");
		$rs_checkfacturaventa=$qry_checkfacturaventa->fetch_array();
		$client_id=$rs_checkfacturaventa['facturaventa_AI_cliente_id'];
		$total_facturaventa=$rs_checkfacturaventa['TX_facturaventa_total']+$new_total;
		$link->query("UPDATE bh_facturaventa SET TX_facturaventa_total = $total_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'") or die($link->error);

		$qry_product = $link->query("SELECT TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$id'")or die($link->error);
		$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
		$descripcion = $r_function->replace_regular_character($rs_product['TX_producto_value']);
		$link->query("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_medida, TX_datoventa_stock) VALUES ('$facturaventa_id', '{$_COOKIE['coo_iuser']}', '$id', '$cantidad', '$precio', '$impuesto', '$descuento', '$descripcion','$medida','{$rs_product['TX_producto_cantidad']}')")or die($link->error);
	}

//   ###############################    ANSWER     ###########################
$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
  $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$txt_clientid="SELECT facturaventa_AI_cliente_id FROM bh_facturaventa WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_clientid=$txt_clientid." AI_facturaventa_id = '$value'";
	}else{
		$txt_clientid=$txt_clientid." AI_facturaventa_id = '$value' OR";
	}
}
$qry_clientid=$link->query($txt_clientid);
$row_clientid=$qry_clientid->fetch_array();
$client_id=$row_clientid['facturaventa_AI_cliente_id'];

$txt_facturaventa="SELECT
bh_facturaventa.AI_facturaventa_id, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero,
bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono,
bh_datoventa.AI_datoventa_id, bh_datoventa.datoventa_AI_producto_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_user_id, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_medida,
bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' ORDER BY AI_facturaventa_id ASC, AI_datoventa_id ASC ";
	}else{
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' OR";
	}
}
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$raw_facturaventa=array();
while ($rs_facturaventa=$qry_facturaventa->fetch_array()) {
	$raw_facturaventa[]=$rs_facturaventa;
}

?>


<table id="tbl_product2sell" class="table table-bordered table-condensed table-striped table-hover">
	<thead class="bg-primary">
		<tr>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Fact. Nº</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-3">Producto</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Medida</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Imp.%</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc.%</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
		  <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">  </th>
		</tr>
	</thead>
	<tbody><?php
		$sub_total= 0;	$total_itbm = 0;	$total_descuento = 0;
		foreach ($raw_facturaventa as $key => $value) {
			$descuento = (($value['TX_datoventa_descuento']*$value['TX_datoventa_precio'])/100);
			$precio_descuento = ($value['TX_datoventa_precio']-$descuento);
			$impuesto = (($value['TX_datoventa_impuesto']*$precio_descuento)/100);
			$precio_total = ($value['TX_datoventa_cantidad']*($precio_descuento+$impuesto));?>
			<tr ondblclick="open_popup('popup_loginadmin.php?a=<?php echo $str_factid ?>&b=<?php echo $client_id ?>&z=admin_datoventa.php','popup_loginadmin','425','420');">
				<td><?php echo $value['TX_producto_codigo']; ?> </td>
				<td><?php echo $value['TX_facturaventa_numero']; ?></td>
				<td><?php echo $r_function->replace_special_character($value['TX_datoventa_descripcion']); ?></td>
				<td><?php echo $raw_medida[$value['TX_datoventa_medida']]; ?></td>
				<td onclick="upd_quantityonnewcollect('<?php echo $value['AI_datoventa_id']; ?>');"><?php echo $value['TX_datoventa_cantidad']; ?></td>
				<td><?php echo number_format($value['TX_datoventa_precio'],2); ?></td>
				<td><?php echo number_format($impuesto,2).' ('.$value['TX_datoventa_impuesto'].'%)'; ?></td>
				<td><?php echo number_format($descuento,2).' ('.$value['TX_datoventa_descuento'].'%)'; ?></td>
				<td><?php echo number_format($precio_total,2); ?></td>
<?php 	$total_descuento+=$value['TX_datoventa_cantidad']*$descuento;								$total_itbm+=$value['TX_datoventa_cantidad']*$impuesto;
				$sub_total+=$value['TX_datoventa_cantidad']*$value['TX_datoventa_precio'];	$total = ($sub_total-$total_descuento)+$total_itbm;	?>
				<td class="al_center"><?php
					if($value['datoventa_AI_user_id'] != $_COOKIE['coo_iuser']){
						if($_COOKIE['coo_tuser'] < 3 && !empty($_SESSION['admin'])){ ?>
							<button type="button" name="<?php echo $value['datoventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect(this.name,'<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
<?php 			}
					}else{ ?>
						<button type="button" name="<?php echo $value['datoventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect(this.name,'<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
<?php 		}
	  ?>	</td>
			</tr><?php
		} ?>
	</tbody>
	<tfoot class="bg-primary">
		<td colspan="5"></td>
    <td><span id="span_nettotal"><?php echo $sub_total; ?></span></td>
    <td><strong>Imp: </strong> <br />B/ <span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span></td>
    <td><strong>Desc: </strong> <br />B/ <span id="span_discount"><?php echo number_format($total_descuento,2); ?></span></td>
    <td><strong>Total: </strong> <br />B/ <span id="span_total"><?php echo number_format($total,2); ?></span></td>
    <td></td>
	</tfoot>
</table>
