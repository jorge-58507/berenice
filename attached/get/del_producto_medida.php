<?php
require '../../bh_conexion.php';
$link = conexion();

$rel_id=$_GET['a'];

$qry_rel = $link->query("SELECT productomedida_AI_medida_id, productomedida_AI_producto_id FROM rel_producto_medida WHERE AI_rel_productomedida_id = '$rel_id'")or die($link->error);
$rs_rel = $qry_rel->fetch_array(MYSQLI_ASSOC);

//################ SI MEDIDA ES UNIDADES O HAY SOLO 1 REL NO SE PODRA BORRAR
	$qry_producto = $link->query("SELECT TX_producto_medida FROM bh_producto WHERE AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}' AND TX_producto_medida = '{$rs_rel['productomedida_AI_medida_id']}'")or die($link->error);
	$qry_check_rel = $link->query("SELECT AI_rel_productomedida_id FROM rel_producto_medida WHERE productomedida_AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);
	if ($qry_producto->num_rows < 1 && $qry_check_rel->num_rows > 1 && $rs_rel['productomedida_AI_medida_id'] != 1) {
			$link->query("DELETE FROM rel_producto_medida WHERE AI_rel_productomedida_id = '$rel_id'")or die($link->error);
	}

			// ################## ANSWER

			$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$rs_rel['productomedida_AI_producto_id']}'")or die($link->error);

							while($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)){ ?>
								<tr>
									<td onclick="get_medida_precio(<?php echo $rs_producto_medida['AI_medida_id']; ?>)" class="al_center"><?php echo $rs_producto_medida['TX_medida_value']; ?></td>
									<td class="al_center"><?php echo $rs_producto_medida['TX_rel_productomedida_cantidad']; ?></td>
									<td class="al_center"><button type="button" class="btn btn-danger btn-sm" id="btn_delmedida" name="btn_delmedida" onclick="del_producto_medida(<?php echo $rs_producto_medida['AI_rel_productomedida_id']; ?>)"><i class="fa fa-times"></i></button> </td>
								</tr>
			<?php 	} ?>
