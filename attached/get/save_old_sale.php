<?php
require '../../bh_conexion.php';
$link = conexion();
$client_id=$_GET['b'];
$time=strtotime($_GET['a']);
$date=date('Y-m-d',$time);
$vendor_id=$_GET['d'];
$number=$_GET['e'];
$observation=$_GET['g'];

$qry_facturaventa=$link->query("SELECT AI_facturaventa_id FROM bh_facturaventa WHERE TX_facturaventa_numero = '$number' AND facturaventa_AI_facturaf_id is NULL")or die($link->error);
if ($qry_facturaventa->num_rows < 1) {
	echo "denied";
	return false;
}

$qry_ins_datoventa = $link->prepare("INSERT INTO bh_datoventa (datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion) VALUES (?,?,?,?,?,?,?,?)")or die($link->error);

$qry_chkexento = $link->query("SELECT AI_cliente_id FROM bh_cliente WHERE AI_cliente_id = '$client_id' AND TX_cliente_exento = '1'")or die($link->error);
$nr_chkexento = $qry_chkexento->num_rows;

$qry_nuevaventa=$link->query("SELECT nuevaventa_AI_producto_id, TX_nuevaventa_unidades, TX_nuevaventa_precio, TX_nuevaventa_descuento, TX_nuevaventa_itbm, TX_nuevaventa_descripcion FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$total=0; $i=0; $raw_nuevaventa=array();
while($rs_nuevaventa=$qry_nuevaventa->fetch_array()){
	$precio = $rs_nuevaventa['TX_nuevaventa_unidades']*$rs_nuevaventa['TX_nuevaventa_precio'];
	$descuento = ($precio*$rs_nuevaventa['TX_nuevaventa_descuento'])/100;
	$precio_descuento = $precio-$descuento;
	$impuesto = ($precio_descuento*$rs_nuevaventa['TX_nuevaventa_itbm'])/100;
	$precio_impuesto = $precio_descuento+$impuesto;
	$total += $precio_impuesto;
	$raw_nuevaventa[$i]['producto']=$rs_nuevaventa['nuevaventa_AI_producto_id'];
	$raw_nuevaventa[$i]['cantidad']=$rs_nuevaventa['TX_nuevaventa_unidades'];
	$raw_nuevaventa[$i]['precio']=$rs_nuevaventa['TX_nuevaventa_precio'];
	$raw_nuevaventa[$i]['descuento']=$rs_nuevaventa['TX_nuevaventa_descuento'];
	if($nr_chkexento > 0){
		$raw_nuevaventa[$i]['impuesto']=0;
	}else{
		$raw_nuevaventa[$i]['impuesto']=$rs_nuevaventa['TX_nuevaventa_itbm'];
	}
	$raw_nuevaventa[$i]['descripcion']=$rs_nuevaventa['TX_nuevaventa_descripcion'];

	$i++;
}
$total=round($total,2);

$rs_facturaventa=$qry_facturaventa->fetch_array(MYSQLI_ASSOC);

$link->query("UPDATE bh_facturaventa SET TX_facturaventa_fecha='$date', facturaventa_AI_cliente_id='$client_id', TX_facturaventa_total='$total', TX_facturaventa_observacion='$observation' WHERE AI_facturaventa_id = '{$rs_facturaventa['AI_facturaventa_id']}'")or die($link->error);

$link->query("DELETE FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '{$rs_facturaventa['AI_facturaventa_id']}'");

foreach ($raw_nuevaventa as $key => $value) {
	$value['descripcion'] = $r_function->replace_regular_character($value['descripcion']);
	$qry_ins_datoventa->bind_param("iiidddds",$rs_facturaventa['AI_facturaventa_id'],$_COOKIE['coo_iuser'],$value['producto'],$value['cantidad'],$value['precio'],$value['impuesto'],$value['descuento'],$value['descripcion']);
	$qry_ins_datoventa->execute();
}

$bh_del="DELETE FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
$link->query($bh_del) or die($link->error);

echo "acepted";
?>
