<?php
require '../../bh_conexion.php';
$link=conexion();

function read_nuevaventa_content(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function read_nuevaventa_rel(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 2")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_nuevaventa_content($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
}
function write_nuevaventa_rel($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 2")or die($link->error);
}
function plus_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$product_id=$_GET['a'];
	$precio=$_GET['b'];
	$descuento=$_GET['c'];
	$itbm=$_GET['d'];
	$activo=$_GET['e'];
	$cantidad=$_GET['f'];
	$medida=$_GET['g'];
	$promocion=$_GET['h'];

	$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
	$descripcion = $rs_product['TX_producto_value'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	if (!array_key_exists($activo, $raw_decode[$_COOKIE['coo_iuser']])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]= array();
	}
	// ###################### restrictor por repeticion
	$raw_nuevaventa = $raw_decode[$_COOKIE['coo_iuser']][$activo];
	$add = 1;
	foreach ($raw_nuevaventa as $key => $value) {
		if ($value['producto_id'] === $product_id && $value['medida'] === $medida && $value['promocion'] === $promocion) {
			$add = 0;
			break;
		}
	}
	// ########################## NEXT index
	if ($add === 1) {
		for($i=0;$i <= count($raw_nuevaventa);$i++) {
			if(!array_key_exists($i,$raw_nuevaventa)){
				$indice_vacio=$i;
			}
		}
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['producto_id'] = $product_id;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['cantidad'] = $cantidad;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['precio'] = $precio;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['impuesto'] = $itbm;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['descuento'] = $descuento;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['descripcion'] = $r_function->replace_regular_character($descripcion);
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['codigo'] = $rs_product['TX_producto_codigo'];
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['medida'] = $medida;
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['stock'] = $rs_product['TX_producto_cantidad'];
		// $raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['promocion'] = $promocion;
		$raw_newproduct = array();
		$raw_newproduct['producto_id'] = $product_id;
		$raw_newproduct['cantidad'] = $cantidad;
		$raw_newproduct['precio'] = $precio;
		$raw_newproduct['impuesto'] = $itbm;
		$raw_newproduct['descuento'] = $descuento;
		$raw_newproduct['descripcion'] = $r_function->replace_regular_character($descripcion);
		$raw_newproduct['codigo'] = $rs_product['TX_producto_codigo'];
		$raw_newproduct['medida'] = $medida;
		$raw_newproduct['stock'] = $rs_product['TX_producto_cantidad'];
		$raw_newproduct['promocion'] = $promocion;
		if(!is_array($raw_newproduct)){ 
			echo "failed";
			return false;
		}	
		$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]=$raw_newproduct;

	}else {
		echo "no panan <br />";
	}
	$contenido = json_encode($raw_decode);
	write_nuevaventa_content($contenido);
	echo $contenido;
}

function plus_multiple_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$raw_product_id=$_GET['a'];	$raw_precio=$_GET['b'];
	$raw_descuento=$_GET['c'];	$raw_impuesto=$_GET['d'];
	$activo=$_GET['e'];					$raw_cantidad=$_GET['f'];
	$raw_medida=$_GET['g'];			$promocion_tipo=$_GET['h'];
	$multiplo=$_GET['i'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	if (!array_key_exists($activo, $raw_decode[$_COOKIE['coo_iuser']])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]= array();
	}

