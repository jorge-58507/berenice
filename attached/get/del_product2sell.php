<?php
require '../../bh_conexion.php';
$link = conexion();

$product_id=$_GET['a'];

		$qry_checknuevaventa=$link->query("SELECT AI_nuevaventa_id FROM bh_nuevaventa WHERE nuevaventa_AI_producto_id = '$product_id'  AND nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'");
		$rs_checknuevaventa=$qry_checknuevaventa->fetch_array();
		$nr_checknuevaventa=$qry_checknuevaventa->num_rows;
		if($nr_checknuevaventa >= 1){
			$id=$rs_checknuevaventa['AI_nuevaventa_id'];
			$bh_del="DELETE FROM bh_nuevaventa WHERE AI_nuevaventa_id = '$id'";
			$link->query($bh_del) or die($link->error);
		}

		$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
		$raw_medida = array();
		while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
			$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
		}

// ################################   ANSWER  ###################

$qry_nuevaventa=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevaventa.TX_nuevaventa_unidades, bh_nuevaventa.TX_nuevaventa_precio, bh_nuevaventa.TX_nuevaventa_itbm, bh_nuevaventa.TX_nuevaventa_descuento, bh_nuevaventa.nuevaventa_AI_producto_id, bh_nuevaventa.AI_nuevaventa_id, bh_nuevaventa.TX_nuevaventa_descripcion, bh_nuevaventa.TX_nuevaventa_medida
  FROM (bh_producto INNER JOIN bh_nuevaventa ON bh_producto.AI_producto_id = bh_nuevaventa.nuevaventa_AI_producto_id)
  WHERE bh_nuevaventa.nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_nuevaventa_id ASC");
$nr_nuevaventa=$qry_nuevaventa->num_rows;


?>
<table id="tbl_product2sell" class="table table-bordered table-hover ">
<caption>Lista de Productos para la Venta</caption>
<thead class="bg_green">
    <tr>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
        <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Producto</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp.</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">P. Uni.</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">SubTotal</th>
        <th></th>
    </tr>
</thead>
<tbody>
  <?php
  if($nr_nuevaventa > 0){
  $rs_nuevaventa=$qry_nuevaventa->fetch_array(MYSQLI_ASSOC);

  $total_itbm = 0;
  $total_descuento = 0;
  $sub_total = 0;
  do{
    $descuento = (($rs_nuevaventa['TX_nuevaventa_descuento']*$rs_nuevaventa['TX_nuevaventa_precio'])/100);
    $precio_descuento = ($rs_nuevaventa['TX_nuevaventa_precio']-$descuento);
    $impuesto = (($rs_nuevaventa['TX_nuevaventa_itbm']*$precio_descuento)/100);
    $precio_unitario = round($precio_descuento+$impuesto,2);
    $precio_total = ($rs_nuevaventa['TX_nuevaventa_unidades']*($precio_unitario));

    $total_itbm += $rs_nuevaventa['TX_nuevaventa_unidades']*$impuesto;
    $total_descuento += $rs_nuevaventa['TX_nuevaventa_unidades']*$descuento;
    $sub_total += $rs_nuevaventa['TX_nuevaventa_unidades']*$rs_nuevaventa['TX_nuevaventa_precio'];
  ?>

      <tr>
        <td><?php echo $rs_nuevaventa['TX_producto_codigo']; ?></td>
        <td onclick="upd_nuevaventa_descripcion(<?php echo $rs_nuevaventa['AI_nuevaventa_id']; ?>)"><?php echo $rs_nuevaventa['TX_nuevaventa_descripcion']; ?></td>
        <td><?php echo $raw_medida[$rs_nuevaventa['TX_nuevaventa_medida']]; ?></td>
        <td onclick="upd_unidadesnuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);"><?php echo $rs_nuevaventa['TX_nuevaventa_unidades']; ?><span id="stock_quantity"><?php echo $rs_nuevaventa['TX_producto_cantidad']; ?></span></td>
        <td onclick="upd_precionuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);"><?php echo number_format($rs_nuevaventa['TX_nuevaventa_precio'],2); ?></td>
        <td><?php echo number_format($impuesto,2); ?></td>
        <td><?php echo number_format($descuento,2); ?></td>
        <td><?php echo number_format($precio_unitario,2); ?></td>
        <td><?php echo number_format($precio_total,2); ?></td>
        <td class="al_center"><button type="button" name="<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2sell(this);"><strong>X</strong></button></td>
      </tr>
  <?php }while($rs_nuevaventa=$qry_nuevaventa->fetch_array(MYSQLI_ASSOC)); ?>
  <?php }else{ ?>
  <?php 	$total_itbm = 0;	$total_descuento = 0;	$sub_total = 0;	?>
		      <tr>
		      	<td colspan="10"></td>
		      </tr>
  <?php }
  $total=($sub_total-$total_descuento)+$total_itbm;
  ?>
</tbody>
<tfoot class="bg_green">
    <tr>
        <td colspan="5"></td>
        <td><strong>T. Imp: </strong> <br /><span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span></td>
        <td><strong>T. Desc: </strong> <br /><span id="span_discount"><?php echo number_format($total_descuento,2); ?></span></td>
        <td></td>
        <td><strong>Total: </strong> <br /><span id="span_total"><?php echo number_format($total,2); ?></span></td>
        <td></td>
    </tr>
</tfoot>
</table>
