<?php
require '../../bh_conexion.php';
$link = conexion();

$medida_value=$_GET['a'];
$method=$_GET['b'];

switch ($method) {
	// case 'activate':
	// 	$link->query("UPDATE bh_area SET TX_area_status='1' WHERE AI_area_id = '$area_id'")or die($link->error);
	// 	break;
case 'desactivate':
	$qry_occupied = $link->query("SELECT AI_medida_id FROM (bh_medida INNER JOIN bh_producto ON bh_medida.AI_medida_id = bh_producto.TX_producto_medida) WHERE bh_medida.AI_medida_id = '$medida_value'")or die($link->error);
	if ($qry_occupied->num_rows < 1 ) {
		$link->query("DELETE FROM bh_medida WHERE AI_medida_id = '$medida_value'")or die($link->error);
	}
	echo "Eliminado!";
	break;
	case 'add':
		$link->query("INSERT INTO bh_medida (TX_medida_value, medida_AI_user_id) VALUES ('{$_GET['a']}','{$_COOKIE['coo_iuser']}')")or die($link->error);
		echo "Agregado Correctamente!";
	break;
}

	// $raw_proveedor=['id' => $area_id,'nombre'=> $method];
 	// echo json_encode($raw_proveedor);
  //

?>
