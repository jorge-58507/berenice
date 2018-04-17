<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$nuevadevolucion_id=$_GET['a'];
$debito = $_GET['b'];

function get_rel_medida_cantidad($producto_id, $medida_id){
  $link=conexion();
  $prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);
  $prep_producto_medida->bind_param("ii", $producto_id, $medida_id); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
  $rs_producto_medida = $qry_producto_medida->fetch_array();
  $link->close();
  return $rs_producto_medida['TX_rel_productomedida_cantidad'];
}

$qry_nuevadevolucion = $link->query("SELECT nuevadevolucion_AI_datoventa_id, TX_nuevadevolucion_medida FROM bh_nuevadevolucion WHERE AI_nuevadevolucion_id = '$nuevadevolucion_id'")or die($link->error);
$rs_nuevadevolucion = $qry_nuevadevolucion->fetch_array();

$bh_del="DELETE FROM bh_nuevadevolucion WHERE AI_nuevadevolucion_id = '$nuevadevolucion_id' AND nuevadevolucion_AI_user_id = '$user_id'";
$link->query($bh_del) or die($link->error);

// ############################# ANSWER ####################

$multiplo = (100-$debito)/100;
$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$prep_datoventa=$link->prepare("SELECT AI_datoventa_id, datoventa_AI_producto_id, TX_datoventa_medida, TX_datoventa_cantidad, TX_datoventa_impuesto FROM bh_datoventa WHERE AI_datoventa_id = ?")or die($link->error);
$prep_rel_cant_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

$qry_nuevadevolucion=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida,
  bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.AI_nuevadevolucion_id, bh_nuevadevolucion.TX_nuevadevolucion_medida, bh_nuevadevolucion.nuevadevolucion_AI_producto_id, bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id,
  bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion
FROM ((bh_datoventa
       INNER JOIN bh_nuevadevolucion ON bh_datoventa.AI_datoventa_id = bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id)
      INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
      WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id'")or die($link->error);
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
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP%</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
      </tr>
    </thead>
    <tbody><?php
      $total_precio = 0; $total_impuesto = 0;
      while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array()){ ?>
        <tr>
        	<td><?php echo $rs_nuevadevolucion['TX_producto_codigo']; ?></td>
          <td><?php echo $rs_nuevadevolucion['TX_datoventa_descripcion']; ?></td>
          <td><?php echo $raw_medida[$rs_nuevadevolucion['TX_nuevadevolucion_medida']]; ?></td>
          <td><?php echo $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']; ?></td>
          <?php
            $prep_datoventa->bind_param("i",$rs_nuevadevolucion['nuevadevolucion_AI_datoventa_id']); $prep_datoventa->execute(); $qry_datoventa=$prep_datoventa->get_result();
            $rs_datoventa=$qry_datoventa->fetch_array();
            $rel_nuevadevolucion = get_rel_medida_cantidad($rs_nuevadevolucion['nuevadevolucion_AI_producto_id'],$rs_nuevadevolucion['TX_nuevadevolucion_medida']);
            $rel_datoventa = get_rel_medida_cantidad($rs_nuevadevolucion['nuevadevolucion_AI_producto_id'],$rs_datoventa['TX_datoventa_medida']);
            $rel_coheficiente = $rel_nuevadevolucion/$rel_datoventa;
            $descuento = ($rs_nuevadevolucion['TX_datoventa_descuento']*$rs_nuevadevolucion['TX_datoventa_precio'])/100;
            $precio_descuento = $rs_nuevadevolucion['TX_datoventa_precio']-$descuento;
            $precio_retencion = $precio_descuento*$multiplo;
            $cant_precio_descuento = ($rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*$rel_coheficiente)*$precio_retencion;
?>        <td><?php echo number_format($cant_precio_descuento,2); ?></td>
<?php       $impuesto = ($rs_nuevadevolucion['TX_datoventa_impuesto']*$precio_retencion)/100;
            $cant_precio_impuesto = ($rs_nuevadevolucion['TX_nuevadevolucion_cantidad']*$rel_coheficiente)*$impuesto; ?>
          <td><?php echo number_format($cant_precio_impuesto,2); ?></td>
          <td><button type="button" id="btn_delreturn" class="btn btn-danger btn-xs" onclick="del_return(<?php echo $rs_nuevadevolucion['AI_nuevadevolucion_id']; ?>);"><strong>X</strong></button></td>
        </tr><?php
        $total_precio += $cant_precio_descuento;
        $total_impuesto += $cant_precio_impuesto;
      } ?>
    </tbody>
    <tfoot class="bg-success">
      <tr>
      	<td colspan="4"></td>
        <td>
          <label for="span_preciowdescuento">Precio c/ Descuento:</label><br />
          B/ <span id="span_preciowdescuento"><?php echo number_format($total_precio,2); ?></span>
        </td>
        <td>
          <label for="span_impuesto">Impuesto:</label><br />
          B/ <span id="span_impuesto"><?php echo number_format($total_impuesto,2); ?></span>
        </td>
        <td>
          <label for="span_totalnc">Total:</label><br />
          B/ <span id="span_totalnc"><?php echo number_format($total_nc = $total_precio+$total_impuesto,2); ?></span>
        </td>
      </tr>
    </tfoot>
  </table>
