<?php
require '../../bh_conexion.php';
$link = conexion();
$fecha_actual = date('Y-m-d');
$product_id = $_GET['a'];
$medida_id = $_GET['b'];
$precio1 = $_GET['c'];
$precio2 = $_GET['d'];
$precio3 = $_GET['e'];
$precio4 = $_GET['f'];
$precio5 = $_GET['g'];

$qry_precio = $link->query("SELECT AI_precio_id FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND precio_AI_medida_id = '$medida_id' AND TX_precio_uno = '$precio1' AND TX_precio_dos = '$precio2' AND TX_precio_tres = '$precio3' AND TX_precio_cuatro = '$precio4' AND TX_precio_cinco = '$precio5'AND TX_precio_inactivo = '0' ")or die($link->error);
if($qry_precio->num_rows < 1){
	$link->query("UPDATE bh_precio SET TX_precio_inactivo='1' WHERE precio_AI_producto_id = '$product_id' AND precio_AI_medida_id = '$medida_id'")or die($link->error);
	$txt_insert_precio="INSERT INTO bh_precio (precio_AI_producto_id, precio_AI_medida_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha ) VALUES ('$product_id','$medida_id','$precio1','$precio2','$precio3','$precio4','$precio5','$fecha_actual')";
	$link->query($txt_insert_precio)or die($link->error);
}


//  ###########################   ANSWER

  $qry_precio_listado = $link->query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_producto.AI_producto_id FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$product_id' AND bh_precio.precio_AI_medida_id = '$medida_id' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die($link->error);


	while ($rs_precio_listado = $qry_precio_listado->fetch_array(MYSQLI_ASSOC)) {
?>
  	<tr>
  		<td><?php echo date('d-m-Y', strtotime($rs_precio_listado['TX_precio_fecha'])); ?></td>
  		<td><?php if (!empty($rs_precio_listado['TX_precio_cuatro'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cuatro'],2); } ?></td>
  		<td><?php if (!empty($rs_precio_listado['TX_precio_cinco'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cinco'],2); } ?></td>
  		<td><?php if (!empty($rs_precio_listado['TX_precio_tres'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_tres'],2); } ?></td>
  		<td><?php if (!empty($rs_precio_listado['TX_precio_dos'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_dos'],2); } ?></td>
  		<td><?php if (!empty($rs_precio_listado['TX_precio_uno'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_uno'],2); } ?></td>
  	</tr>
<?php
		}
?>
