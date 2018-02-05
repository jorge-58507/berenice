<?php
require '../../bh_con.php';
$link = conexion();

$name=str_replace("ampersand","&",$_GET['a']);
$name=strtoupper($name);
$cif=$_GET['b'];
$telephone=$_GET['c'];
$direction=$_GET['d'];

$client_id = $_GET['e'];

	mysql_query("UPDATE bh_cliente SET TX_cliente_nombre='$name', TX_cliente_cif='$cif', TX_cliente_direccion='$direction', TX_cliente_telefono='$telephone' WHERE AI_cliente_id = '$client_id'")or die(mysql_error());

//################################    ANSWER   ####################
?>

    	<label for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $client_id; ?>" id="txt_filterclient" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient(event)" />
