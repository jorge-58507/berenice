<?php
require '../../bh_con.php';
$link = conexion();
session_start();
$facturaf_id=$_GET['a'];

$txt_datoventa="SELECT
bh_producto.TX_producto_value,
bh_datoventa.TX_datoventa_cantidad,
bh_datoventa.TX_datoventa_precio,
bh_datoventa.TX_datoventa_impuesto,
bh_datoventa.TX_datoventa_descuento,
bh_datoventa.TX_datoventa_descripcion,
bh_facturaf.TX_facturaf_deficit,
bh_facturaf.facturaf_AI_cliente_id
FROM (((bh_datoventa
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";

//echo $txt_datoventa;
$qry_datoventa = mysql_query($txt_datoventa);
$rs_datoventa = mysql_fetch_assoc($qry_datoventa);
?>
		<table id="tbl_datoventa" class="table table-bordered table-condensed table-striped">
        <thead class="bg-info">
        <tr>
        	<th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Producto</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php $total=0; ?>
        <?php do{ ?>
        <?php
		$precio=$rs_datoventa['TX_datoventa_cantidad']*$rs_datoventa['TX_datoventa_precio'];
		$descuento=($precio*$rs_datoventa['TX_datoventa_descuento'])/100;
		$precio_descuento=$precio-$descuento;
		$impuesto=($precio_descuento*$rs_datoventa['TX_datoventa_impuesto'])/100;
		$subtotal=$precio_descuento+$impuesto;
		$total += $subtotal;
		 ?>
        <tr>
        	<td><?php echo $rs_datoventa['TX_datoventa_descripcion']; ?></td>
        	<td><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></td>
        	<td><?php echo number_format($subtotal,2); ?></td>
        </tr>

        <?php	$deficit = $rs_datoventa['TX_facturaf_deficit'];
				$client_id = $rs_datoventa['facturaf_AI_cliente_id'];
			 }while($rs_datoventa = mysql_fetch_assoc($qry_datoventa)); ?>
        </tbody>
        <tfoot class="bg-info">
		<tr>
        	<th></th><th></th><th><strong>B/ <?php echo number_format($total,2); ?></strong></th>
        </tr>
        </tfoot>
        </table>
        <?php
			if(isset($_SESSION['admin'])){
		?>
        <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	    <button type="button" id="btn_makenc" class="btn btn-warning" onclick="window.opener.location='make_nc.php?a=<?php echo $facturaf_id; ?>'">N.C.</button>
            &nbsp;&nbsp;
            <?php  if($deficit > 0){ ?>
            <button type="button" id="btn_debit" class="btn btn-success" onclick="window.opener.location='new_debit.php?a=<?php echo $facturaf_id; ?>&b=<?php echo $client_id;?>'">DEBITAR</button>
            <?php  } ?>
        </div>
        <?php
			}
		?>
