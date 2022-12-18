<?php
require '../../bh_conexion.php';
$link = conexion();

$printer_id=$_GET['a'];
$method=$_GET['b'];


class class_printer {
	function get_printer($printer_id) {
		$link = conexion();
		$qry_printer = $link->query("SELECT * FROM bh_impresora WHERE AI_impresora_id = '$printer_id'")or die($link->error);
		$rs_printer=$qry_printer->fetch_array(MYSQLI_ASSOC);
		$raw_printer = ['serial'=>$rs_printer['TX_impresora_serial'],
		'client'=>$rs_printer['TX_impresora_cliente'],
		'seudonim'=>$rs_printer['TX_impresora_seudonimo'],
		'recipient'=>$rs_printer['TX_impresora_recipiente'],
		'return'=>$rs_printer['TX_impresora_retorno'],
		'till'=>$rs_printer['TX_impresora_cajaregistradora']
	];
		return json_encode($raw_printer);
	}
	function save_printer ($printer_id) {
		$link = conexion();
		if (!empty($printer_id)) {		
			$link->query("UPDATE bh_impresora SET TX_impresora_recipiente = '{$_GET['recipient']}', TX_impresora_retorno = '{$_GET['return_folder']}', TX_impresora_cliente = '{$_GET['client']}', TX_impresora_serial = '{$_GET['serial']}', TX_impresora_seudonimo = '{$_GET['seudonim']}', TX_impresora_cajaregistradora = '{$_GET['till']}' WHERE AI_impresora_id = '$printer_id'")or die($link->error);
			$message = 'Actualizado correctamente';
		}else{
			$link->query("INSERT INTO bh_impresora (TX_impresora_recipiente,TX_impresora_retorno,TX_impresora_cliente,TX_impresora_serial,TX_impresora_seudonimo,TX_impresora_cajaregistradora) VALUES ('{$_GET['recipient']}','{$_GET['return_folder']}','{$_GET['client']}','{$_GET['serial']}','{$_GET['seudonim']}','{$_GET['till']}')")or die($link->error);
			$message = 'Registro Almacenado';			
		}
		$raw_answer = ['message'=>$message];
		return json_encode($raw_answer);
	}
	function del_printer($printer_id) {
		$link = conexion();
		$qry_printer = $link->query("DELETE FROM bh_impresora WHERE AI_impresora_id = '$printer_id'")or die($link->error);
		$raw_answer = ['message'=>'Elemento Borrado Correctamente'];
		return json_encode($raw_answer);
	}


	function get_name() {
		return $this->name;
	}
}

$cls_printer = new class_printer;
$raw_printer = $cls_printer->$method($printer_id);
echo $raw_printer;

// switch ($method) {
// 	// case 'activate':
// 	// 	$link->query("UPDATE bh_area SET TX_area_status='1' WHERE AI_area_id = '$area_id'")or die($link->error);
// 	// 	break;
// case 'desactivate':
// 	$qry_occupied = $link->query("SELECT AI_medida_id FROM (bh_medida INNER JOIN bh_producto ON bh_medida.AI_medida_id = bh_producto.TX_producto_medida) WHERE bh_medida.AI_medida_id = '$medida_value'")or die($link->error);
// 	if ($qry_occupied->num_rows < 1 ) {
// 		$link->query("DELETE FROM bh_medida WHERE AI_medida_id = '$medida_value'")or die($link->error);
// 	}
// 	echo "Eliminado!";
// 	break;
// 	case 'add':
// 		$link->query("INSERT INTO bh_medida (TX_medida_value, medida_AI_user_id) VALUES ('{$_GET['a']}','{$_COOKIE['coo_iuser']}')")or die($link->error);
// 		echo "Agregado Correctamente!";
// 	break;
// }

	// $raw_proveedor=['id' => $area_id,'nombre'=> $method];
 	// echo json_encode($raw_proveedor);
  //

?>
