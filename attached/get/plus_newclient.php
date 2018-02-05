<?php
require '../../bh_con.php';
$link = conexion();
$name=str_replace("ampersand","&",$_GET['a']);
$name=strtoupper($name);
$cif=$_GET['b'];
$direction=strtoupper($_GET['c']);
$telephone=$_GET['d'];

	mysql_query("INSERT INTO bh_cliente (TX_cliente_nombre, TX_cliente_cif, TX_cliente_direccion, TX_cliente_telefono, TX_cliente_interes)
VALUES ('$name', '$cif', '$direction', '$telephone', '0')");
	$qry_lastid=mysql_query("SELECT LAST_INSERT_ID();");
	$rs_lastid = mysql_fetch_row($qry_lastid);

//################################    ANSWER   ####################
?>

    	<label for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $rs_lastid[0]; ?>" id="txt_filterclient" name="txt_filterclient" value="<?php echo $name; ?>" onkeyup="unset_filterclient(event)" />
