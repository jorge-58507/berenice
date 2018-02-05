<?php
require 'bh_con.php';
$link=conexion();

require 'attached/php/req_login.php';

session_start();
unset($_SESSION['admin']);

if(isset($_GET['a'])){
$datoventa_id=$_GET['a'];
}else{
$datoventa_id=0;
}
if(isset($_GET['b'])){
$client_id=$_GET['b'];
}else{
$client_id=0;
}
$goto = $_GET['z'];

$raw_data = array($datoventa_id, $client_id);

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
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/jshash-2.2/sha1.js"></script>
<script type="text/javascript">

$(document).ready(function() {
$('#txt_adminpassword').focus();

$(document.forms['form_loginadmin']).submit(function(){

	if($('#txt_adminpassword').val() == ""){
		alert("Campo contraseña no puede estar vacio");
		return false;
	}
	$('#txt_adminpassword').prop("value",	hex_sha1($('#txt_adminpassword').val())	);

	$.ajax({	data: {"a" : $('#txt_adminpassword').val() },	type: "GET",	dataType: "text",	url: "attached/get/get_loginuser_popup.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus + "   " + data);
	 if(data === '1'){
			var ans = confirm('¿Abandonara la presente pagina?');
			if(ans){
				window.opener.document.location='<?php echo $goto; ?>?a=<?php echo $datoventa_id; ?>&b=<?php echo  $client_id; ?>';
			}else{
				self.close();
			}
		}else{
			$('#txt_adminpassword').css("border", "2px outset #8D0000");
			$('#txt_adminpassword').val("");
		}
 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	 return false
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

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form method="post" name="form_loginadmin">
<div id="container_adminpassword" class="col-xs-8 col-sm-6 col-md-4 col-lg-2">

	<label for="txt_adminpassword">Contrase&ntilde;a</label>
    <input type="password" id="txt_adminpassword" name="txt_adminpassword" class="form-control input-sm" placeholder="Contrase&ntilde;a" />
    <button type="submit" id="btn_login" class="btn btn-default" >Ingresar</button>
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
