<?php
require '../../bh_conexion.php';
$link = conexion();

$nuevacompra_id=$_GET['a'];
$new_quantity=$_GET['b'];

	$qry_nuevacompra=$link->query("SELECT AI_nuevacompra_id FROM bh_nuevacompra WHERE AI_nuevacompra_id = '$nuevacompra_id' AND nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
	$nr_nuevacompra=$qry_nuevacompra->num_rows;
	if($nr_nuevacompra > 0){
		$rs_nuevacompra=$qry_nuevacompra->fetch_array();
		$id=$rs_nuevacompra['AI_nuevacompra_id'];
		$bh_update="UPDATE bh_nuevacompra SET TX_nuevacompra_unidades='$new_quantity' WHERE AI_nuevacompra_id = '$id'";
		$link->query($bh_update) or die ($link->error);
	}

	$qry_newpurchase=$link->query("SELECT bh_nuevacompra.AI_nuevacompra_id, bh_nuevacompra.nuevacompra_AI_producto_id, bh_nuevacompra.TX_nuevacompra_unidades, bh_nuevacompra.TX_nuevacompra_precio, bh_nuevacompra.TX_nuevacompra_itbm, bh_nuevacompra.TX_nuevacompra_descuento, bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevacompra.TX_nuevacompra_p4
		FROM (bh_nuevacompra
		INNER JOIN bh_producto ON bh_nuevacompra.nuevacompra_AI_producto_id = bh_producto.AI_producto_id)
		WHERE bh_nuevacompra.nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
	$rs_newpurchase=$qry_newpurchase->fetch_array();

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
  if($qry_newpurchase->num_rows > 0){
		do{
			$precio4product=$rs_newpurchase['TX_nuevacompra_unidades']*$rs_newpurchase['TX_nuevacompra_precio'];
			$descuento4product=($rs_newpurchase['TX_nuevacompra_descuento']*$precio4product)/100;
			$total_descuento+=$descuento4product;
			$precio_descuento=$precio4product-$descuento4product;
			$impuesto4product=($rs_newpurchase['TX_nuevacompra_itbm']*$precio_descuento)/100;
			$total_itbm+=$impuesto4product;
			$total_desc_imp=$precio_descuento+$impuesto4product;
			$total+=$total_desc_imp;
		?>
    <tr>
    	<td><?php echo $rs_newpurchase['TX_producto_codigo']; ?></td>
      <td><?php echo $rs_newpurchase['TX_producto_value']; ?></td>
      <td><?php echo $rs_newpurchase['TX_producto_medida']; ?></td>
      <td onclick="upd_quantitynewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_unidades']; ?></td>
      <td><?php echo $rs_newpurchase['TX_nuevacompra_precio']; ?></td>
      <td><?php echo $rs_newpurchase['TX_nuevacompra_descuento']."% = ".number_format($descuento4product,4);?></td>
      <td><?php echo $rs_newpurchase['TX_nuevacompra_itbm']."% = ".number_format($impuesto4product,4); ?></td>
      <td><?php echo number_format($total_desc_imp,4);	?></td>
      <td>
      <center>
      <button type="button" name="<?php echo $rs_newpurchase['nuevacompra_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2purchase(this);"><strong>X</strong></button>
      </center>
      </td>
			<td><span id="<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>" class="form-control" onclick="upd_newpurchase_price(this)"><?php echo number_format($rs_newpurchase['TX_nuevacompra_p4'],2);	?></span></td>
    </tr>
<?php 	}while($rs_newpurchase=$qry_newpurchase->fetch_array());?>
<?php }else{ ?>
    <tr>
    	<td></td><td></td><td></td><td></td><td></td>
        <td></td><td></td><td></td><td></td>
    </tr>
    <?php } ?>
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
