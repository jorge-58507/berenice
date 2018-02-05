<?php
require '../../bh_con.php';
date_default_timezone_set('America/Panama');
$link=conexion();
$date_i = date('Y-m',strtotime($_GET['a']));
$date_i = date('Y-m-d',strtotime($date_i));
$date_f = date('Y-m',strtotime($_GET['b']));
$date_f = date('Y-m-d',strtotime($date_f));

$product_id = $_GET['c'];

$datetime1=new DateTime($date_i);
$datetime2=new DateTime($date_f);
$interval=$datetime2->diff($datetime1);
$intervalMeses=$interval->format("%m");
$intervalAnos = $interval->format("%y")*12;
$meses = $intervalMeses+$intervalAnos;
$raw_fecha=array();
$fecha_sumada=$date_i;
for($i=0;$i<$meses;$i++){
	$fecha_sumada = date('Y-m-d',strtotime('+1 month',strtotime($fecha_sumada)));
	$raw_fecha[$i]=$fecha_sumada;
}

	$qry_json=mysql_query("SELECT TX_rotacion_json FROM bh_rotacion WHERE rotacion_AI_producto_id = '$product_id'")or die(mysql_error());
	$array_merged=array();
	while($rs_json=mysql_fetch_array($qry_json)){
		$decoded=json_decode($rs_json['TX_rotacion_json'],true);
		
		$year=date('Y',strtotime($rs_json['TX_rotacion_json']));
		$array_merged=array_merge($array_merged,json_decode($rs_json['TX_rotacion_json'],true));
	}
$stock=0;
$counter=0;
$raw_date_finded=array();
foreach($raw_fecha as $fecha){
	for($it=0;$it<count($array_merged);$it++){
		if(isset($array_merged[$it][$fecha])){
			$stock+=$array_merged[$it][$fecha];
			$raw_date_finded[$counter]=$fecha;
			$counter++;
		}
	}
}

$txt_purchase="SELECT TX_datocompra_cantidad FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) 
WHERE bh_facturacompra.TX_facturacompra_fecha >= '$date_i' AND  bh_facturacompra.TX_facturacompra_fecha <= '$date_f' AND bh_datocompra.datocompra_AI_producto_id = '$product_id'";
$qry_purchase=mysql_query($txt_purchase);
$cantidad_comprada=0;
while($rs_purchase=mysql_fetch_array($qry_purchase)){
$cantidad_comprada+=$rs_purchase[0];
}
//echo "<br /> Compras: ".$cantidad_comprada;

if($counter == 0){
	$promedio = 0;
	$rotacion = 0;
//	$json_rotation='{"a":'; 
//$a="1"; $b="2"; $c="3"; $d="4"; $e="5"; 
	$raw_rotation=array("1"=>date('d-m-Y',strtotime($date_i)),"2"=>date('d-m-Y',strtotime($date_f)),"3"=>$promedio,"4"=>$rotacion,"5"=>0 );
//	$raw_rotation=array($a=>$date_i,$b=>$date_f,$c=>$promedio,$d=>$rotacion,$e=>0 );
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