// ################ INICIO DEL CICLO DE INSERCION
	$rel_nuevaventa='';
	foreach ($raw_product_id as $indice => $raw_product_id) {

		$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$raw_product_id'")or die($link->error);
		$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
		$descripcion = $rs_product['TX_producto_value'];
		//####################### RESTRICTOR por cantidad
		$adde = 1;
		if (round($raw_cantidad[$indice]*$multiplo,2) > $rs_product['TX_producto_cantidad']) {
			$adde = 0;
		}

		// ###################### RESTRICTOR por repeticion
		$raw_nuevaventa = $raw_decode[$_COOKIE['coo_iuser']][$activo];
		$add = 1;
		foreach ($raw_nuevaventa as $key => $value) {
			if ($value['producto_id'] === $raw_product_id && $value['promocion'] === $promocion_tipo) {
				$add = 0;
				break;
			}
		}
		// ########################## NEXT index
		for($i=0;$i <= count($raw_nuevaventa);$i++) {
			if(!array_key_exists($i,$raw_nuevaventa)){
				$indice_vacio=$i;
			}
		}
		if ($add === 1 && $adde === 1) {
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['producto_id'] = $raw_product_id;
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['cantidad'] = round($raw_cantidad[$indice]*$multiplo,2);
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['precio'] = $raw_precio[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['impuesto'] = $raw_impuesto[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['descuento'] = $raw_descuento[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['descripcion'] = $descripcion;
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['codigo'] = $rs_product['TX_producto_codigo'];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['medida'] = $raw_medida[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['stock'] = $rs_product['TX_producto_cantidad'];
			$raw_decode[$_COOKIE['coo_iuser']][$activo][$indice_vacio]['promocion'] = $promocion_tipo;

			$rel_nuevaventa .= ($rel_nuevaventa != '') ? ','.$indice_vacio : $indice_vacio;
		}
	}
	$contenido = json_encode($raw_decode);
	write_nuevaventa_content($contenido);
	echo $contenido;

	if (!empty($rel_nuevaventa)) {
		$contenido_nuevaventarel = read_nuevaventa_rel();
		$raw_contenido_nuevaventarel = json_decode($contenido_nuevaventarel, true);
		$raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo][]=$rel_nuevaventa;
		$contenido_nuevaventarel = json_encode($raw_contenido_nuevaventarel);
		write_nuevaventa_rel($contenido_nuevaventarel);
	}
}

function upd_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$key_nuevaventa=$_GET['a'];
	$value=$_GET['b'];
	$activo=$_GET['c'];
	$campo=$_GET['d'];
	$contenido=read_nuevaventa_content();
	$raw_nuevaventa=json_decode($contenido, true);

	if($campo === 'descripcion'){
		$value = $r_function->url_replace_special_character($value);
		$value = $r_function->replace_regular_character($value);
	}
	if ($raw_nuevaventa[$_COOKIE['coo_iuser']][$activo][$key_nuevaventa]['promocion'] < 1) {
		$raw_nuevaventa[$_COOKIE['coo_iuser']][$activo][$key_nuevaventa][$campo] = $value;
		$contenido = json_encode($raw_nuevaventa);
	}
	write_nuevaventa_content($contenido);
	echo $contenido;
}

function del_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$key_nuevaventa=$_GET['a'];
	$activo=$_GET['b'];

	$contenido_nuevaventarel=read_nuevaventa_rel();
	$raw_contenido_nuevaventarel=json_decode($contenido_nuevaventarel, true);
	$raw_2delete = array();	$rel_2delete = '';
	if (!empty($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo])) {
		foreach ($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo] as $key => $value) {
			$raw_value = explode(",",$value);
			if (in_array($key_nuevaventa,$raw_value)) {
				$raw_2delete=$raw_value;
				$rel_2delete=$key;
				break;
			}
		}
	}
	if (count($raw_2delete) === 0) {
		$raw_2delete[] = $key_nuevaventa;
	}
	$contenido=read_nuevaventa_content();
	$raw_nuevaventa=json_decode($contenido, true);
	foreach ($raw_2delete as $key => $key_nuevaventa) {
		unset($raw_nuevaventa[$_COOKIE['coo_iuser']][$activo][$key_nuevaventa]);
	}

	$contenido = json_encode($raw_nuevaventa);

	write_nuevaventa_content($contenido);
	echo $contenido;

	if ($rel_2delete >= 0) {
		$contenido_nuevaventarel = read_nuevaventa_rel();
		$raw_contenido_nuevaventarel = json_decode($contenido_nuevaventarel, true);
		unset($raw_contenido_nuevaventarel[$_COOKIE['coo_iuser']][$activo][$rel_2delete]);
		$contenido_nuevaventarel = json_encode($raw_contenido_nuevaventarel);
		write_nuevaventa_rel($contenido_nuevaventarel);
	}
}
function reload_nuevaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$activo=$_GET['a'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);
	if(!is_array($raw_decode)){ 
		echo "failed";
		return false;
	}	
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	if (!array_key_exists($activo, $raw_decode[$_COOKIE['coo_iuser']])) {
		$raw_decode[$_COOKIE['coo_iuser']][$activo]= array();
	}
	$contenido=json_encode($raw_decode);
	echo $contenido;
}

function duplicate_datoventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$facturaventa_id=$_GET['a'];

	$contenido=read_nuevaventa_content();
	$raw_decode=json_decode($contenido, true);

	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {	$raw_decode[$_COOKIE['coo_iuser']]= array();	}
	if (!array_key_exists('first_sale', $raw_decode[$_COOKIE['coo_iuser']])) {	$raw_decode[$_COOKIE['coo_iuser']]['first_sale']= array();	}
	unset($raw_decode[$_COOKIE['coo_iuser']]['first_sale']);

	$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_medida, TX_datoventa_promocion FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$facturaventa_id'")or die($link->error);
	$i=0;
	while ($rs_datoventa=$qry_datoventa->fetch_array(MYSQLI_ASSOC)) {
		$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '{$rs_datoventa['datoventa_AI_producto_id']}'")or die($link->error);
		$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
		if ($rs_datoventa['TX_datoventa_promocion'] < 1) {
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['producto_id'] = $rs_datoventa['TX_datoventa_cantidad'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['cantidad'] = $rs_datoventa['TX_datoventa_cantidad'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['precio'] = $rs_datoventa['TX_datoventa_precio'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['impuesto'] = $rs_datoventa['TX_datoventa_impuesto'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['descuento'] = $rs_datoventa['TX_datoventa_descuento'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['descripcion'] = $rs_datoventa['TX_datoventa_descripcion'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['codigo'] = $rs_product['TX_producto_codigo'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['medida'] = $rs_datoventa['TX_datoventa_medida'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['stock'] = $rs_product['TX_producto_cantidad'];
			$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['promocion'] = $rs_datoventa['TX_datoventa_promocion'];
		}
		$i++;
	}

	// $raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$i]['descripcion'] = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);

	$contenido=json_encode($raw_decode);
	write_nuevaventa_content($contenido);
	foreach ($raw_decode[$_COOKIE['coo_iuser']]['first_sale'] as $key => $value) {
		$raw_decode[$_COOKIE['coo_iuser']]['first_sale'][$key]['descripcion'] = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
	}
	$contenido=json_encode($raw_decode);
	echo $contenido;
}

$funct=$_GET['z'];
switch ($funct) {
	case 'plus':
		plus_nuevaventa();
		break;
	case 'plus_multiple' :
		plus_multiple_nuevaventa();
		break;
	case 'upd':
		upd_nuevaventa();
		break;
	case 'del':
		del_nuevaventa();
		break;
	case 'reload':
		reload_nuevaventa();
		break;
	case 'duplicate':
		duplicate_datoventa();
		break;
}

?>
