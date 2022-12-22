<?php
require '../../bh_conexion.php';
$link = conexion();

$area_id=$_GET['a'];
$method=$_GET['b'];

switch ($method) {
	case 'activate':
		$link->query("UPDATE bh_area SET TX_area_status='1' WHERE AI_area_id = '$area_id'")or die($link->error);
		break;
	case 'desactivate':
		$qry_area_occupied = $link->query("SELECT AI_area_id FROM (bh_area INNER JOIN bh_producto ON bh_area.AI_area_id = bh_producto.producto_AI_area_id) WHERE bh_area.AI_area_id = '$area_id'")or die($link->error);
		if ($qry_area_occupied->num_rows < 1 ) {
			$link->query("DELETE FROM bh_area WHERE AI_area_id = '$area_id'")or die($link->error);
		}else{
			$link->query("UPDATE bh_area SET TX_area_status='0' WHERE AI_area_id = '$area_id'")or die($link->error);
		}
		break;
	case 'add':
		$link->query("INSERT INTO bh_area (TX_area_value, TX_area_status, area_AI_user_id) VALUES ('{$_GET['a']}','1','{$_COOKIE['coo_iuser']}')")or die($link->error);
	break;

}

	$raw_proveedor=['id' => $area_id,'nombre'=> $method];
 	echo json_encode($raw_proveedor);

	// echo 'probando texto';

?>
