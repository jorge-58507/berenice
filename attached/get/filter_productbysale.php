<?php
require '../../bh_conexion.php';
$link = conexion();

$facturaf_id=$_GET['a'];


$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad,
bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
FROM (((bh_facturaf
	INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
	WHERE AI_facturaf_id = '$facturaf_id'";

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value")or die ($link->error);
$nr_product=$qry_product->num_rows;
?>

		<?php
	if($nr_product > 0){
    while($rs_product=$qry_product->fetch_array()){
			$descuento4product = ($rs_product['TX_datoventa_descuento']*$rs_product['TX_datoventa_precio'])/100;
			$precio_descuento = $rs_product['TX_datoventa_precio']-$descuento4product;
			$impuesto4product = ($rs_product['TX_datoventa_impuesto']*$precio_descuento)/100;
			$precio_impuesto = $precio_descuento+$impuesto4product;
        ?>
    <tr onclick="filter_salebyproduct('<?php echo $rs_product['AI_producto_id']; ?>');">
        <td><?php echo $rs_product['TX_producto_codigo']; ?></td>
        <td><?php echo $rs_product['TX_producto_value']; ?></td>
        <td><?php echo $rs_product['TX_datoventa_cantidad']; ?></td>
        <td><?php echo number_format($precio_impuesto,2); ?></td>
    </tr>
	<?php
	}
	}else{
	?>
	<tr>	<td></td><td></td><td></td><td></td>	</tr>
	<?php
	}?>
