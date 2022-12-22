<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_stock.php';

$json_request = trim(file_get_contents("php://input"));
$request = json_decode($json_request, true);

$cls = $_GET['cls'];
$method = $_GET['mtd'];


class class_product {
	public function get_grouplist () {
		$link = conexion();
		$qry_productogrupo = $link->query("SELECT AI_productogrupo_id, TX_productogrupo_titulo, TX_productogrupo_json FROM bh_productogrupo ORDER BY TX_productogrupo_titulo ASC")or die($link->error);
		$array_grupo = [];
		while ($rs_grupo = $qry_productogrupo->fetch_array(MYSQLI_ASSOC)) {
			$array_grupo[] = $rs_grupo;
		}
		return json_encode($array_grupo);
	}
	public function filter_product () {
		$value = $_GET['a'];
		$limit = $_GET['b'];
		$link = conexion();
		$arr_value = (explode(' ',$value));
		$arr_value = array_values(array_unique($arr_value));

		$txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_codigo, TX_producto_referencia, TX_producto_activo, TX_producto_minimo, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_medida, TX_producto_inventariado FROM bh_producto WHERE ";
		foreach ($arr_value as $key => $value) {
			$txt_product .= ($value === end($arr_value)) ? "TX_producto_value LIKE '%{$value}%' OR " : "TX_producto_value LIKE '%{$value}%' AND ";
		}
		foreach ($arr_value as $key => $value) {
			$txt_product .= ($value === end($arr_value)) ? "TX_producto_codigo LIKE '%{$value}%' OR " : "TX_producto_codigo LIKE '%{$value}%' AND ";
		}
		foreach ($arr_value as $key => $value) {
			$txt_product .= ($value === end($arr_value)) ? "TX_producto_referencia LIKE '%{$value}%'" : "TX_producto_referencia LIKE '%{$value}%' AND ";
		}
		$line_limit = ($limit != '') ? ' ORDER BY TX_producto_value ASC LIMIT '.$limit : '';
		$qry_product=$link->query($txt_product.$line_limit);
		$array_product = [];
		while ($rs_product = $qry_product->fetch_array(MYSQLI_ASSOC)) {
			$array_product[] = $rs_product;
		}
		echo json_encode($array_product);
	}
	
