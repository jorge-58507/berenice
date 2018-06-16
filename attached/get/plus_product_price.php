<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

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

	$qry_producto=$link->query("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$rs_producto=$qry_producto->fetch_array(MYSQLI_ASSOC);
	$qry_medida=$link->query("SELECT TX_medida_value FROM bh_medida WHERE AI_medida_id = '$medida_id'")or die($link->error);
	$rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC);
	$file = fopen("../../precio_log.txt", "a");
	fwrite($file, date('d-m-Y H:i:s')." ".$rs_producto['TX_producto_value']." (".$product_id.")"." - ".$rs_medida['TX_medida_value']." - ".$_COOKIE['coo_suser'].PHP_EOL );
	fclose($file);

}


//  ###########################   ANSWER

  $qry_precio_listado = $link->query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_producto.AI_producto_id FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$product_id' AND bh_precio.precio_AI_medida_id = '$medida_id' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die($link->error);

$text='';
	while ($rs_precio_listado = $qry_precio_listado->fetch_array(MYSQLI_ASSOC)) {
		$text .='<tr><td>';
		$text .= date('d-m-Y', strtotime($rs_precio_listado['TX_precio_fecha'])).'</td><td>';
		$text .= (!empty($rs_precio_listado['TX_precio_cuatro'])) ? 'B/ '.number_format($rs_precio_listado['TX_precio_cuatro'],2).'</td><td>' : '</td><td>';
		$text .= (!empty($rs_precio_listado['TX_precio_cinco'])) ? 'B/ '.number_format($rs_precio_listado['TX_precio_cinco'],2).'</td><td>' : '</td><td>';
		$text .= (!empty($rs_precio_listado['TX_precio_tres'])) ? 'B/ '.number_format($rs_precio_listado['TX_precio_tres'],2).'</td><td>' : '</td><td>';
		$text .= (!empty($rs_precio_listado['TX_precio_dos'])) ? 'B/ '.number_format($rs_precio_listado['TX_precio_dos'],2).'</td><td>' : '</td><td>';
  	$text .= (!empty($rs_precio_listado['TX_precio_uno'])) ? 'B/ '.number_format($rs_precio_listado['TX_precio_uno'],2).'</td></tr>' : '</td></tr>';
		}
// echo $text;

$qry_product=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '{$_GET['a']}'")or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.AI_rel_productomedida_id, rel_producto_medida.TX_rel_productomedida_cantidad FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$_GET['a']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
}
$select='
<label class="label label_blue_sky"  for="sel_medida_descripcion">Medida:</label>
<select  class="form-control" id="sel_medida_descripcion" name="sel_medida_descripcion" tabindex="3">';
foreach ($raw_producto_medida as $key => $rs_medida) {
	if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
		$measure_selected=$rs_medida['TX_medida_value'];
		$select .= '<option value="'.$rs_medida['AI_medida_id'].'" selected="selected">'.$rs_medida['TX_medida_value'].'</option>';
	}else{
		$select .= '<option value="'.$rs_medida['AI_medida_id'].'">'.$rs_medida['TX_medida_value'].'</option>';
	}
}
$select .= '</select>';

$raw_response=array($text,$select);

echo json_encode($raw_response)
?>
