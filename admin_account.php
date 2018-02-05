<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');
require 'attached/php/req_login_admin.php';

$qry_account =	$link->query("SELECT bh_user.AI_user_id, bh_user.TX_user_seudonimo, bh_user.TX_user_type, bh_tuser.TX_tuser_value FROM (bh_user INNER JOIN bh_tuser ON bh_tuser.AI_tuser_id = bh_user.TX_user_type)")or die($link->error);
$qry_typeuser = $link->query("SELECT bh_tuser.AI_tuser_id, bh_tuser.TX_tuser_value FROM bh_tuser");
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/jshash-2.2/sha1.js"></script>
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



$("#btn_back").click(function(){
	history.back(1);
});

$("#btn_save_user").on("click",function(){
	if($("#txt_password").val() != $("#txt_password_2").val()){ $("#txt_password").focus(); return false; }
	if($("#txt_seudonimo").val() ==	""){ return false;	}
	$.ajax({ data: {"a" : $("#txt_seudonimo").val(),"c" : hex_sha1($('#txt_password').val()) }, type: "GET", dataType: "text", url: "attached/get/get_checkuser.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus );
		console.log("data: " + data);
		if(data > 0){ alert("Utilize otra contrase\u00F1a"); $("#txt_password").focus(); return false;}else{ plus_user(); }
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

})

$('#txt_seudonimo').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz ");
$('#txt_password').validCampoFranz('.0123456789abcdefghijklmnopqrstuvwxyz- ');
$("#txt_seudonimo").on("keyup",function(){
	this.value = this.value.toUpperCase();
})

$("#txt_filteraccount").on("keyup",function(){
	$.ajax({ data: {"a" : this.value }, type: "GET", dataType: "text", url: "attached/get/filter_user.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus );
		$("#tbl_account tbody").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})
$("#container_newuser").css("display","none");

$("#div_newuser").click(function(){
	$("#container_newuser").toggle(500);
	$("#div_newuser").toggleClass("fa-angle-double-down");
	$("#div_newuser").toggleClass("fa-angle-double-up");
});

});
function plus_user(){
	$('#txt_password').prop("value",
	hex_sha1($('#txt_password').val())
	)
	$.ajax({ data: {"a" : $("#txt_seudonimo").val(), "b" : $("#sel_type").val(), "c" : $("#txt_password").val() }, type: "GET", dataType: "text", url: "attached/get/plus_user.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus );
		$("#tbl_account tbody").html(data);
		$("#txt_seudonimo, #txt_password, #txt_password_2").val("");
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
<form action="" method="post" name="form_sell"  id="form_sell">

<div id="container_newuser" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtseudonimo" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label for="txt_seudonimo">Nombre</label>
			<input type="text" id="txt_seudonimo" placeholder="Nombre de Usuario" class="form-control" value=""	/>
		</div>
		<div id="container_seltype" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label for="sel_type">Tipo de Usuario</label>
			<select id="sel_type" class="form-control" name="sel_type">
<?php while ($rs_typeuser = $qry_typeuser->fetch_array()) {
			if ($rs_account['TX_user_type'] == $rs_typeuser['AI_tuser_id']) {
			?>
				<option value="<?php echo $rs_typeuser['AI_tuser_id']; ?>" selected="selected" ><?php echo $rs_typeuser['TX_tuser_value']; ?></option>
			<?php }else{ ?>
				<option value="<?php echo $rs_typeuser['AI_tuser_id']; ?>" ><?php echo $rs_typeuser['TX_tuser_value']; ?></option>
			<?php }
			} ?>
			</select>
		</div>
		<div id="container_txtpassword" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="txt_password">Contrase&ntilde;a</label>
			<input type="password" id="txt_password" class="form-control"	value="" />
		</div>
		<div id="container_txtpassword" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="txt_password_2">Confirmar</label>
			<input type="password" id="txt_password_2" class="form-control"	value="" />
		</div>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_save_user" class="btn btn-success">Nvo. Usuario</button>
		</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_div_newuser" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<i id="div_newuser" class="fa fa-angle-double-down" aria-hidden="true"> Nvo. Usuario</i>
  </div>
</div>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
</div>

<div id="container_filteraccount" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<div id="container_txtfilteraccount" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="txt_filteraccount">Buscar:</label>
		<input type="text" id="txt_filteraccount" class="form-control"	value="" />
	</div>
	<div id="container_tblaccount" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<table id="tbl_account" class="table table-bordered table-condensed table-striped table-hover">
			<caption>Usuarios Registrados</caption>
		<thead class="bg_green">
		<tr>
			<th>Nombre</th>
			<th>Tipo</th>
		</tr>
		</thead>
		<tfoot class="bg_green">
		<tr>
			<td></td><td></td>
		</tr>
		</tfoot>
		<tbody>
<?php 	while($rs_account=$qry_account->fetch_array()){ ?>
		<tr onclick="open_popup('popup_upduser.php?a=<?php echo $rs_account['AI_user_id']; ?>','popup_upduser','450','420')">
			<td><?php echo $rs_account['TX_user_seudonimo']; ?></td>
			<td><?php echo $rs_account['TX_tuser_value']; ?></td>
		</tr>
<?php 	} ?>
		</tbody>
		</table>
	</div>
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
