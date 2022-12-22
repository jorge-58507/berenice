<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];


$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$provider="SELECT AI_proveedor_id, TX_proveedor_nombre FROM bh_proveedor WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$provider=$provider."TX_proveedor_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$provider=$provider."TX_proveedor_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}
$qry_provider=$link->query($provider);
	echo "<select id='sel_provider' class='form-control' size='3'>";
	if($qry_provider->num_rows === 0){
		echo "<option value=''>&nbsp;</option>";
	}else{
		while ($rs_provider=$qry_provider->fetch_array()) {
			echo "<option value='$rs_provider[0]' onclick='set_txtprovider(this)'>$rs_provider[1]</option>";
		}
	}
	echo "</select>";

?>
