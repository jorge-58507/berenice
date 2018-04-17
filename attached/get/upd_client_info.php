<?php
require '../../bh_conexion.php';
$link = conexion();

// $name=str_replace("ampersand","&",$_GET['a']);
//
$name = $r_function->url_replace_special_character($_GET['a']);
$name = $r_function->replace_regular_character($name);
// $name = strtoupper($name);
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

	$link->query("UPDATE bh_cliente SET TX_cliente_nombre='$name', TX_cliente_cif='$cif', TX_cliente_direccion='$direction', TX_cliente_telefono='$telephone' WHERE AI_cliente_id = '$client_id'")or die($link->error);

//################################    ANSWER   ####################
$name = $r_function->replace_special_character($name);
?>
			<label for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $client_id; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" />
