<?php
require '../../bh_con.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$id=$_GET['a'];
$cantidad=$_GET['b'];
$precio=$_GET['c'];
$impuesto=$_GET['e'];
$descuento=$_GET['d'];
$facturaventa_id=$_GET['f'];


	$qry_chkproduct=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$id'");
	$rs_chkproduct=mysql_fetch_assoc($qry_chkproduct);
	$existencia=$rs_chkproduct['TX_producto_cantidad'];
		$resta=$existencia-$cantidad;
		$bh_update="UPDATE bh_producto SET TX_producto_cantidad='$resta' WHERE AI_producto_id = '$id'";
		mysql_query($bh_update, $link) or die (mysql_error());
		
	mysql_query("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_stock)
VALUES ('$facturaventa_id', '{$_COOKIE['coo_iuser']}', '$id', '$cantidad', '$precio', '$impuesto', '$descuento', '$resta')");

?>
<?php
$qry_venta=mysql_query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_datoventa.datoventa_AI_producto_id, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_user_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_medida, bh_user.TX_user_seudonimo
FROM ((((bh_facturaventa 
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id) 
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id) 
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id) 
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id) 
WHERE AI_facturaventa_id = '$facturaventa_id'");
$nr_venta=mysql_num_rows($qry_venta);
$rs_venta=mysql_fetch_assoc($qry_venta);

?>

  
    <table id="tbl_product2sell" class="table table-bordered table-hover ">
    <caption>Lista de Productos para la Venta</caption>
	<thead>
        <tr>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-4">Producto</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">Medida</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">ITBM%</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc%</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
            <th class="bg-danger col-xs-2 col-sm-2 col-md-1 col-lg-1">  </th>
        </tr>
    </thead>
    <tbody>
<?php
if($nr_venta > 0){

?>
<?php 
$total_itbm = 0;
$total_descuento = 0;
$total = 0;
?>
<?php do{ ?>
<?php
		$total_itbm = $total_itbm+($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_impuesto']/100)));
		$total_descuento = $total_descuento+($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_descuento']/100)));
		$total = $total+($rs_venta['TX_datoventa_precio']*$rs_venta['TX_datoventa_cantidad'])+
		($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_impuesto']/100)))-
		($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_descuento']/100)));
?>

		<tr>
            <td><?php echo $rs_venta['TX_producto_codigo']; ?></td>
            <td><?php echo $rs_venta['TX_producto_value']; ?></td>
            <td><?php echo $rs_venta['TX_producto_medida']; ?></td>
            <td>
			<?php echo $rs_venta['TX_datoventa_cantidad']; ?>
            <span id="stock_quantity"><?php echo $rs_venta['TX_producto_cantidad']; ?></span>
            </td>
            <td><?php echo $rs_venta['TX_datoventa_precio']; ?></td>
            
            <td><?php echo $rs_venta['TX_datoventa_impuesto']."% = ".
	$rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_impuesto']/100)); ?></td>
            
            <td><?php echo $rs_venta['TX_datoventa_descuento']."% = ".
		$rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_descuento']/100)); ?></td>
            
            <td>
            <?php 
			echo
			($rs_venta['TX_datoventa_precio']*$rs_venta['TX_datoventa_cantidad'])+
			($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_impuesto']/100)))-
			($rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_descuento']/100)));
			?>
            </td>
            <td>
            <center>
            <?php if($rs_venta['datoventa_AI_user_id'] != $_COOKIE['coo_iuser']){ ?>
            <?php }else{ ?>
            <button type="button" name="<?php echo $rs_venta['datoventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger" onclick="javascript: del_product2addpaydesk(this);"><strong>X</strong></button>
            <?php } ?>
            </center>
            </td>
		</tr>
<?php }while($rs_venta=mysql_fetch_assoc($qry_venta)); ?>
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
            <td class="bg-danger"></td>
            <td class="bg-danger"></td>
            <td class="bg-danger"></td>
            <td class="bg-danger"></td>
            <td class="bg-danger"></td>
            <td class="bg-danger">
            <strong>ITBM: </strong> <br /><span id="span_itbm"><?php echo $total_itbm; ?></span>
            </td>
            <td class="bg-danger">
            <strong>Desc: </strong> <br /><span id="span_discount"><?php echo $total_descuento; ?></span>
            </td>
            <td class="bg-danger">
            <strong>Total: </strong> <br /><span id="span_total"><?php echo $total; ?></span>
            </td>
            <td class="bg-danger">  </td>
		</tr>
    </tfoot>
    </table>
   
    