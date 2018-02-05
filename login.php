<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
session_start();
session_destroy();
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
$exe_checkpass=mysql_query($txt_checkpass, $link);
$nr_checkpass=mysql_num_rows($exe_checkpass);
if($nr_checkpass <= '0'){
	$login_alert = "<script type='text/javascript'>alert('Este usuario no existe')</script>";
	$login_content = "&nbsp; Para ingresar haga Click <a href='index.php' target='_self' class='link_1'>AQU&Iacute;</a>";
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>".$login_alert.$login_content;

}else{
	$rs_checkpass=mysql_fetch_assoc($exe_checkpass);
	$user_id=$rs_checkpass['AI_user_id'];
	$user_type=$rs_checkpass['TX_user_type'];
	$user_seudonimo=$rs_checkpass['TX_user_seudonimo'];

	//$login_alert = "<script type='text/javascript'>alert('Bienvenido:".$user_seudonimo."')</script>";

	create_cookies_user($user_id,$user_type,$user_seudonimo);

	$QRY_title=mysql_query("SELECT * FROM bh_opcion WHERE TX_opcion_titulo = 'titulo'");
	$RS_title=mysql_fetch_assoc($QRY_title);
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


/*if($rs_checkpass['TX_user_type']==1){
	$login_content = "&nbsp;Para ingresar haga Click <a href='stock.php' target='_self' >AQU&Iacute;</a>";
	echo "<meta http-equiv='Refresh' content='1;url=stock.php'>".$login_alert.$login_content;
}else{
	$login_content = "&nbsp;Para ingresar haga Click <a href='stock.php' target='_self' >AQU&Iacute;</a>";
	echo "<meta http-equiv='Refresh' content='1;url=new_purchase.php'>".$login_alert;
}
*/
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
