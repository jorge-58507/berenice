<?php
require '../../bh_conexion.php';

$link = conexion();
$name = $r_function->url_replace_special_character($_GET['a']);
$name = preg_replace('/^\s+|\s+$/', '', $r_function->replace_regular_character($name));
$cif = preg_replace('/^\s+|\s+$/', '', $_GET['b']);
$direction=strtoupper($_GET['d']);
$direction = preg_replace('/^\s+|\s+$/', '', $direction);
$telephone = preg_replace('/^\s+|\s+$/', '', $_GET['c']);
$client_id = $_GET['e'];
if(!empty($_GET['f'])){
	$activo="_".$_GET['f'];
	$function="unset_filterclient";
}else{
	$activo="";
	$function="unset_filterclient_oldsale";
}
$dv = preg_replace('/^\s+|\s+$/', '', $_GET['g']);
$habilitado = $_GET['h'];
$taxpayer=$_GET['i'];
$type=$_GET['j'];
$email=preg_replace('/^\s+|\s+$/', '', $_GET['k']);

$chk_client = $link->query("SELECT TX_cliente_cif FROM bh_cliente WHERE TX_cliente_cif = '$cif' AND AI_cliente_id != '$client_id'");
if ($chk_client->num_rows > 0) {
	echo json_encode(['status' => 'failed', 'message' => 'Este RUC/Cedula ya existe.']);
	return false;
}

$link->query("UPDATE bh_cliente SET TX_cliente_nombre='$name', TX_cliente_cif='$cif', TX_cliente_direccion='$direction', TX_cliente_telefono='$telephone', TX_cliente_dv='$dv', TX_cliente_porcobrar='$habilitado', TX_cliente_contribuyente='$taxpayer', TX_cliente_tipo='$type', TX_cliente_correo='$email' WHERE AI_cliente_id = '$client_id'")or die($link->error);



//################################    ANSWER   ####################
$name = $r_function->replace_special_character($name);
echo json_encode([
	'status' => 'success',
	'client_id' => $client_id,
	'active' => $activo,
	'name' => $name,
	'function' => $function
]);
return false;

// $data['activo'] = $activo;
// $data['name'] = $name;
// $data['function'] = $function;
// $data['client'] = $client_id;
// $data['status'] = 'success';

// echo json_encode($data);


?>
			<!-- <label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $client_id; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" /> -->
