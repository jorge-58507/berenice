<?php
require '../../bh_con.php';
$link=conexion();

session_start();
unset($_SESSION['admin']);

function create_session_admin($id_user){
	$_SESSION['admin']=$id_user;
}
$pass=$_GET['a'];

$qry_checkpass=mysql_query("SELECT AI_user_id FROM bh_user WHERE TX_user_password = '$pass' AND TX_user_type = '2' OR TX_user_password = '$pass' AND TX_user_type = '1'", $link);
$nr_checkpass=mysql_num_rows($qry_checkpass);

if($nr_checkpass < '1'){
	echo "0";
}else{
	$row_checkpass=mysql_fetch_row($qry_checkpass);
	create_session_admin($row_checkpass[0]);
	echo "1";
}



?>
