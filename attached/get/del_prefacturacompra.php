<?php
require '../../bh_conexion.php';
$link = conexion();

$facturacompra_id=$_GET['a'];

	$qry_facturacompra=$link->query("SELECT AI_facturacompra_id FROM bh_facturacompra WHERE AI_facturacompra_id = '$facturacompra_id' AND TX_facturacompra_preguardado = 0")or die($link->error);
	if ($qry_facturacompra->num_rows > 0) {
		echo 'denied';
		return false;
	}

	$link->query("DELETE FROM bh_facturacompra WHERE AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
	$link->query("DELETE FROM bh_datocompra WHERE datocompra_AI_facturacompra_id = '$facturacompra_id'")or die($link->error);


// ################################   ANSWER  ###################

$qry_facturacompra=$link->query("SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_elaboracion, bh_facturacompra.TX_facturacompra_numero, bh_proveedor.TX_proveedor_nombre
FROM (bh_facturacompra INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id) WHERE bh_facturacompra.TX_facturacompra_preguardado = 1")or die($link->error);


  		if ($qry_facturacompra->num_rows > 0) {
					while ($rs_facturacompra=$qry_facturacompra->fetch_array()) {	?>
						<tr>
							<td><?php echo $rs_facturacompra['TX_facturacompra_elaboracion']; ?></td>
							<td><?php echo $rs_facturacompra['TX_facturacompra_fecha']; ?></td>
							<td><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?></td>
							<td><?php echo $rs_facturacompra['TX_proveedor_nombre']; ?></td>
							<td class="al_center">
								<button class="btn btn-warning btn-sm" id="btn_modificar" onclick="mod_facturacompra(<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>)"><i class="fa fa-wrench"></i></button>
								&nbsp;
								<button class="btn btn-danger btn-sm" id="btn_modificar" onclick="del_facturacompra(<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>)"><i class="fa fa-times"></i></button>
							</td>
						</tr>
<?php			}
				}else{ ?>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
<?php		}	?>
