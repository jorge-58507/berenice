<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$method=$_GET['b'];

switch ($method) {
	case 'activate':
		$link->query("UPDATE bh_familia SET TX_familia_status='1' WHERE AI_familia_id = '$value'")or die($link->error);
		break;
	case 'desactivate':
		$qry_familia_occupied = $link->query("SELECT bh_familia.AI_familia_id, bh_familia.TX_familia_value, count(bh_producto.producto_AI_subfamilia_id) as conteo
		FROM ((bh_familia
		INNER JOIN bh_subfamilia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
		LEFT JOIN bh_producto ON bh_subfamilia.AI_subfamilia_id = bh_producto.producto_AI_subfamilia_id)
		WHERE bh_familia.AI_familia_id = '$value'
		GROUP BY bh_familia.AI_familia_id
		ORDER BY bh_subfamilia.TX_subfamilia_value")or die($link->error);
		$rs_familia_occupied = $qry_familia_occupied->fetch_array(MYSQLI_ASSOC);
		if ($rs_familia_occupied['conteo'] < 1 ) {
			$link->query("DELETE FROM bh_familia WHERE AI_familia_id = '$value'")or die($link->error);
		}else{
			$link->query("UPDATE bh_familia SET TX_familia_status='0' WHERE AI_familia_id = '$value'")or die($link->error);
		}
		break;
	case 'add':
		$value = $r_function->url_replace_special_character($value);
		$value = $r_function->replace_regular_character($value);
		$link->query("INSERT INTO bh_familia (TX_familia_value, TX_familia_status, TX_familia_prefijo) VALUES ('$value','1','{$_GET['c']}')")or die($link->error);
	break;
	case 'set_subfamilia':
			$qry_subfamilia = $link->query("SELECT bh_subfamilia.AI_subfamilia_id, bh_subfamilia.TX_subfamilia_value, bh_subfamilia.TX_subfamilia_prefijo,
			bh_subfamilia.TX_subfamilia_status, bh_subfamilia.subfamilia_AI_familia_id, COUNT(bh_producto.producto_AI_subfamilia_id) AS conteo
			FROM ((bh_subfamilia
			INNER JOIN bh_familia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
			LEFT JOIN bh_producto ON bh_producto.producto_AI_subfamilia_id = bh_subfamilia.AI_subfamilia_id)
			WHERE bh_familia.AI_familia_id = '$value'
			GROUP BY AI_subfamilia_id
			ORDER BY bh_subfamilia.TX_subfamilia_value");
		$raw_subfamilia = array();
		while ($rs_subfamilia = $qry_subfamilia->fetch_array(MYSQLI_ASSOC)) {
			$raw_subfamilia[] = $rs_subfamilia;
		}
		echo json_encode($raw_subfamilia);
	break;


  // ################   SUBFAMILIA


	case 'add_subfamilia':
		$value = $r_function->url_replace_special_character($value);
		$value = $r_function->replace_regular_character($value);
		$link->query("INSERT INTO bh_subfamilia (TX_subfamilia_value, TX_subfamilia_status, subfamilia_AI_familia_id, TX_subfamilia_prefijo ) VALUES ('$value','1','{$_GET['c']}','{$_GET['d']}')")or die($link->error);
		echo 'agregado Correctamente';
	break;

	case 'activate_subfamilia':
		$link->query("UPDATE bh_subfamilia SET TX_subfamilia_status='1' WHERE AI_subfamilia_id = '$value'")or die($link->error);
	break;

	case 'desactivate_subfamilia':
		$qry_subfamilia_occupied = $link->query("SELECT bh_subfamilia.AI_subfamilia_id, bh_subfamilia.TX_subfamilia_value, bh_subfamilia.TX_subfamilia_status, count(bh_producto.producto_AI_subfamilia_id) as conteo
			FROM ((bh_familia
			INNER JOIN bh_subfamilia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
			LEFT JOIN bh_producto ON bh_subfamilia.AI_subfamilia_id = bh_producto.producto_AI_subfamilia_id)
			WHERE bh_subfamilia.AI_subfamilia_id = '$value'
			GROUP BY producto_AI_subfamilia_id
			ORDER BY bh_subfamilia.TX_subfamilia_value")or die($link->error);
		$rs_subfamilia_occupied = $qry_subfamilia_occupied->fetch_array(MYSQLI_ASSOC);
		if ($rs_subfamilia_occupied['conteo'] < 1 ) {
			$link->query("DELETE FROM bh_subfamilia WHERE AI_subfamilia_id = '$value'")or die($link->error);
		}else{
			$link->query("UPDATE bh_subfamilia SET TX_subfamilia_status='0' WHERE AI_subfamilia_id = '$value'")or die($link->error);
		}
	break;


}

	// $raw_proveedor=['id' => $area_id,'nombre'=> $method];
 	// echo json_encode($raw_proveedor);

	// echo 'probando texto';

?>
