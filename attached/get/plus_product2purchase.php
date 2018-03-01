<?php
require '../../bh_con.php';
$link = conexion();

$id=$_GET['a'];
$cantidad=$_GET['b'];
$precio=$_GET['c'];
$impuesto=$_GET['e'];
$descuento=$_GET['d'];
$p4 =	$_GET['f'];


//	$qry_chkproduct=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$id'");
//	$rs_chkproduct=mysql_fetch_assoc($qry_chkproduct);
//	$existencia=$rs_chkproduct['TX_producto_cantidad'];
//		$suma=$cantidad+$existencia;
//		$bh_update="UPDATE bh_producto SET TX_producto_cantidad='$suma' WHERE AI_producto_id = '$id'";
//		mysql_query($bh_update, $link) or die (mysql_error());

	mysql_query("INSERT INTO bh_nuevacompra (nuevacompra_AI_user_id, nuevacompra_AI_producto_id, TX_nuevacompra_unidades, TX_nuevacompra_precio, TX_nuevacompra_itbm, TX_nuevacompra_descuento, TX_nuevacompra_p4)
VALUES ('{$_COOKIE['coo_iuser']}','$id', '$cantidad', '$precio', '$impuesto', '$descuento','$p4')");

//  ######################        ANSWER               ####################

$qry_newpurchase=mysql_query("SELECT bh_nuevacompra.AI_nuevacompra_id, bh_nuevacompra.nuevacompra_AI_producto_id, bh_nuevacompra.TX_nuevacompra_unidades, bh_nuevacompra.TX_nuevacompra_precio, bh_nuevacompra.TX_nuevacompra_itbm, bh_nuevacompra.TX_nuevacompra_descuento, bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevacompra.TX_nuevacompra_p4
	FROM (bh_nuevacompra INNER JOIN bh_producto ON bh_nuevacompra.nuevacompra_AI_producto_id = bh_producto.AI_producto_id)
	WHERE bh_nuevacompra.nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die(mysql_error());
$rs_newpurchase=mysql_fetch_assoc($qry_newpurchase)

?>
<table id="tbl_newentry" class="table table-bordered table-condensed table-striped">
	<thead class="bg_green">
    <tr>
    	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
      <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Producto</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc%</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">ITBM%</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">SubTotal</th>
      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">P. Regular</th>
    </tr>
    </thead>

    <tbody>
    <?php
		$total_itbm = 0;
		$total_descuento = 0;
		$total = 0;
	do{

		$precio=$rs_newpurchase['TX_nuevacompra_unidades']*$rs_newpurchase['TX_nuevacompra_precio'];
		$descuento=($precio*$rs_newpurchase['TX_nuevacompra_descuento'])/100;
		$precio_descuento=$precio+$descuento;
		$impuesto=($precio_descuento*$rs_newpurchase['TX_nuevacompra_itbm'])/100;
		$precio_total=$precio_descuento+$impuesto;

		?>
    <tr>
    	<td><?php echo $rs_newpurchase['TX_producto_codigo'] ?></td>
        <td><?php echo $rs_newpurchase['TX_producto_value'] ?></td>
        <td><?php echo $rs_newpurchase['TX_producto_medida'] ?></td>
				<td onclick="upd_quantitynewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_unidades']; ?></td>

        <td><?php echo $rs_newpurchase['TX_nuevacompra_precio'] ?></td>
        <td><?php echo $rs_newpurchase['TX_nuevacompra_descuento']."% = ".
		number_format($descuento,4);
		 ?></td>
        <td><?php echo $rs_newpurchase['TX_nuevacompra_itbm']."% = ".
		number_format($impuesto,4);
		 ?></td>
        <td><?php
		echo number_format($precio_total,4);
		?></td>
        <td>
        <center>
        <button type="button" name="<?php echo $rs_newpurchase['nuevacompra_AI_producto_id'] ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2purchase(this);"><strong>X</strong></button>
        </center>
        </td>
				<td><span id="<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>" class="form-control" onclick="upd_newpurchase_price(this)"><?php echo number_format($rs_newpurchase['TX_nuevacompra_p4'],2);	?></span></td>
    </tr>
<?php
		$total_itbm += $impuesto;
		$total_descuento += $descuento;
		$total += $precio_total;
?>
    <?php }while($rs_newpurchase=mysql_fetch_assoc($qry_newpurchase));?>
    </tbody>
	<tfoot class="bg_green">
    <tr>
    	<td></td><td></td><td></td><td></td><td></td>
        <td>B/ <?php echo number_format($total_descuento,4); ?></td>
        <td>B/ <?php echo number_format($total_itbm,4); ?></td>
        <td>B/ <?php echo number_format($total,2); ?></td>
				<td></td>
				<td></td>
    </tr>
    </tfoot>
</table>
