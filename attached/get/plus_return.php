<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$datoventa_id = $_GET['a'];
$cantidad = $_GET['b'];
$debito = $_GET['c'];

$qry_datoventa=$link->query("SELECT datoventa_AI_producto_id FROM bh_datoventa WHERE AI_datoventa_id = '$datoventa_id'");
$row_datoventa=$qry_datoventa->fetch_array();
$txt_chkreturn="SELECT AI_nuevadevolucion_id FROM bh_nuevadevolucion WHERE nuevadevolucion_AI_producto_id = '$row_datoventa[0]' AND nuevadevolucion_AI_user_id = '$user_id'";
$qry_checkreturn=$link->query($txt_chkreturn)or die($link->error);
$nr_checkreturn=$qry_checkreturn->num_rows;
if($nr_checkreturn < 1){
  $link->query("INSERT INTO bh_nuevadevolucion (nuevadevolucion_AI_producto_id, nuevadevolucion_AI_datoventa_id, nuevadevolucion_AI_user_id, TX_nuevadevolucion_cantidad) VALUES ('$row_datoventa[0]','$datoventa_id','$user_id','$cantidad')")or die($link->error);
}

$multiplo = (100-$debito)/100;

//############################## ANSWER   ##########################

$qry_nuevadevolucion=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.AI_nuevadevolucion_id, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion
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
<?php $total_precio = 0; ?>
<?php $total_impuesto = 0; ?>
<?php do{ ?>
    <tr>
    	<td><?php echo $rs_nuevadevolucion['TX_producto_codigo']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_datoventa_descripcion']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_producto_medida']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']; ?></td>
      <td><?php
          $preciowdescuento = $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*($rs_nuevadevolucion['TX_datoventa_precio']*$multiplo) - ($rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*($rs_nuevadevolucion['TX_datoventa_precio']*($rs_nuevadevolucion['TX_datoventa_descuento']/100)));
		 echo number_format($preciowdescuento,2); ?></td>
      <td><?php
          $impuesto = $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*(($rs_nuevadevolucion['TX_datoventa_precio']*$multiplo)*($rs_nuevadevolucion['TX_datoventa_impuesto']/100));
		 echo number_format($impuesto,2); ?></td>
      <td>
        <button type="button" id="btn_delreturn" class="btn btn-danger btn-xs" onclick="del_return(<?php echo $rs_nuevadevolucion['AI_nuevadevolucion_id']; ?>);"><strong>X</strong></button>
      </td>
    </tr>
<?php $total_precio += $preciowdescuento; ?>
<?php $total_impuesto += $impuesto; ?>
<?php }while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array()); ?>
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
