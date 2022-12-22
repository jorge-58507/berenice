<?php
require '../../bh_conexion.php';
$link = conexion();

$value = $r_function->replace_regular_character($_GET['term']);
$raw_value = explode(' ', $value);
$txt_proveedor="SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion, TX_proveedor_telefono FROM bh_proveedor WHERE ";
foreach ($raw_value as $key => $value) {
	if ($value === end($raw_value)) {
		$txt_proveedor .= " TX_proveedor_nombre LIKE '%$value%'";
	}else {
		$txt_proveedor .= " TX_proveedor_nombre LIKE '%$value%' OR";
	}
}
// echo $txt_proveedor."ORDER BY TX_proveedor_nombre ASC LIMIT 20";
$qry_proveedor=$link->query($txt_proveedor."ORDER BY TX_proveedor_nombre ASC LIMIT 20");

$raw_provider=array();
$i=0;
while ($rs_proveedor = $qry_proveedor->fetch_array(MYSQLI_ASSOC)) {
	$raw_provider[$i]['id'] = $rs_proveedor['AI_proveedor_id'];
	$raw_provider[$i]['value'] = $r_function->replace_special_character($rs_proveedor['TX_proveedor_nombre']);
	$i++;
}

echo json_encode($raw_provider);



?>
