<?php
require '../../bh_conexion.php';
$link = conexion();

$qry_rel = $link->query("SELECT AI_rel_productomedida_id FROM rel_producto_medida WHERE productomedida_AI_producto_id = '{$_GET['b']}' AND productomedida_AI_medida_id = '{$_GET['a']}' AND TX_rel_productomedida_cantidad = '{$_GET['c']}'")or die($link->error);
if ($qry_rel->num_rows < 1) {
	$link->query("INSERT INTO rel_producto_medida (productomedida_AI_medida_id, productomedida_AI_producto_id, TX_rel_productomedida_cantidad) VALUES ('{$_GET['a']}','{$_GET['b']}','{$_GET['c']}')")or die($link->error);
}

// ################## ANSWER

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$_GET['b']}'")or die($link->error);

				while($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)){ ?>
					<tr>
						<td onclick="get_medida_precio(<?php echo $rs_producto_medida['AI_medida_id']; ?>)" class="al_center"><?php echo $rs_producto_medida['TX_medida_value']; ?></td>
						<td class="al_center"><?php echo $rs_producto_medida['TX_rel_productomedida_cantidad']; ?></td>
						<td class="al_center"><button type="button" class="btn btn-danger btn-sm" id="btn_delmedida" name="btn_delmedida" onclick="del_producto_medida(<?php echo $rs_producto_medida['AI_rel_productomedida_id']; ?>)"><i class="fa fa-times"></i></button> </td>
					</tr>
<?php 	} ?>
