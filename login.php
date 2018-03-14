<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');
session_start();
session_destroy();

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>


<link href="attached/css/index_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/various.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {


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

<?php
function create_cookies_user($id_user,$user_type,$user_seudonimo){
	setcookie("coo_iuser","".$id_user."",time()+(60*60*24*365));
	setcookie("coo_tuser","".$user_type."",time()+(60*60*24*365));
	setcookie("coo_suser","".$user_seudonimo."",time()+(60*60*24*365));
}

$pass=$_POST['password_login'];


$txt_checkpass="SELECT * FROM bh_user WHERE TX_user_password = '$pass'";
$exe_checkpass=$link->query($txt_checkpass);
$nr_checkpass=$exe_checkpass->num_rows;
if($nr_checkpass <= '0'){
	$login_alert = "<script type='text/javascript'>alert('Este usuario no existe')</script>";
	$login_content = "&nbsp; Para ingresar haga Click <a href='index.php' target='_self' class='link_1'>AQU&Iacute;</a>";
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>".$login_alert.$login_content;

}else{
	$rs_checkpass=$exe_checkpass->fetch_array();
	$user_id=$rs_checkpass['AI_user_id'];  	$user_type=$rs_checkpass['TX_user_type'];  	$user_seudonimo=$rs_checkpass['TX_user_seudonimo'];
	create_cookies_user($user_id,$user_type,$user_seudonimo);
	$QRY_title=$link->query("SELECT * FROM bh_opcion WHERE TX_opcion_titulo = 'titulo'")or die($link->error);
	$RS_title=$QRY_title->fetch_array();
	$title=$RS_title['TX_opcion_value'];
	setcookie("coo_tittle","".$title."",time()+86400);
  $user_type=$rs_checkpass['TX_user_type'];
  switch ($user_type) {
    case "1":
		$login_content = "&nbsp;Para ingresar haga Click <a href='start.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=start.php'>".$login_content;
        break;
    case "2":
		$login_content = "&nbsp;Para ingresar haga Click <a href='start.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=start.php'>".$login_content;
        break;
    case "3":
		$login_content = "&nbsp;Para ingresar haga Click <a href='sale.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=sale.php'>".$login_content;
        break;
    case "4":
		$login_content = "&nbsp;Para ingresar haga Click <a href='paydesk.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=paydesk.php'>".$login_content;
        break;
    case "5":
		$login_content = "&nbsp;Para ingresar haga Click <a href='stock.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=stock.php'>".$login_content;
        break;
    default:
		$login_content = "&nbsp;Para ingresar haga Click <a href='start.php' target='_self' >AQU&Iacute;</a>";
		echo "<meta http-equiv='Refresh' content='1;url=start.php'>".$login_content;
  }

  $file = fopen("login_log.txt", "a");
  fwrite($file, date('d-m-Y H:i:s')." (".$cliente.")"." - ".$user_seudonimo.PHP_EOL );
  fclose($file);

  $qry_user=$link->query("SELECT AI_user_id, TX_user_cliente FROM bh_user WHERE AI_user_id = '$user_id' AND TX_user_online = 1")or die($link->error);
  $rs_user=$qry_user->fetch_array(MYSQLI_ASSOC);
  if ($qry_user->num_rows > 0) {
    setcookie("coo_usercliente","".$rs_user['TX_user_cliente']."",time()+(60*60*24*365));
  }else{
      $link->query("UPDATE bh_user SET TX_user_online = '1', TX_user_cliente = '$cliente' WHERE AI_user_id = '$user_id'")or die($link->error);
  }

}
 ?>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
</head>

<body>
</body>
</html>
