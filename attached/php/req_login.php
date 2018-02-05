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

$qry_checklogin=mysql_query("SELECT * FROM bh_user WHERE AI_user_id = '$iuser'", $link);
$nr_checklogin=mysql_num_rows($qry_checklogin);
if($nr_checklogin > 0){
	$rs_checklogin=mysql_fetch_assoc($qry_checklogin);
	if($rs_checklogin['TX_user_type']!=$tuser){
		echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
	}
	if($rs_checklogin['TX_user_seudonimo']!=$suser){
		echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
	}
}else{
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}
?>
