<?php
require '../../bh_conexion.php';
$link = conexion();

$nuevacompra_id=$_GET['a'];
$new_tax=$_GET['b'];

	$qry_nuevacompra=$link->query("SELECT AI_nuevacompra_id FROM bh_nuevacompra WHERE AI_nuevacompra_id = '$nuevacompra_id' AND nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
	$nr_nuevacompra=$qry_nuevacompra->num_rows;
	if($nr_nuevacompra > 0){
		$rs_nuevacompra=$qry_nuevacompra->fetch_array();
		$id=$rs_nuevacompra['AI_nuevacompra_id'];
		$bh_update="UPDATE bh_nuevacompra SET TX_nuevacompra_itbm='$new_tax' WHERE AI_nuevacompra_id = '$id'";
		$link->query($bh_update) or die ($link->error);
	}

	//  ######################        ANSWER               ####################
	$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
	$raw_medida = array();
	while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
		$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
	}
	$qry_newpurchase=$link->query("SELECT bh_nuevacompra.AI_nuevacompra_id, bh_nuevacompra.nuevacompra_AI_producto_id, bh_nuevacompra.TX_nuevacompra_unidades, bh_nuevacompra.TX_nuevacompra_precio, bh_nuevacompra.TX_nuevacompra_itbm, bh_nuevacompra.TX_nuevacompra_descuento, bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevacompra.TX_nuevacompra_p4, bh_nuevacompra.TX_nuevacompra_medida
	FROM (bh_nuevacompra INNER JOIN bh_producto ON bh_nuevacompra.nuevacompra_AI_producto_id = bh_producto.AI_producto_id)
	WHERE bh_nuevacompra.nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);

	$total_itbm = 0;	$total_descuento = 0;	$total = 0;
	while($rs_newpurchase=$qry_newpurchase->fetch_array()){
		$precio=$rs_newpurchase['TX_nuevacompra_unidades']*$rs_newpurchase['TX_nuevacompra_precio'];
		$descuento=($precio*$rs_newpurchase['TX_nuevacompra_descuento'])/100;
		$precio_descuento=$precio+$descuento;
		$impuesto=($precio_descuento*$rs_newpurchase['TX_nuevacompra_itbm'])/100;
		$precio_total=$precio_descuento+$impuesto;
?>
		<tr>
    	<td><?php echo $rs_newpurchase['TX_producto_codigo'] ?></td>
      <td><?php echo $r_function->replace_special_character($rs_newpurchase['TX_producto_value']); ?></td>
      <td><?php echo $raw_medida[$rs_newpurchase['TX_nuevacompra_medida']]; ?></td>
			<td onclick="upd_quantitynewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_unidades']; ?></td>
      <td onclick="upd_pricenewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_precio'] ?></td>
      <td><?php echo $rs_newpurchase['TX_nuevacompra_descuento']."% = ".number_format($descuento,4);	?></td>
      <td onclick="upd_taxnewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"			><?php echo $rs_newpurchase['TX_nuevacompra_itbm']."% = ".number_format($impuesto,4); ?></td>
      <td><?php	echo number_format($precio_total,4);	?></td>
      <td class="al_center"><button type="button" name="<?php echo $rs_newpurchase['AI_nuevacompra_id'] ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2purchase(this);"><strong>X</strong></button></td>
	  <td><span id="<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>" class="form-control" onclick="upd_newpurchase_price(this)"><?php echo number_format($rs_newpurchase['TX_nuevacompra_p4'],2);	?></span></td>
    </tr><?php
		$total_itbm += $impuesto;	$total_descuento += $descuento;	$total += $precio_total;
	};	?>
	<tr class="bg_green">
		<td colspan="5"></td>
		<td>B/ <?php echo number_format($total_descuento,4); ?></td>
		<td>B/ <?php echo number_format($total_itbm,4); ?></td>
		<td>B/ <?php echo number_format($total,2); ?></td>
		<td colspan="2"></td>
	</tr>
