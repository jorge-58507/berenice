<?php
require '../../bh_conexion.php';
$link = conexion();

$name = $r_function->url_replace_special_character($_GET['a']);
$name = $r_function->replace_regular_character($name);

$cif=$_GET['b'];
$telephone=$_GET['c'];
$direction=$_GET['d'];
$client_id = $_GET['e'];
if(!empty($_GET['f'])){
	$activo="_".$_GET['f'];
	$function="";
}else{
	$activo="";
	$function="_oldsale";
}
$dv = $_GET['g'];
$habilitado = $_GET['h'];
$taxpayer=$_GET['i'];
$type=$_GET['j'];
$email=$_GET['k'];

$chk_client = $link->query("SELECT TX_cliente_cif FROM bh_cliente WHERE TX_cliente_cif = '$cif' AND AI_cliente_id != '$client_id'");
if ($chk_client->num_rows > 0) {
	$data['status'] = 'failed';

	echo json_encode($data);
	return false;
}

$link->query("UPDATE bh_cliente SET TX_cliente_nombre='$name', TX_cliente_cif='$cif', TX_cliente_direccion='$direction', TX_cliente_telefono='$telephone', TX_cliente_dv='$dv', TX_cliente_porcobrar='$habilitado', TX_cliente_contribuyente='$taxpayer', TX_cliente_tipo='$type', TX_cliente_correo='$email' WHERE AI_cliente_id = '$client_id'")or die($link->error);

//################################    ANSWER   ####################
$name = $r_function->replace_special_character($name);

$data['activo'] = $activo;
$data['name'] = $name;
$data['function'] = $function;
$data['client'] = $client_id;
$data['status'] = 'success';

echo json_encode($data);
return false;


?>
			<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $client_id; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" />
