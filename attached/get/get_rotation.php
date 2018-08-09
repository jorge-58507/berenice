<?php
require '../../bh_conexion.php';
date_default_timezone_set('America/Panama');
$link=conexion();
$date_i = date('Y-m',strtotime($_GET['a']));
$date_i = date('Y-m-d',strtotime($date_i));
$year_i = date('Y',strtotime($_GET['a']));

$date_f = date('Y-m',strtotime($_GET['b']));
$date_f = date('Y-m-d',strtotime($date_f));
$year_f = date('Y',strtotime($_GET['b']));

$product_id = $_GET['c'];

$datetime1=new DateTime($date_i);
$datetime2=new DateTime($date_f);
$interval=$datetime2->diff($datetime1);
$intervalMeses=$interval->format("%m");
$intervalAnos = $interval->format("%y")*12;
$meses = $intervalMeses+$intervalAnos;
$raw_fecha=array();
$fecha_sumada=$date_i;
	for($i=0;$i<=$meses;$i++){
		$raw_fecha[$i]=$fecha_sumada;
		$fecha_sumada = date('Y-m-d',strtotime('+1 month',strtotime($fecha_sumada)));
	}
	$qry_json=$link->query("SELECT TX_rotacion_json FROM bh_rotacion WHERE rotacion_AI_producto_id = '$product_id' ")or die($link->error);
	$array_merged=array();
	while($rs_json=$qry_json->fetch_array()){
		$decoded=json_decode($rs_json['TX_rotacion_json'],true);
		$year=date('Y',strtotime($rs_json['TX_rotacion_json']));
		$array_merged=array_merge($array_merged,json_decode($rs_json['TX_rotacion_json'],true));
	}
$stock=0;
$counter=0;
$raw_date_finded=array();
foreach($raw_fecha as $fecha){
	for($it=0;$it<count($array_merged);$it++){
		$merged_keys = array_keys($array_merged[$it]);
		foreach ($merged_keys as $key => $fecha_entera) {
			if (date('Y-m',strtotime($fecha_entera)) === date('Y-m',strtotime($fecha))) {
				$stock+=$array_merged[$it][$fecha_entera];
				$raw_date_finded[$counter]=$fecha_entera;
				$counter++;
			}
		}
	}
}
$content = file_get_contents("../tool/reduce_recompose/reduce_recompose.json");
// echo $content;
$raw_contenido = json_decode($content, true);
$reducido=0; $sumado=0;
foreach ($raw_contenido['saved'] as $index => $saved) {
	foreach ($saved['minus'] as $key => $minus) {
		if ($minus['producto_id'] === $product_id) {
			$reducido += $minus['cantidad'];
		}
	}
	foreach ($saved['plus'] as $key => $plus) {
		if ($plus['producto_id'] === $product_id) {
			$sumado += $plus['cantidad'];
		}
	}
}

$txt_purchase="SELECT TX_datocompra_cantidad FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_facturacompra.TX_facturacompra_fecha >= '$date_i' AND  bh_facturacompra.TX_facturacompra_fecha <= '$date_f' AND bh_datocompra.datocompra_AI_producto_id = '$product_id'";
$qry_purchase=$link->query($txt_purchase);
$cantidad_comprada=$sumado;
while($rs_purchase=$qry_purchase->fetch_array()){
	$cantidad_comprada+=$rs_purchase[0];
}
if($counter === 0){
	$promedio = 0;
	$rotacion = 0;
	$raw_rotation=array("1"=>date('d-m-Y',strtotime($date_i)),"2"=>date('d-m-Y',strtotime($date_f)),"3"=>$promedio,"4"=>$rotacion,"5"=>0 );
}else{
	$finded_i=date('d-m-Y',strtotime($raw_date_finded[0]));
	$finded_f=date('d-m-Y',strtotime($raw_date_finded[$counter-1]));
	$promedio=$stock/$counter;
	$rotacion = round($cantidad_comprada/$promedio,2);
	if($rotacion == 0){
		$raw_rotation=array("1"=>$finded_i,"2"=>$finded_f,"3"=>$promedio,"4"=>$rotacion,"5"=>0 );
	}else{
		$raw_rotation=array("1"=>$finded_i,"2"=>$finded_f,"3"=>$promedio,"4"=>$rotacion,"5"=>round(30.41/$rotacion,1) );
	}
}
	echo json_encode($raw_rotation);
?>
