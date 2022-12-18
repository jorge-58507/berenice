<?php
require '../../bh_conexion.php';
$link = conexion();

$producto_id = $_GET['b'];
$medida_id = $_GET['a'];
$cantidad = $_GET['c'];

$qry_rel = $link->query("SELECT AI_rel_productomedida_id FROM rel_producto_medida WHERE productomedida_AI_producto_id = '{$_GET['b']}' AND productomedida_AI_medida_id = '{$_GET['a']}' AND TX_rel_productomedida_cantidad = '{$_GET['c']}'")or die($link->error);

if ($qry_rel->num_rows < 1) {
	$link->query("INSERT INTO rel_producto_medida (productomedida_AI_medida_id, productomedida_AI_producto_id, TX_rel_productomedida_cantidad, productomedida_AI_letra_id, productomedida_AI_user_id) VALUES ('{$_GET['a']}','{$_GET['b']}','{$_GET['c']}','{$_GET['d']}','{$_COOKIE['coo_iuser']}')")or die($link->error);
	$message = 'Agregado Exitosamente.';
}else{
	$rs_rel=$qry_rel->fetch_Array(MYSQLI_ASSOC);
	$link->query("UPDATE rel_producto_medida SET productomedida_AI_letra_id = '{$_GET['d']}' WHERE AI_rel_productomedida_id = '{$rs_rel['AI_rel_productomedida_id']}'")or die($link->error);
	$message = 'Informac&iacute;on Actualizada.';
}

// ################################### ANSWER  ###################################

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje
FROM ((bh_medida
INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id)
INNER JOIN bh_letra ON bh_letra.AI_letra_id = rel_producto_medida.productomedida_AI_letra_id)
WHERE productomedida_AI_producto_id = '{$_GET['b']}'")or die($link->error);

$text='';

while($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)){
		$text .= '<tr>
		<td onclick="get_medida_precio('.$rs_producto_medida['AI_medida_id'].')" class="al_center">'.$rs_producto_medida['TX_medida_value'].'</td>
		<td class="al_center">'.$rs_producto_medida['TX_rel_productomedida_cantidad'].'</td>
		<td class="al_center">'.$rs_producto_medida['TX_letra_value']." (".$rs_producto_medida['TX_letra_porcentaje']."%)".'</td>
		<td class="al_center"><button type="button" class="btn btn-danger btn-sm" id="btn_delmedida" name="btn_delmedida" onclick="del_producto_medida('.$rs_producto_medida['AI_rel_productomedida_id'].')"><i class="fa fa-times"></i></button> </td>
	</tr>';
}



$qry_product=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '{$_GET['b']}'")or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.AI_rel_productomedida_id, rel_producto_medida.TX_rel_productomedida_cantidad FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$_GET['b']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
}
$select='
<label class="label label_blue_sky"  for="sel_medida_descripcion">Medida:</label>
<select  class="form-control input-sm" id="sel_medida_descripcion" name="sel_medida_descripcion" tabindex="3">';
foreach ($raw_producto_medida as $key => $rs_medida) {
	if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
		$measure_selected=$rs_medida['TX_medida_value'];
		$select .= '<option value="'.$rs_medida['AI_medida_id'].'" selected="selected">'.$rs_medida['TX_medida_value'].'</option>';
	}else{
		$select .= '<option value="'.$rs_medida['AI_medida_id'].'">'.$rs_medida['TX_medida_value'].'</option>';
	}
}
$select .= '</select>';

$raw_response=array($text,$select,$message);
echo json_encode($raw_response);
