<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$nuevadevolucion_id=$_GET['a'];
$bh_del="DELETE FROM bh_nuevadevolucion WHERE AI_nuevadevolucion_id = '$nuevadevolucion_id' AND nuevadevolucion_AI_user_id = '$user_id'";
$link->query($bh_del) or die($link->error);

// ############################# ANSWER ####################
?>
<?php
$qry_nuevadevolucion=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.AI_nuevadevolucion_id, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
FROM ((bh_datoventa
       INNER JOIN bh_nuevadevolucion ON bh_datoventa.AI_datoventa_id = bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id)
      INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
      WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id'");
$rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array();
?>
<table id="tbl_return" class="table table-bordered table-striped table-condensed">
    <caption>Productos a Reingresar</caption>
    <thead class="bg-success">
	    <tr>
	    	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
	      <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Producto</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
	      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">ITBM%</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	    </tr>
    </thead>
    <tbody>
<?php
  $total_precio = 0; $total_impuesto = 0;
  if ($qry_nuevadevolucion->num_rows > 0) {
	do{ ?>
    <tr>
    	<td><?php echo $rs_nuevadevolucion['TX_producto_codigo']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_producto_value']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_producto_medida']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']; ?></td>
      <td><?php
		 echo number_format($preciowdescuento = $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*$rs_nuevadevolucion['TX_datoventa_precio'] - ($rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*($rs_nuevadevolucion['TX_datoventa_precio']*($rs_nuevadevolucion['TX_datoventa_descuento']/100))),2); ?></td>
      <td><?php
		 echo number_format($impuesto = $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*($rs_nuevadevolucion['TX_datoventa_precio']*($rs_nuevadevolucion['TX_datoventa_impuesto']/100)),2); ?></td>
      <td>
        <button type="button" id="btn_delreturn" class="btn btn-danger btn-xs" onclick="del_return(<?php echo $rs_nuevadevolucion['AI_nuevadevolucion_id']; ?>);"><strong>X</strong></button>
      </td>
    </tr>
<?php $total_precio += $preciowdescuento; ?>
<?php $total_impuesto += $impuesto; ?>
<?php }while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array());
}else{ ?>
<tr> <td colspan="7"></td>
<?php } ?>


    </tbody>
    <tfoot class="bg-success">
    <tr>
    	<td></td><td></td><td></td><td></td>
      <td>
        <label for="span_preciowdescuento">Precio c/ Descuento:</label><br />
        B/ <span id="span_preciowdescuento"><?php echo number_format($total_precio,2); ?></span></td>
      <td>
        <label for="span_impuesto">Impuesto:</label><br />
        B/ <span id="span_impuesto"><?php echo number_format($total_impuesto,2); ?></span></td>
      <td>
        <label for="span_totalnc">Total:</label><br />
        B/ <span id="span_totalnc"><?php echo number_format($total_nc = $total_precio+$total_impuesto,2); ?></span></td>
    </tr>
    </tfoot>
    </table>
