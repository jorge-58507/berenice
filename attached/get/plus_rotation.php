<?php
ini_set('max_execution_time', 700);
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$fecha=date('Y-m-d',strtotime($_GET['a']));
// $fecha = '2018-07-29';
$fecha_year=date('Y',strtotime($fecha));

$prep_rotation=$link->prepare("SELECT AI_rotacion_id, TX_rotacion_json FROM bh_rotacion WHERE TX_rotacion_ciclo = '$fecha_year' AND rotacion_AI_producto_id = ? ")or die($link->error);
$upd_rotacion_json=$link->prepare("UPDATE bh_rotacion SET TX_rotacion_json = ? WHERE rotacion_AI_producto_id = ? AND TX_rotacion_ciclo = ?")or die($link->error);
$ins_rotacion=$link->prepare("INSERT INTO bh_rotacion (rotacion_AI_producto_id, TX_rotacion_ciclo, TX_rotacion_json) VALUES (?,?,?)")or die($link->error);

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_cantidad FROM bh_producto WHERE TX_producto_inventariado = 1 ORDER BY AI_producto_id")or die($link->error);
while($rs_product = $qry_product->fetch_array()){
	$prep_rotation->bind_param("i", $rs_product['AI_producto_id']);
	$prep_rotation->execute(); 	$qry_rotation=$prep_rotation->get_result();
	if($qry_rotation->num_rows > 0){
		//  #################### AGREGAR UNA ROTACION MAS
		$rs_rotacion=$qry_rotation->fetch_array(MYSQLI_ASSOC);
		$raw_json = json_decode($rs_rotacion['TX_rotacion_json'],true);
		if(empty($raw_json[$fecha_year][$fecha])){
			$raw_json[$fecha_year][$fecha]=$rs_product['TX_producto_cantidad']*1;
			$upd_json = json_encode($raw_json);
			$upd_rotacion_json->bind_param("sis",$upd_json,$rs_product['AI_producto_id'],$fecha_year);
			$upd_rotacion_json->execute();
		}
	}else{
		//  #################### CREAR UNA ROTACION MAS
		$raw_new_json=array();
		$raw_new_json[$fecha_year][$fecha]=$rs_product['TX_producto_cantidad']*1;
		$new_json=json_encode($raw_new_json);
		$ins_rotacion->bind_param("iss", $rs_product['AI_producto_id'], $fecha_year, $new_json)or die($link->error);
		$ins_rotacion->execute();
	}
}
echo "All Right";
?>