	public function count_group () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected['selected_id'];
			$link->query("UPDATE bh_producto SET TX_producto_inventariado = '1' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Se marcaron como contadas el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Inventariado"]);
	}
	public function uncount_group () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected['selected_id'];
			$link->query("UPDATE bh_producto SET TX_producto_inventariado = '0' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Se les quitó el conteo el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Sin Inventariar"]);
	}

	public function enable_group () {
		$link = conexion();
		$request = $GLOBALS['request'];
		$r_function = new recurrent_function();

		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected["selected_id"];
			$link->query("UPDATE bh_producto SET TX_producto_activo = '0' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Fueron Activados el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Habilitado Exitosamente"]);
	}
	public function disable_group () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected["selected_id"];
			$link->query("UPDATE bh_producto SET TX_producto_activo = '1' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Fueron Desactivados el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Deshabilitado Exitosamente"]);
	}

	public function set_to_cero () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected["selected_id"];
			$link->query("UPDATE bh_producto SET TX_producto_cantidad = '0' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Fueron Pasados a Cero el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Contados a Cero"]);
	}


	public function save_group () {
		$link = conexion();
		$request = $GLOBALS['request'];
		$group_title = $request['a'];
		$array_group = [];
		foreach ($request['b'] as $key => $selected) {
			$array_group[$key]['id'] = $selected['selected_id'];
			$array_group[$key]['code'] = $selected['selected_code'];
			$array_group[$key]['description'] = $selected['selected_description'];
		}
		$group_json = json_encode($request['b']);
		$group_created = (date("YmdGis",time())); 

		$link->query("INSERT INTO bh_productogrupo (TX_productogrupo_titulo, TX_productogrupo_json, TX_productogrupo_creado) VALUES ('$group_title', '$group_json', '$group_created')")or die($link->error);

		echo json_encode(["message" => "Agregado Correctamente"]);
	}
	public function validate_group () {
		$link = conexion();
		$request = $GLOBALS['request'];
		$group_json = json_encode($request['b']);

		$qry_count = $link->query("SELECT AI_productogrupo_id FROM bh_productogrupo WHERE TX_productogrupo_titulo = '{$request['a']}'")or die($link->error);
		if ($qry_count->num_rows > 0) {	echo json_encode(["message" => "Este Titulo ya Existe", "success" => 0]);	return false;	}
		$qry_count_json = $link->query("SELECT AI_productogrupo_id FROM bh_productogrupo WHERE TX_productogrupo_json = '$group_json'")or die($link->error);
		if ($qry_count_json->num_rows > 0) {	
			echo json_encode(["message" => "Este Grupo Ya Existe", "success" => 0]);
		}else{
			echo json_encode(["message" => "Perfecto!", "success" => 1]);			
		}
	}
	public function pick_group () {
		$link = conexion();
		$request = $GLOBALS['request'];
		$group_id = $request['a'];

		$qry_productgroup = $link->query("SELECT AI_productogrupo_id, TX_productogrupo_titulo, TX_productogrupo_json FROM bh_productogrupo WHERE AI_productogrupo_id = '$group_id'")or die($link->error);
		$array_group = [];
		while ($rs_productgroup = $qry_productgroup->fetch_array(MYSQLI_ASSOC)) {
			$group_json = json_decode($rs_productgroup['TX_productogrupo_json'], true);
			foreach ($group_json as $key => $line) {
				$id = $line['selected_id'];
				$qry_quantity = $link->query("SELECT TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$id'")or die($link->error);
				$rs_quantity = $qry_quantity->fetch_array();
				$group_json[$key]['selected_quantity'] = $rs_quantity['TX_producto_cantidad'];

				
			}
			// $rs_quantity = $qry_quantity->fetch_array();
			// $rs_productgroup['selected_quantity'] = $rs_quantity['TX_producto_cantidad'];
			$array_group[0]['TX_productogrupo_json'] = json_encode($group_json);
		}

		// while ($rs_productgroup = $qry_productgroup->fetch_array(MYSQLI_ASSOC)) {
		// 	$array_group[] = $rs_productgroup;
		// }
		echo json_encode(["message" => "Ready", "array_group" => $array_group]);
	}

	public function delete_group ()	{
		$link = conexion();
		$request = $GLOBALS['request'];
		$group_id = $request['a'];

		$link->query("DELETE FROM bh_productogrupo WHERE AI_productogrupo_id = '$group_id'")or die($link->error);
		echo json_encode(["message" => "Eliminado Correctamente", "array_group" => $this->get_grouplist()]);
	}



	public function discount_group () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected['selected_id'];
			$link->query("UPDATE bh_producto SET TX_producto_descontable = '1' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Se marcaron como descontable el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "Descontable"]);
	}
	public function undiscount_group () {
		$link = conexion();
		$r_function = new recurrent_function();

		$request = $GLOBALS['request'];
		$txt_content = '';
		foreach ($request['a'] as $key => $selected) {
			$selected_id = $selected['selected_id'];
			$link->query("UPDATE bh_producto SET TX_producto_descontable = '0' WHERE AI_producto_id = '$selected_id'")or die($link->error);
			$txt_content .= "(".$selected['selected_id'].") ".$selected['selected_code']." ".$r_function->replace_special_character_no_html($selected['selected_description']).PHP_EOL;
		}
		$file = fopen("../../inventario_log.txt", "a");
		fwrite($file, "{{".PHP_EOL.$txt_content."}} ".PHP_EOL."         Se les quitó el descontable el ".date('d-m-Y G:i:s').PHP_EOL );
		fclose($file);

		echo json_encode(["message" => "No Descontable"]);
	}

}
$class_selected = new $cls;
$class_selected->$method();

?>
