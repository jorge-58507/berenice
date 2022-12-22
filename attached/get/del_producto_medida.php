<?php
require '../../bh_conexion.php';
$link = conexion();

$rel_id=$_GET['a'];

$qry_rel = $link->query("SELECT productomedida_AI_medida_id, productomedida_AI_producto_id FROM rel_producto_medida WHERE AI_rel_productomedida_id = '$rel_id'")or die($link->error);
$rs_rel = $qry_rel->fetch_array(MYSQLI_ASSOC);
if ($qry_rel->num_rows > 0) {
//################ SI MEDIDA ES UNIDADES <--(YA NO) SI LA MEDIDA ES LA PREDETERMINADA O HAY SOLO 1 REL NO SE PODRA BORRAR
	//   VERIFICAR QUE NO SEA MEDIDA PREDETERMINADA (< A 0)
	$qry_producto = $link->query("SELECT TX_producto_medida FROM bh_producto WHERE AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}' AND TX_producto_medida = '{$rs_rel['productomedida_AI_medida_id']}'")or die($link->error);
	//   EXISTE LA MEDIDA?
	$qry_check_rel = $link->query("SELECT AI_rel_productomedida_id FROM rel_producto_medida WHERE productomedida_AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);

	if ($qry_producto->num_rows < 1 || $qry_check_rel->num_rows > 1) {
			$link->query("DELETE FROM rel_producto_medida WHERE AI_rel_productomedida_id = '$rel_id'")or die($link->error);
	}
}

// ############################  ANSWER   ##############

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje
																		FROM ((bh_medida
																		INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id)
																		INNER JOIN bh_letra ON bh_letra.AI_letra_id = rel_producto_medida.productomedida_AI_letra_id)
																		WHERE productomedida_AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);

$text='';

while($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)){
		$text .= '<tr>
		<td onclick="get_medida_precio('.$rs_producto_medida['AI_medida_id'].')" class="al_center">'.$rs_producto_medida['TX_medida_value'].'</td>
		<td class="al_center">'.$rs_producto_medida['TX_rel_productomedida_cantidad'].'</td>
		<td class="al_center">'.$rs_producto_medida['TX_letra_value']." (".$rs_producto_medida['TX_letra_porcentaje']."%)".'</td>
		<td class="al_center"><button type="button" class="btn btn-danger btn-sm" id="btn_delmedida" name="btn_delmedida" onclick="del_producto_medida('.$rs_producto_medida['AI_rel_productomedida_id'].')"><i class="fa fa-times"></i></button> </td>
	</tr>';
}



$qry_product=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje
FROM ((bh_medida
INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id)
INNER JOIN bh_letra ON bh_letra.AI_letra_id = rel_producto_medida.productomedida_AI_letra_id)
WHERE productomedida_AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);
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

$raw_response=array($text,$select);
echo json_encode($raw_response);
