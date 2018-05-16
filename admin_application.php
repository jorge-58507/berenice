<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

require 'attached/php/req_login_sale.php';

if(isset($_GET['a'])){
	$facturaventa_ids=$_GET['a'];
}else{
	$facturaventa_ids=0;
}
if(isset($_GET['b'])){
	$client_id=$_GET['b'];
}else{
	$client_id=0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});
$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})

$("label.label_blue_sky").parents('div').css("margin-top", "80");

});

function get_tool(root_tool){
	$.ajax({data: {"a" : root_tool }, type: "GET", dataType: "text", url: root_tool+"body_tool.php",})
	.done(function( data, textStatus, jqXHR ) {
		$("#container_tool").html(data)
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
    	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        Bienvenido: <label class="bg-primary">
         <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
        </label>
        </div>
		<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php
switch ($_COOKIE['coo_tuser']){
	case '1':
		include 'attached/php/nav_master.php';
	break;
	case '2':
		include 'attached/php/nav_admin.php';
	break;
	case '3':
		include 'attached/php/nav_sale.php';
	break;
	case '4':
		include 'attached/php/nav_paydesk.php';
	break;
	case '5':
		include 'attached/php/nav_stock.php';
	break;
}
?>
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="login.php" method="post" name="form_login"  id="form_login">
<?php
	$raw_tool  = scandir('attached/tool/');
	$raw_directories = array();
	foreach ($raw_tool as $key => $value) {
		if (!strpos($value,'.')) {
			$raw_directories[] = $value;
		}
	}
 ?>
<div id="container_btn_scroll" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<div id="container_scroll" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
<?php
	function read_json_tool($name_tool){
		$content = file_get_contents("attached/tool/".$name_tool."/".$name_tool.".json");
		return $content;
	}
		foreach ($raw_directories as $key => $folder) {
			if ($folder != '.' && $folder != '..' ) {
				$root_tool = "attached/tool/".$folder."/";
				$json_tool = read_json_tool($folder);
				$raw_tool = json_decode($json_tool, true);
				if ($key%2==0) {?>
					<button type="button" id="" class="btn btn-info btn-block" onclick="get_tool('<?php echo $root_tool; ?>')"><strong><?php echo $raw_tool['titulo']; ?></strong></button>
<?php		}else{?>
					<button type="button" id="" class="btn btn-default btn-block" onclick="get_tool('<?php echo $root_tool; ?>')"><strong><?php echo $raw_tool['titulo']; ?></strong></button>
	<?php	}
			}
		}
 ?>
	</div>
</div>
<div id="container_tool" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

</div>


</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>

</body>
</html>
