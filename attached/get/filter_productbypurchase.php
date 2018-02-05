<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];


$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datocompra.TX_datocompra_cantidad, bh_datocompra.TX_datocompra_precio, ROUND((bh_datocompra.TX_datocompra_precio * (bh_datocompra.TX_datocompra_impuesto/100)),2) AS impuesto, ROUND(bh_datocompra.TX_datocompra_precio-(bh_datocompra.TX_datocompra_precio * (bh_datocompra.TX_datocompra_descuento/100)),2) AS precio FROM ((bh_facturacompra INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) INNER JOIN bh_producto ON bh_datocompra.datocompra_AI_producto_id = bh_producto.AI_producto_id) WHERE AI_facturacompra_id = '$value'";

//echo $txt_product;
$qry_product=mysql_query($txt_product." ORDER BY TX_producto_value");
$rs_product=mysql_fetch_assoc($qry_product);

$nr_product=mysql_num_rows($qry_product);



			?>


	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
        <th class="col-xs-5 col-sm-5 col-md-5 col-lg-3">Nombre</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-1">Cantidad</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Impuesto</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$total=0;
	if($nr_product > 0){
    do{
		$total += $rs_product['TX_datocompra_cantidad']*($rs_product['precio']+$rs_product['impuesto']);
        ?>
    <tr onclick="filter_purchasebyproduct('<?php echo $rs_product['AI_producto_id']; ?>');">
        <td><?php echo $rs_product['TX_producto_codigo']; ?></td>
        <td><?php echo $rs_product['TX_producto_value']; ?></td>
        <td><?php echo $rs_product['TX_datocompra_cantidad']; ?></td>
        <td><?php echo number_format($rs_product['precio'],2); ?></td>
        <td><?php echo number_format($rs_product['impuesto'],2); ?></td>
    </tr>
	<?php
    }while($rs_product=mysql_fetch_assoc($qry_product));
	}else{
	?>
	<tr>	<td></td><td></td><td></td><td></td><td></td><td></td>	</tr>
	<?php
	}?>
    </tbody>
	<tfoot class="bg-primary">
	<tr>	<td></td><td></td><td></td><td></td>
    <td><?php echo "$ ".number_format($total,2); ?></td>	
    </tr>
	</tfoot>
    </table>

