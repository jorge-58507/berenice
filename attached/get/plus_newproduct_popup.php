<?php
require '../../bh_conexion.php';
$link = conexion();
function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "&deg;", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$codigo=$_GET['a'];
$referencia=$_GET['b'];
$letra=$_GET['i'];
$value=edit_quote($_GET['c']);
$medida=$_GET['d'];
$cantidad=$_GET['e'];
$minimo=$_GET['g'];
$maximo=$_GET['f'];
$exento=$_GET['h'];

$p_5=$_GET['n'];
$p_4=$_GET['m'];
$p_3=$_GET['l'];
$p_2=$_GET['k'];
$p_1=$_GET['j'];


$fecha_actual=date('Y-m-d');

	$qry_checkproduct=$link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_codigo = '$codigo'")or die($link->error);
	$nr_checkproduct=$qry_checkproduct->num_rows;
	if($nr_checkproduct < 1){

		$bh_insert="INSERT INTO bh_producto
					 (TX_producto_codigo, TX_producto_value, TX_producto_medida, TX_producto_cantidad, TX_producto_minimo, TX_producto_maximo, TX_producto_exento, TX_producto_referencia, producto_AI_letra_id)
		VALUES ('$codigo','$value','$medida','$cantidad','$minimo','$maximo','$exento','$referencia','$letra')";
		$link->query($bh_insert) or die($link->error);

		$qry_lastid=$link->query("SELECT LAST_INSERT_ID();")or die($link->error);
		$rs_lastid = $qry_lastid->fetch_array();
		$lastid = $rs_lastid[0];

		$bh_insprecio="INSERT INTO bh_precio (precio_AI_producto_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha) VALUES ('$lastid','$p_1','$p_2','$p_3','$p_4','$p_5','$fecha_actual')";
		$link->query($bh_insprecio) or die($link->error);

		echo $lastid;
	}else{
		$rs_checkproduct = $qry_checkproduct->fetch_array();
		echo $rs_checkproduct['AI_producto_id'];
	}

?>
