<?php
require '../../bh_conexion.php';
$link = conexion();

$facturaf_id=$_GET['a'];

$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad,
bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_facturaf.TX_facturaf_numero
FROM (((bh_facturaf
	INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
	WHERE AI_facturaf_id = '$facturaf_id'";

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value")or die ($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
$nr_product=$qry_product->num_rows;
?>
<table id="tbl_datofacturaf"  border="0" class="table table-bordered table-hover table-condensed table-striped">
	<caption class="caption">Productos incluidos Fact. #<?php echo $rs_product['TX_facturaf_numero']; ?></caption>
	<thead class="bg-primary">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
			<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">DESCRIPCION</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Impuesto</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P. Unitario</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Subtotal</th>
		</tr>
	</thead>
	<tbody>
<?php
		$total = 0;
		if($nr_product > 0){
	    do{
				$descuento4product = ($rs_product['TX_datoventa_descuento']*$rs_product['TX_datoventa_precio'])/100;
				$precio_descuento = $rs_product['TX_datoventa_precio']-$descuento4product;
				$impuesto4product = ($rs_product['TX_datoventa_impuesto']*$precio_descuento)/100;
				$precio_impuesto = $precio_descuento+$impuesto4product;
				$total+=$rs_product['TX_datoventa_cantidad']*($precio_descuento+$impuesto4product); ?>
		    <tr>
	        <td><?php echo $rs_product['TX_producto_codigo']; 		?></td>
	        <td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); 			?></td>
	        <td><?php echo $rs_product['TX_datoventa_cantidad']; 	?></td>
	        <td><?php echo number_format($precio_descuento,2); ?></td>
					<td><?php echo number_format($impuesto4product,2); ?></td>
					<td><?php echo number_format($precio_descuento+$impuesto4product,2); ?></td>
					<td><?php echo number_format($rs_product['TX_datoventa_cantidad']*($precio_descuento+$impuesto4product),2); ?></td>
		    </tr><?php
			}while($rs_product=$qry_product->fetch_array());
		}else{	?><tr>	<td colspan="7"></td>	</tr><?php	}?>
	</tbody>
	<tfoot class="bg-primary">
		<tr>
			<td colspan="6"></td>
			<td><?php echo "B/ ".number_format($total,2); ?></td>
		</tr>
	</tfoot>
</table>
