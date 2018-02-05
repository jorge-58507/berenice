<?php
require '../../bh_con.php';
$link=conexion();

session_start();

$raw_session_admin=array();
if(!empty($_COOKIE['coo_iuser'])){
	if($_COOKIE['coo_tuser'] < 3){
		$raw_session_admin[0][0]=$_COOKIE['coo_iuser'];
	}else if(!empty($_SESSION['admin'])){
		$raw_session_admin[0][0]=$_SESSION['admin'];
	}else{
		$raw_session_admin[0][0]="";
	}
};
$encode= json_encode($raw_session_admin);
echo $encode;
?>
