<?php
require '../../bh_conexion.php';
$link=conexion();

function read_viejaventa_content(){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function read_viejaventa_rel(){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_viejaventa_content($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
}
function write_viejaventa_rel($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
}

function plus_viejaventa($product_id,$precio,$descuento,$itbm,$cantidad,$medida,$promocion){
	$link=conexion();	$r_function = new recurrent_function();

	$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
	$descripcion = $rs_product['TX_producto_value'];

	$contenido=read_viejaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	// ###################### restrictor por repeticion
	$raw_viejaventa = $raw_decode[$_COOKIE['coo_iuser']];		$add = 1;
	foreach ($raw_viejaventa as $key => $value) {
		if ($value['producto_id'] === $product_id && $value['promocion'] === $promocion) {
			$add = 0;	break;
		}
	}
	// ########################## NEXT index
	if ($add === 1) {
		for($i=0;$i <= count($raw_viejaventa);$i++) {
			if(!array_key_exists($i,$raw_viejaventa)){
				$indice_vacio=$i;
			}
		}
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['producto_id'] = $product_id;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['cantidad'] = $cantidad;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['precio'] = $precio;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['impuesto'] = $itbm;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['descuento'] = $descuento;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['descripcion'] = $r_function->replace_regular_character($descripcion);
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['codigo'] = $rs_product['TX_producto_codigo'];
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['medida'] = $medida;
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['stock'] = $rs_product['TX_producto_cantidad'];
		$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['promocion'] = $promocion;
	}
	$contenido = json_encode($raw_decode);
	write_viejaventa_content($contenido);
	echo $contenido;
}

function plus_multiple_viejaventa($raw_product_id,$raw_precio,$raw_descuento,$raw_impuesto,$raw_cantidad,$raw_medida,$promocion_tipo,$multiplo){
	$link=conexion();	$r_function = new recurrent_function();
	$contenido=read_viejaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
// ################ INICIO DEL CICLO DE INSERCION
	$rel_viejaventa='';
	foreach ($raw_product_id as $indice => $raw_product_id) {
		$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$raw_product_id'")or die($link->error);
		$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
		$descripcion = $rs_product['TX_producto_value'];
		// ###################### RESTRICTOR por repeticion
		$raw_viejaventa = $raw_decode[$_COOKIE['coo_iuser']];
		$add = 1;
		foreach ($raw_viejaventa as $key => $value) {
			if ($value['producto_id'] === $raw_product_id && $value['promocion'] === $promocion_tipo) {
				$add = 0;
				break;
			}
		}
		// ########################## NEXT index
		for($i=0;$i <= count($raw_viejaventa);$i++) {
			if(!array_key_exists($i,$raw_viejaventa)){
				$indice_vacio=$i;
			}
		}
		if ($add === 1) {
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['producto_id'] = $raw_product_id;
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['cantidad'] = round($raw_cantidad[$indice]*$multiplo,2);
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['precio'] = $raw_precio[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['impuesto'] = $raw_impuesto[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['descuento'] = $raw_descuento[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['descripcion'] = $descripcion;
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['codigo'] = $rs_product['TX_producto_codigo'];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['medida'] = $raw_medida[$indice];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['stock'] = $rs_product['TX_producto_cantidad'];
			$raw_decode[$_COOKIE['coo_iuser']][$indice_vacio]['promocion'] = $promocion_tipo;

			$rel_viejaventa .= ($rel_viejaventa != '') ? ','.$indice_vacio : $indice_vacio;
		}
	}
	$contenido = json_encode($raw_decode);
	write_viejaventa_content($contenido);
	echo $contenido;

	if (!empty($rel_viejaventa)) {
		$contenido_viejaventarel = read_viejaventa_rel();
		$raw_contenido_viejaventarel = json_decode($contenido_viejaventarel, true);
		$raw_contenido_viejaventarel[$_COOKIE['coo_iuser']][]=$rel_viejaventa;
		$contenido_viejaventarel = json_encode($raw_contenido_viejaventarel);
		write_viejaventa_rel($contenido_viejaventarel);
	}
}

function upd_viejaventa($key_viejaventa,$value,$campo){
	$link=conexion();	$r_function = new recurrent_function();
	$contenido=read_viejaventa_content();
	$raw_viejaventa=json_decode($contenido, true);
	if ($raw_viejaventa[$_COOKIE['coo_iuser']][$key_viejaventa]['promocion'] < 1 || strlen($value) > 10) {
		$value=$r_function->url_replace_special_character($value);
		$raw_viejaventa[$_COOKIE['coo_iuser']][$key_viejaventa][$campo] = $r_function->replace_regular_character($value);
		$contenido = json_encode($raw_viejaventa);
	}
	write_viejaventa_content($contenido);
	echo $contenido;
}

function del_viejaventa($key_viejaventa){
	$link=conexion();	$r_function = new recurrent_function();
	$contenido_viejaventarel=read_viejaventa_rel();
	$raw_contenido_viejaventarel=json_decode($contenido_viejaventarel, true);
	$raw_2delete = array();	$rel_2delete = '';
	$raw_relminus=array();
if (!empty($raw_contenido_viejaventarel[$_COOKIE['coo_iuser']])) {
		foreach ($raw_contenido_viejaventarel[$_COOKIE['coo_iuser']] as $key => $value) {
			$raw_value = explode(",",$value);
			if (in_array($key_viejaventa,$raw_value)) {
				$raw_2delete=$raw_value;
				$rel_2delete=$key;
				break;
			}
		}
		foreach ($raw_contenido_viejaventarel[$_COOKIE['coo_iuser']] as $key => $value) {
			$raw_value = explode(",",$value);
			if ($key_viejaventa < $raw_value[0]) {
				$str_aux='';
				foreach ($raw_value as $Kkey => $value_tominus) {
					$str_aux .= ($value_tominus === end($raw_value)) ? $value_tominus-1 : ($value_tominus-1).',';
				}
				$raw_relminus[$key]=$str_aux;
			}
		}
	}
	if (count($raw_2delete) === 0) {
		$raw_2delete[] = $key_viejaventa;
	}
	$contenido=read_viejaventa_content();
	$raw_viejaventa=json_decode($contenido, true);
	foreach ($raw_2delete as $key => $key_viejaventa) {
		unset($raw_viejaventa[$_COOKIE['coo_iuser']][$key_viejaventa]);
	}
	$raw_viejaventa[$_COOKIE['coo_iuser']]=array_values($raw_viejaventa[$_COOKIE['coo_iuser']]);
	$contenido = json_encode($raw_viejaventa);
	write_viejaventa_content($contenido);
	echo $contenido;
	if ($rel_2delete >= 0) {
		$contenido_viejaventarel = read_viejaventa_rel();
		$raw_contenido_viejaventarel = json_decode($contenido_viejaventarel, true);
		unset($raw_contenido_viejaventarel[$_COOKIE['coo_iuser']][$rel_2delete]);
	}
	if (count($raw_relminus) > 0) {
		foreach ($raw_relminus as $key => $value) {
			$raw_contenido_viejaventarel[$_COOKIE['coo_iuser']][$key]=$value;
		}
	}
	$contenido_viejaventarel = json_encode($raw_contenido_viejaventarel);
	write_viejaventa_rel($contenido_viejaventarel);
}

function reload_viejaventa(){
	$link=conexion();	$r_function = new recurrent_function();
	$contenido=read_viejaventa_content();
	$raw_decode=json_decode($contenido, true);
	if (!array_key_exists($_COOKIE['coo_iuser'], $raw_decode)) {
		$raw_decode[$_COOKIE['coo_iuser']]= array();
	}
	$contenido=json_encode($raw_decode);
	echo $contenido;
}

function reordenar_viejaventa($posicion,$n_posicion){
	$contenido_viejaventa=read_viejaventa_content();
	$raw_viejaventa=json_decode($contenido_viejaventa, true);
	if ($raw_viejaventa[$_COOKIE['coo_iuser']][$posicion]['promocion'] > 0 || $raw_viejaventa[$_COOKIE['coo_iuser']][$n_posicion-1]['promocion'] > 0 || $n_posicion > count($raw_viejaventa[$_COOKIE['coo_iuser']]) || $n_posicion < 1) {
		$contenido_viejaventa = json_encode($raw_viejaventa, true);
		echo $contenido_viejaventa;
		return false;
	}
	$n_posicion=$n_posicion-1;
	$n_array = array();
	if ($posicion > $n_posicion) {
		$menor=$n_posicion;	$mayor=$posicion;
		for($i=$menor;$i<$mayor;$i++){
			$n_array[$i+1] = $raw_viejaventa[$_COOKIE['coo_iuser']][$i];
		}
		$n_array[$menor] = $raw_viejaventa[$_COOKIE['coo_iuser']][$posicion];
		foreach ($n_array as $key => $value) {
			$raw_viejaventa[$_COOKIE['coo_iuser']][$key] = $value;
		}
		$rel_viejaventa = read_viejaventa_rel();
		$raw_rel = json_decode($rel_viejaventa, true);
		$rel_tomodified = array();
		foreach ($n_array as $key => $array) {
			foreach ($raw_rel[$_COOKIE['coo_iuser']] as $rel_key => $raw_value) {
				if (in_array($key, explode(",",$raw_value))) {
					if (!in_array($rel_key, $rel_tomodified)) {
						$rel_tomodified[]=$rel_key;
					}
				}
			}
		}
		foreach ($rel_tomodified as $key => $index_value) {
			$array_tomodified = explode(",",$raw_rel[$_COOKIE['coo_iuser']][$index_value]);
			foreach ($array_tomodified as $key => $value) {
				$array_tomodified[$key] = $value+1;
			}
			$str_modified = implode(",",$array_tomodified);
			$raw_rel[$_COOKIE['coo_iuser']][$index_value]=$str_modified;
		}
		$rel_viejaventa = json_encode($raw_rel);
		write_viejaventa_rel($rel_viejaventa);
	}else{
		$menor=$posicion; $mayor=$n_posicion;
		for($i=$menor;$i<$mayor;$i++){
			$n_array[$i] = $raw_viejaventa[$_COOKIE['coo_iuser']][$i+1];
		}
		$n_array[$mayor] = $raw_viejaventa[$_COOKIE['coo_iuser']][$posicion];
		foreach ($n_array as $key => $value) {
			$raw_viejaventa[$_COOKIE['coo_iuser']][$key] = $value;
		}

		$rel_viejaventa = read_viejaventa_rel();
		$raw_rel = json_decode($rel_viejaventa, true);
		$rel_tomodified = array();
		foreach ($n_array as $key => $array) {
			foreach ($raw_rel[$_COOKIE['coo_iuser']] as $rel_key => $raw_value) {
				if (in_array($key, explode(",",$raw_value))) {
					if (!in_array($rel_key, $rel_tomodified)) {
						$rel_tomodified[]=$rel_key;
					}
				}
			}
		}
		foreach ($rel_tomodified as $key => $index_value) {
			$array_tomodified = explode(",",$raw_rel[$_COOKIE['coo_iuser']][$index_value]);
			foreach ($array_tomodified as $key => $value) {
				$array_tomodified[$key] = $value-1;
			}
			$str_modified = implode(",",$array_tomodified);
			$raw_rel[$_COOKIE['coo_iuser']][$index_value]=$str_modified;
		}
		$rel_viejaventa = json_encode($raw_rel);
		write_viejaventa_rel($rel_viejaventa);


	}
	$contenido = json_encode($raw_viejaventa, true);
	write_viejaventa_content($contenido);
	echo $contenido;
}

$funct=$_GET['z'];
switch ($funct) {
	case 'plus':
		plus_viejaventa($_GET['a'],$_GET['b'],$_GET['c'],$_GET['d'],$_GET['e'],$_GET['f'],$_GET['g']);
		break;
	case 'plus_multiple' :
		plus_multiple_viejaventa($_GET['a'],$_GET['b'],$_GET['c'],$_GET['d'],$_GET['e'],$_GET['f'],$_GET['g'],$_GET['h']);
		break;
	case 'upd':
	 	upd_viejaventa($_GET['a'],$_GET['b'],$_GET['c']);
		break;
	case 'del':
		del_viejaventa($_GET['a']);
		break;
	case 'reload':
		reload_viejaventa();
		break;
	case 'reordenar':
		reordenar_viejaventa($_GET['a'],$_GET['b']);
		break;
}

?>
