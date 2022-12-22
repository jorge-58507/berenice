<?php
require '../../bh_conexion.php';
$link = conexion();
$value=$r_function->url_replace_special_character($_GET['a']);
$value=$r_function->replace_regular_character($value);

$arr_value = (explode(' ',$value));
$arr_value = array_values(array_unique($arr_value));

$size_value=sizeof($arr_value);
$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value FROM bh_producto WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND TX_producto_activo = '0'";
	}else{
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND TX_producto_activo = '0'";
	}else{
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 30")or die($link->error);
$raw_producto=array(); $i=0;
while ($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto[]=$rs_product;
};

			$res_cantidad=($qry_product->num_rows > 200)? '+200' : $qry_product->num_rows;
			$caption = 'Se Encontr&oacute;: '.$res_cantidad.' Resultado(s) para "'.$r_function->url_replace_special_character($_GET['a']).'".';
			$raw_result = array($caption,$raw_producto);
			echo json_encode($raw_result);
