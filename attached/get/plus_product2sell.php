<?php
require '../../bh_conexion.php';
$link = conexion();

$uid=$_COOKIE['coo_iuser'];
$id=$_GET['a'];
$cantidad=$_GET['b'];
$precio=round($_GET['c'],2);
$impuesto=$_GET['e'];
$descuento=$_GET['d'];
$medida=$_GET['f'];

	$qry_product = $link->query("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = '$id'")or die($link->error);
	$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
	$descripcion = $r_function->replace_regular_character($rs_product['TX_producto_value']);

	$qry_chkproduct=$link->query("SELECT AI_nuevaventa_id FROM bh_nuevaventa WHERE nuevaventa_AI_producto_id = '$id' AND nuevaventa_AI_user_id = '$uid'");
	$nr_chkproduct=$qry_chkproduct->num_rows;
	if($nr_chkproduct < 1){
		$link->query("INSERT INTO bh_nuevaventa (nuevaventa_AI_user_id, nuevaventa_AI_producto_id, TX_nuevaventa_unidades, TX_nuevaventa_precio, TX_nuevaventa_itbm, TX_nuevaventa_descuento, TX_nuevaventa_descripcion, TX_nuevaventa_medida) VALUES ('$uid', '$id', '$cantidad', '$precio', '$impuesto', '$descuento', '$descripcion', '$medida')")or die($link->error);
	}


//  ##############################        ANSWER
$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}
$qry_nuevaventa=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevaventa.TX_nuevaventa_unidades, bh_nuevaventa.TX_nuevaventa_precio, bh_nuevaventa.TX_nuevaventa_itbm, bh_nuevaventa.TX_nuevaventa_descuento, bh_nuevaventa.nuevaventa_AI_producto_id, bh_nuevaventa.TX_nuevaventa_descripcion, bh_nuevaventa.AI_nuevaventa_id, bh_nuevaventa.TX_nuevaventa_medida
	 FROM (bh_producto
	 INNER JOIN bh_nuevaventa ON bh_producto.AI_producto_id = bh_nuevaventa.nuevaventa_AI_producto_id)
	 WHERE bh_nuevaventa.nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_nuevaventa_id ASC");
$nr_nuevaventa=$qry_nuevaventa->num_rows;

?>


    <table id="tbl_product2sell" class="table table-bordered table-hover">
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
			      $total_itbm = 0; $total_descuento = 0; $total = 0;
			      if($nr_nuevaventa > 0){
			        $rs_nuevaventa=$qry_nuevaventa->fetch_array();
			        do{
			      	 	$precio = $rs_nuevaventa['TX_nuevaventa_precio'];
			          $descuento = ($precio*$rs_nuevaventa['TX_nuevaventa_descuento'])/100;
			          $precio_descuento = $precio-$descuento;
			          $impuesto = ($precio_descuento*$rs_nuevaventa['TX_nuevaventa_itbm'])/100;
			      		$p_unitario = $precio_descuento+$impuesto;
			          $subtotal = $rs_nuevaventa['TX_nuevaventa_unidades']*$p_unitario;

			      		$total_descuento += $rs_nuevaventa['TX_nuevaventa_unidades']*$descuento;
			      		$total_itbm += $rs_nuevaventa['TX_nuevaventa_unidades']*$impuesto;
			          $total += $subtotal;
			?>
			      		<tr>
			            <td><?php echo $rs_nuevaventa['TX_producto_codigo']; ?></td>
			            <td onclick="upd_nuevaventa_descripcion(<?php echo $rs_nuevaventa['AI_nuevaventa_id']; ?>)"><?php echo $rs_nuevaventa['TX_nuevaventa_descripcion']; ?></td>
			            <td><?php echo $raw_medida[$rs_nuevaventa['TX_nuevaventa_medida']]; ?></td>
			            <td onclick="upd_unidadesnuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);"><?php echo $rs_nuevaventa['TX_nuevaventa_unidades']; ?></td>
			      			<td onclick="upd_precionuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);"><?php echo number_format($rs_nuevaventa['TX_nuevaventa_precio'],2); ?></td>
			            <td><?php echo number_format($impuesto,2); ?></td>
			            <td><?php echo number_format($descuento,2); ?></td>
			      			<td><?php echo number_format($p_unitario,2); ?></td>
			            <td><?php	echo number_format($subtotal,2);	?></td>
			            <td>
			            <center>
			            <button type="button" name="<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2sell(this);"><strong>X</strong></button>
			            </center>
			            </td>
			      		</tr>
			<?php       }while($rs_nuevaventa=$qry_nuevaventa->fetch_array());
			          }else{ ?>
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
			<?php     }   ?>
    </tbody>
    <tfoot>
		<tr class="bg_green">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
            <strong>T. Imp: </strong> <br /><span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span>
            </td>
            <td>
            <strong>T. Desc: </strong> <br /><span id="span_discount"><?php echo number_format($total_descuento,2); ?></span>
            </td>
						<td></td>
            <td>
            <strong>Total: </strong> <br /><span id="span_total"><?php echo number_format($total,2); ?></span>
            </td>
            <td>  </td>
		</tr>
    </tfoot>
    </table>
