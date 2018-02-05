<?php
require '../../bh_con.php';
$link = conexion();

$product_id=$_GET['a'];

		$qry_checknuevaventa=mysql_query("SELECT * FROM bh_nuevaventa WHERE nuevaventa_AI_producto_id = '$product_id'", $link);
		$rs_checknuevaventa=mysql_fetch_assoc($qry_checknuevaventa);
		$nr_checknuevaventa=mysql_num_rows($qry_checknuevaventa);
		if($nr_checknuevaventa >= 1){
			$id=$rs_checknuevaventa['AI_nuevaventa_id'];
			$bh_del="DELETE FROM bh_datoventa WHERE AI_datoventa_id = '$id'";
			mysql_query($bh_del, $link) or die(mysql_error());			
		}
?>
    <table id="tbl_product2sell" class="table table-bordered table-hover">
    <caption>Lista de Productos para la Venta</caption>
	<thead>
        <tr>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-4">Producto</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">Medida</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">ITBM%</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc%</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
            <th class="bg-success col-xs-2 col-sm-2 col-md-1 col-lg-1">  </th>
        </tr>
    </thead>
    <tbody>
<?php

$qry_nuevaventa=mysql_query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevaventa.TX_nuevaventa_unidades, bh_nuevaventa.TX_nuevaventa_precio, bh_nuevaventa.TX_nuevaventa_itbm, bh_nuevaventa.TX_nuevaventa_descuento, bh_nuevaventa.nuevaventa_AI_producto_id FROM bh_producto, bh_nuevaventa WHERE bh_producto.AI_producto_id = bh_nuevaventa.nuevaventa_AI_producto_id AND bh_nuevaventa.nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_nuevaventa_id ASC");
$nr_nuevaventa=mysql_num_rows($qry_nuevaventa);

if($nr_nuevaventa > 0){
$rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa);
?>
<?php 
$total_itbm = 0;
$total_descuento = 0;
$total = 0;
?>
<?php do{ ?>
<?php
		$total_itbm = $total_itbm+($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_itbm']/100)));
		$total_descuento = $total_descuento+($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_descuento']/100)));
		$total = $total+($rs_nuevaventa['TX_nuevaventa_precio']*$rs_nuevaventa['TX_nuevaventa_unidades'])+
		($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_itbm']/100)))-
		($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_descuento']/100)));
?>

		<tr>
            <td><?php echo $rs_nuevaventa['TX_producto_codigo']; ?></td>
            <td><?php echo $rs_nuevaventa['TX_producto_value']; ?></td>
            <td><?php echo $rs_nuevaventa['TX_producto_medida']; ?></td>
            <td onclick="upd_unidadesnuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);">
			<?php echo $rs_nuevaventa['TX_nuevaventa_unidades']; ?>
            <span id="stock_quantity"><?php echo $rs_nuevaventa['TX_producto_cantidad']; ?></span>
            </td>
            <td><?php echo $rs_nuevaventa['TX_nuevaventa_precio']; ?></td>
            
            <td><?php echo $rs_nuevaventa['TX_nuevaventa_itbm']."% = ".
		$rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_itbm']/100)); ?></td>
            
            <td><?php echo $rs_nuevaventa['TX_nuevaventa_descuento']."% = ".
		$rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_descuento']/100)); ?></td>
            
            <td>
            <?php 
			echo
			($rs_nuevaventa['TX_nuevaventa_precio']*$rs_nuevaventa['TX_nuevaventa_unidades'])+
			($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_itbm']/100)))-
			($rs_nuevaventa['TX_nuevaventa_unidades']*($rs_nuevaventa['TX_nuevaventa_precio']*($rs_nuevaventa['TX_nuevaventa_descuento']/100)));
			?>
            </td>
            <td>
            <center>
            <button type="button" name="<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger" onclick="javascript: del_product2sell(this);"><strong>X</strong></button>
            </center>
            </td>
		</tr>
<?php }while($rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa)); ?>
<?php }else{ ?>
<?php 
$total_itbm = 0;
$total_descuento = 0;
$total = 0;
?>
		<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

		</tr>
<?php } ?>
    </tbody>
    <tfoot>
		<tr>
            <td class="bg-success"></td>
            <td class="bg-success"></td>
            <td class="bg-success"></td>
            <td class="bg-success"></td>
            <td class="bg-success"></td>
            <td class="bg-success">
            <strong>ITBM: </strong> <br /><span id="span_itbm"><?php echo $total_itbm; ?></span>
            </td>
            <td class="bg-success">
            <strong>Desc: </strong> <br /><span id="span_discount"><?php echo $total_descuento; ?></span>
            </td>
            <td class="bg-success">
            <strong>Total: </strong> <br /><span id="span_total"><?php echo $total; ?></span>
            </td>
            <td class="bg-success">  </td>
		</tr>
    </tfoot>
    </table>


