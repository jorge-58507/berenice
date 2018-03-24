<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['term'];

$arr_value = (explode(' ',$value));
$txt_proveedor="SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_telefono, TX_proveedor_direccion FROM bh_proveedor WHERE ";
foreach ($arr_value as $key => $value) {
	if ($value === end($arr_value)) {
		$txt_proveedor=$txt_proveedor."TX_proveedor_nombre LIKE '%{$value}%'";
	}else{
		$txt_proveedor=$txt_proveedor."TX_proveedor_nombre LIKE '%{$value}%' AND ";
	}

}
$qry_proveedor=$link->query($txt_proveedor." ORDER BY TX_proveedor_nombre ASC")or die($link->error);
$raw_proveedor = array();	$i=0;
while($rs_proveedor=$qry_proveedor->fetch_array(MYSQLI_ASSOC)){
	if (substr_count($rs_proveedor['TX_proveedor_nombre'], 'NO USAR') < 1 ) {
		$raw_proveedor[$i]['id'] = $rs_proveedor['AI_proveedor_id'];
		$raw_proveedor[$i]['value'] = $rs_proveedor['TX_proveedor_nombre'];
		$raw_proveedor[$i]['telefono'] = $rs_proveedor['TX_proveedor_telefono'];
		$raw_proveedor[$i]['direccion'] = $rs_proveedor['TX_proveedor_direccion'];
	$i++;
	}
}

echo json_encode($raw_proveedor);
?>
