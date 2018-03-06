<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturacompra_fecha >=	'$date_i' AND TX_facturacompra_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturacompra="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_almacen.TX_almacen_value, bh_facturacompra.TX_facturacompra_ordendecompra, bh_proveedor.TX_proveedor_nombre, bh_facturacompra.TX_facturacompra_elaboracion
FROM ((bh_facturacompra
      INNER JOIN bh_proveedor ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
	  INNER JOIN bh_almacen ON bh_facturacompra.TX_facturacompra_almacen = bh_almacen.AI_almacen_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado = 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado = 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado = 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra .= " ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC".$line_limit;

$qry_facturacompra=$link->query($txt_facturacompra)or die($link->error);
$rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC);
$nr_facturacompra=$qry_facturacompra->num_rows;

		if($nr_facturacompra > 0){
		do{ ?>
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
<?php }while($rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC));
		}else{?>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
<?php } ?>
