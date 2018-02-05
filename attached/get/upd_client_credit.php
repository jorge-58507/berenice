<?php
require '../../bh_con.php';
$link = conexion();

$client_id=$_GET['a'];
$limite_credito=$_GET['b'];
$plazo_credito=$_GET['c'];

$txt_checkcredit="SELECT * FROM bh_cliente WHERE AI_cliente_id = '$client_id'";
?>
<?php
$qry_checkcredit=mysql_query($txt_checkcredit, $link);
$nr_checkcredit=mysql_num_rows($qry_checkcredit);

	$txt_upd="UPDATE bh_cliente SET TX_cliente_limitecredito='$limite_credito', TX_cliente_plazocredito='$plazo_credito' WHERE AI_cliente_id = '$client_id'";
	mysql_query($txt_upd);
	
	echo $txt_upd;
	
?>
