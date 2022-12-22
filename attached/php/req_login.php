<?php
if(empty($_COOKIE['coo_iuser'])){
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}
if(empty($_COOKIE['coo_tuser'])){
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}
if(empty($_COOKIE['coo_suser'])){
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}
$iuser=$_COOKIE['coo_iuser'];
$tuser=$_COOKIE['coo_tuser'];
$suser=$_COOKIE['coo_suser'];
$access = 'true';
if(!is_object($link)){
	$qry_checklogin=mysql_query("SELECT * FROM bh_user WHERE AI_user_id = '$iuser'")or die(mysql_error());
	$nr_checklogin=mysql_num_rows($qry_checklogin);
	$rs_checklogin=mysql_fetch_assoc($qry_checklogin);
}else{
	$qry_checklogin=$link->query("SELECT * FROM bh_user WHERE AI_user_id = '$iuser'")or die($link->error);
	$nr_checklogin=$qry_checklogin->num_rows;
	$rs_checklogin=$qry_checklogin->fetch_array();
}
if($nr_checklogin > 0){
	if($rs_checklogin['TX_user_type']!=$tuser){
		$access='false';
	}
	if($rs_checklogin['TX_user_seudonimo']!=$suser){
		$access='false';
	}
}else{
	$access='false';
}
if($access == 'false'){
	header('Location: index.php');
}
date_default_timezone_set('America/Panama');

?>
