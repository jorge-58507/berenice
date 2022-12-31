<?php
require '../../bh_conexion.php';

$link = conexion();
$name = $r_function->url_replace_special_character($_GET['a']);
$name = preg_replace('/^\s+|\s+$/', '', $r_function->replace_regular_character($name)); 
$cif= preg_replace('/^\s+|\s+$/', '', $_GET['b']);
$direction=strtoupper($_GET['c']);
$direction = preg_replace('/^\s+|\s+$/', '', $direction);
$telephone = preg_replace('/^\s+|\s+$/', '', $_GET['d']);

if(!empty($_GET['e'])){
	$activo="_".$_GET['e'];
	$function="unset_filterclient";
}else{
	$activo="";
	$function="unset_filterclient_oldsale";
}
$dv=preg_replace('/^\s+|\s+$/', '', $_GET['f']);

$taxpayer=$_GET['g'];
$type=$_GET['h'];
$email=preg_replace('/^\s+|\s+$/', '', $_GET['i']);

echo $name; return false;

$verify_client = $link->query("SELECT AI_cliente_id FROM bh_cliente WHERE TX_cliente_cif = '$cif' AND TX_cliente_dv = '$dv' AND TX_cliente_nombre = '$name'") or die ($link->error);
if ($verify_client->num_rows > 0) {
	echo json_encode(['status' => 'denied', 'message' => 'Este cliente ya existe.']);
	return false;
}

$link->query("INSERT INTO bh_cliente (TX_cliente_nombre, TX_cliente_cif, TX_cliente_direccion, TX_cliente_telefono, TX_cliente_interes, TX_cliente_dv, TX_cliente_restringido, TX_cliente_contribuyente, TX_cliente_tipo, TX_cliente_correo) VALUES ('$name', '$cif', '$direction', '$telephone', '0', '$dv', '1', '$taxpayer', '$type', '$email')")or die($link->error);
$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
$rs_lastid = $qry_lastid->fetch_array();

//################################    ANSWER   ####################
$name = $r_function->replace_special_character($name);
echo json_encode([
	'status' => 'success',
	'client_id' => $rs_lastid[0],
	'active' => $activo,
	'name' => $name,
	'function' => $function
]);
return false;
?>
    	<!-- <label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $rs_lastid[0]; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" /> -->
