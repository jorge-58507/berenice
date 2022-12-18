<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$r_function->url_replace_special_character($_GET['a']);
$value=$r_function->replace_regular_character($value);
$value = strtoupper($value);

$line_limit = "";
if(!empty($_GET['b']) && $_GET['b'] != 'undefined'){
	$limite = $_GET['b'];
}

$arr_value = (explode(' ',$value));
$arr_value = array_values(array_unique($arr_value));

$omitir = array(); $buscar = array();
foreach ($arr_value as $key => $value) {
 	$pos = strpos($value, 'NO:');
	if ($pos === false ) {
		$buscar[]=$value;
	}else{
		$omitir[]=substr($value,3);
	}
}

$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.producto_AI_letra_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_cantidad, bh_producto.TX_producto_inventariado FROM bh_producto WHERE ";
// FILTROS
foreach ($buscar as $key => $value) {
		$txt_product=$txt_product."TX_producto_value LIKE '%$value%' AND ";
}
// OMISIONES
if (count($omitir) > 0) {
	foreach ($omitir as $key => $value) {
		$txt_product .= ($value === end($omitir)) ? "TX_producto_value NOT LIKE '%$value%' AND  TX_producto_activo = '0' " : "TX_producto_value NOT LIKE '%$value%' AND ";
	}
}else{
	$txt_product .= " TX_producto_activo = '0'";
}
$txt_product=$txt_product." OR ";
// FILTROS
foreach ($buscar as $key => $value) {
		$txt_product=$txt_product." TX_producto_codigo LIKE '%$value%' AND ";
}
// OMISIONES
if (count($omitir) > 0) {
	foreach ($omitir as $key => $value) {
		$txt_product .= ($value === end($omitir)) ? "TX_producto_codigo NOT LIKE '%$value%' AND  TX_producto_activo = '0' " : "TX_producto_value NOT LIKE '%$value%' AND ";
	}
}else{
	$txt_product .= " TX_producto_activo = '0'";
}
$qry_precio = $link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0'")or die($link->error);
$prep_inventoried = $link->prepare("SELECT TX_inventario_json FROM bh_inventario WHERE inventario_AI_producto_id = ?")or die($link->error);

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC")or die($link->error);
$raw_producto=array(); $i=0;
while ($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)) {
	if ($i < $limite) {
		$qry_precio->bind_param("i", $rs_product['AI_producto_id']); $qry_precio->execute(); $result = $qry_precio->get_result();
		$rs_precio=$result->fetch_array(MYSQLI_ASSOC);
		$prep_inventoried->bind_param("i", $rs_product['AI_producto_id']); $prep_inventoried->execute(); $qry_inventoried = $prep_inventoried->get_result();
		$rs_inventoried=$qry_inventoried->fetch_array(MYSQLI_ASSOC);
		if ($qry_inventoried->num_rows > 0) {
			$raw_inventoried = json_decode($rs_inventoried['TX_inventario_json'], true);
			$last_inventoried = end($raw_inventoried);
			$last_date_inventoried = key($last_inventoried);
		}else{
			$last_date_inventoried = '';
		}	
		$raw_producto[$i]=$rs_product;
		$raw_producto[$i]['precio']=$rs_precio['TX_precio_cuatro'];
		$raw_producto[$i]['inventoried']=date('d-m-Y', strtotime($last_date_inventoried));
	}else{
		break;
	}
	$i++;
};

			$res_cantidad=($qry_product->num_rows > 200)? '+200' : $qry_product->num_rows;
			$caption = 'Se Encontr&oacute;: '.$res_cantidad.' Resultado(s) para "'.$r_function->url_replace_special_character($_GET['a']).'".';
			$raw_result = array($caption,$raw_producto);
			echo json_encode($raw_result);
