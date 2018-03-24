<?php
require '../../bh_conexion.php';
$link = conexion();
$name=str_replace("ampersand","&",$_GET['a']);
$name=strtoupper($name);
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
// $activo="_".$_GET['e'];

	$link->query("INSERT INTO bh_cliente (TX_cliente_nombre, TX_cliente_cif, TX_cliente_direccion, TX_cliente_telefono, TX_cliente_interes)
VALUES ('$name', '$cif', '$direction', '$telephone', '0')");
	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();

//################################    ANSWER   ####################
?>

    	<label for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $rs_lastid[0]; ?>" id="txt_filterclient<?php echo $activo; ?>" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient<?php echo $function; ?>(event)" />
