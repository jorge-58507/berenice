<!-- reload: upd_discount -->
<?php
require 'bh_con.php';
$link=conexion();
?>
<?php 
include 'attached/php/funk.php';
?>
<?php
date_default_timezone_set('America/Panama');
require 'attached/php/req_login_admin.php';
$nr_nextnc=mysql_fetch_row(mysql_query("SELECT TX_notadecredito_numero FROM bh_notadecredito ORDER BY TX_notadecredito_numero DESC LIMIT 1"));

$qry_client=mysql_query("SELECT AI_cliente_id, TX_cliente_nombre FROM bh_cliente ORDER BY TX_cliente_nombre ASC");
$rs_client=mysql_fetch_assoc($qry_client);

$qry_lastclientid = mysql_query("SELECT MAX(AI_cliente_id) AS id FROM bh_cliente");
if ($row = mysql_fetch_row($qry_lastclientid)) {
	$last_clientid = trim($row[0]);
	$next_clientid = $last_clientid+'1';
}

?>
<?php 
$fecha_actual=date('Y-m-d');
$pre_nc_numero = "00000000".($nr_nextnc[0]+1);
$nc_numero = (substr($pre_nc_numero,-8));
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
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>

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

$("#txt_ncmonto").validCampoFranz(".0123456789");

$("#container_nc_selclient").css("display","none");

$("#txt_filterclient").focus(function(){
	$("#container_nc_selclient").show(500);
})
$("#txt_filterclient").blur(function(){
	$("#container_nc_selclient").hide(500);
})

motivo_value="";
$("#txt_motivo").keyup(function(){
	this.value = this.value.toUpperCase();
	if(this.value.length > '40'){
		this.value=motivo_value;
	}else{
		motivo_value = this.value;
	}
});

$("#txt_filterclient").keyup(function(){
filter_client_newnc(this);
});

$("#btn_addclient").click(function(){
	open_addclient('<?php echo $next_clientid;?>');
});

$("#txt_motivo").keyup(function(){
//	this.value = this.value.toUpperCase();
});

$("#btn_save").click(function(){
	if($("#txt_filterclient").prop("alt") == ""){
		alert("Debe Agregar al Cliente Primero");
		return false;
	}
	if($("#txt_motivo").val() == ""){
		$("#txt_motivo").css("border",'2px outset #F00');
		return false;
	}else{
		$("#txt_motivo").css("border",'2px inset #797b7e80');
	}
	var ans = val_intwdec($("#txt_ncmonto").val())
	if(!ans){
		$("#txt_ncmonto").css("border",'2px outset #F00');
		return false;
	}else{
		$("#txt_ncmonto").css("border",'2px inset #797b7e80');
	}
	plus_newcreditnote();
});

$("#btn_cancel").click(function(){
	window.location='admin_facturaf.php';
});

$(window).on('beforeunload',function(){
	close_popup();
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
<form name="form_new_nc" id="form_new_nc" action="ins_newnc.php" method="post">

<div id="container_nc_info" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_nc_fecha" class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
		<label for="span_fecha">Fecha</label>
        <span id="span_fecha" class="form-control bg-disabled"><?php echo $fecha_actual; ?></span>
	</div>
	<div id="container_nc_numero" class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
		<label for="span_numero">Nº</label>
        <span id="span_numero" class="form-control bg-disabled"><?php echo $nc_numero; ?></span>
	</div>
</div>
<div id="container_nc_cliente" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_nc_clientfilter" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
		<label for="txt_filterclient">Cliente</label>
    	<input type="text" id="txt_filterclient" name="txt_filterclient" alt="" class="form-control" />
    </div>
	<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
		<button type="button" id="btn_addclient" class="btn btn-success"><strong>+</strong></button>
	</div>
    <div id="container_nc_selclient" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<select id="sel_client" size="2" class="form-control">
        <?php do{ ?>
        	<option value="<?php echo $rs_client['AI_cliente_id'] ?>" onclick="set_txtfilterclient(this);"><?php echo $rs_client['TX_cliente_nombre'] ?></option>
       <?php }while($rs_client=mysql_fetch_assoc($qry_client));
	   ?>
        </select>
	</div>
</div>
<div id="container_nc_input" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_nc_motivo" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<label for="txt_motivo" class="lbl">Motivo</label>
        <input type="text" id="txt_motivo" name="txt_motivo" class="form-control"  />
	</div>
	<div id="container_nc_monto" class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
		<label for="txt_ncmonto">Monto</label>
        <input type="text" id="txt_ncmonto" name="txt_ncmonto" class="form-control" />
	</div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
    &nbsp;
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>


<!-- ############# FIN DE CONTENT  ################-->
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
