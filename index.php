<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<?php
function ObtenerIP(){
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
$ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
$ip = $_SERVER['REMOTE_ADDR'];
else
$ip = "IP desconocida";
return($ip);
}
$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

$txt_vendor="SELECT TX_user_seudonimo, TX_user_password FROM bh_user WHERE TX_user_type = '3'";
$qry_vendor=$link->query($txt_vendor);
$vendor_array=array();
$i=0;
while($rs_vendor=$qry_vendor->fetch_array()){
	$vendor_array[$i] = $rs_vendor;
	$i++;
};
function unlog_user(){
	if(!empty($_COOKIE['coo_iuser'])){
	setcookie('coo_iuser','',time()-100);
	}
	if(!empty($_COOKIE['coo_tuser'])){
	setcookie('coo_tuser','',time()-100);
	}
	if(!empty($_COOKIE['coo_suser'])){
	setcookie('coo_suser','',time()-100);
	}
	if(!empty($_COOKIE['coo_tittle'])){
	setcookie('coo_tittle','',time()-100);
	}
	if(!empty($_COOKIE['coo_usercliente'])){
	setcookie('coo_usercliente','',time()-100);
	}
}
if(!empty($_COOKIE['coo_iuser'])){
$link->query("UPDATE bh_user SET TX_user_online = '0' WHERE AI_user_id = '{$_COOKIE['coo_iuser']}' AND TX_user_cliente = '$cliente'")or die($link->error);
unlog_user();
}

?>
<link href="attached/image/f_icono.ico" rel="shortcut icon" type="icon" />
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/index_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/login_funct.js"></script>
<script type="text/javascript" src="attached/js/jshash-2.2/sha1.js"></script>


<script type="text/javascript">

$(document).ready(function() {

$('#password_login').focus();

$('#form_login').submit(function(){
	if($('#password_login').val() == ""){
		alert("Campo contraseña no puede estar vacio");
		return false;
	}
	$('#password_login').prop("value",
	hex_sha1($('#password_login').val())
	);
	var obj_form = document.forms['form_login'];
	obj_form.submit();
})

$("#container_expand_login").click(function(){
	$("#login_container").slideToggle(300);
	$("#container_expand_login").toggleClass("fa-angle-double-down");
	$("#container_expand_login").toggleClass("fa-angle-double-up");
	setTimeout($('#password_login').focus(),500);
});


});


</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">

		<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">




		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="login.php" method="post" name="form_login"  id="form_login">
	<div id="container_quickaccess" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<?php for($i=0;$i<count($vendor_array);$i++){?>
		<button type="button" id="btn_vendor" class="btn btn-lg btn-info" name="<?php echo $vendor_array[$i][1] ?>" onclick="quick_access(this);">
        <?php echo $vendor_array[$i][0] ?></button> &nbsp;&nbsp;
	<?php }?>
    </div>
    <div id="login_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		<strong>Contraseña: </strong>
		<input type="password" id="password_login" name="password_login" autofocus="autofocus">
        <br /><br />
        <button type="submit" id="btn_login" class="btn btn-default">
        Ingresar</button>
	</div>
    <div id="container_div_expand_login" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="container_expand_login" class="fa fa-angle-double-down"></div>
  </div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
