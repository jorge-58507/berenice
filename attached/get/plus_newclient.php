<?php
require '../../bh_conexion.php';
$link = conexion();
$name = $r_function->url_replace_special_character($_GET['a']);
$name = $r_function->replace_regular_character($name);
$cif=$_GET['b'];
$direction=strtoupper($_GET['c']);
$telephone=$_GET['d'];
if(!empty($_GET['e'])){
	$activo="_".$_GET['e'];
	$function="";
}else{
	$activo="";
	$function="_oldsale";
}

	$link->query("INSERT INTO bh_cliente (TX_cliente_nombre, TX_cliente_cif, TX_cliente_direccion, TX_cliente_telefono, TX_cliente_interes) VALUES ('$name', '$cif', '$direction', '$telephone', '0')")or die($link->error);
	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();

//################################    ANSWER   ####################
$name = $r_function->replace_special_character($name);
?>
    	<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $rs_lastid[0]; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" />
