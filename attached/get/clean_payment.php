<?php
require '../../bh_con.php';
$link = conexion();

	mysql_query("DELETE FROM bh_pago WHERE pago_AI_user_id = '{$_COOKIE['coo_iuser']}'", $link) or die(mysql_error());

	echo "all right";
?>
