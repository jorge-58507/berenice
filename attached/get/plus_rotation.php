<?php
ini_set('max_execution_time', 700);
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$fecha=date('Y-m-d',strtotime($_GET['a']));
$fecha_year=date('Y',strtotime($fecha));

$qry_rotation=$link->prepare("SELECT AI_rotacion_id, TX_rotacion_json FROM bh_rotacion WHERE TX_rotacion_ciclo = '$fecha_year' AND rotacion_AI_producto_id = ? ")or die($link->error);
$upd_rotacion_json=$link->prepare("UPDATE bh_rotacion SET TX_rotacion_json = ? WHERE rotacion_AI_producto_id = ? AND TX_rotacion_ciclo = ?")or die($link->error);
$ins_rotacion=$link->prepare("INSERT INTO bh_rotacion (rotacion_AI_producto_id, TX_rotacion_ciclo, TX_rotacion_json) VALUES (?,?,?)")or die($link->error);

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_cantidad FROM bh_producto ORDER BY AI_producto_id")or die($link->error);
while($rs_product = $qry_product->fetch_array()){

	$qry_rotation->bind_param("i", $rs_product['AI_producto_id']);
	$qry_rotation->execute()or die($link->error);
	$result_rotation =	$qry_rotation->get_result();

	if($result_rotation->num_rows > 0){

		$rs_rotacion=$result_rotation->fetch_array();
		$raw_json = json_decode($rs_rotacion['TX_rotacion_json'],true);
		if(empty($raw_json[$fecha_year][$fecha])){
			$raw_json[$fecha_year][$fecha]=$rs_product['TX_producto_cantidad']*1;
			$upd_json = json_encode($raw_json);
			$upd_rotacion_json->bind_param("sis",$upd_json,$rs_product['AI_producto_id'],$fecha_year);
			$upd_rotacion_json->execute();
			$link->close();
			echo "<br />UPDATe: ".$rs_product['AI_producto_id'];
		}
	}else{
		$raw_new_json=array();
		$raw_new_json[$fecha_year][$fecha]=$rs_product['TX_producto_cantidad']*1;
		$new_json=json_encode($raw_new_json);
		$ins_rotacion->bind_param("iss", $rs_product['AI_producto_id'], $fecha_year, $new_json)or die($link->error);
		$ins_rotacion->execute()or die("error ".$link->error);
			echo "<br />insert: ".$rs_product['AI_producto_id'];
		$link->close();
	}
}
echo "All Right";
?>
