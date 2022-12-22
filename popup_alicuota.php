<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
$qry_taxes=mysql_query("SELECT AI_impuesto_id, TX_impuesto_nombre, TX_impuesto_value FROM bh_impuesto");
$raw_taxes=array();
$i=1;
while($rs_taxes=mysql_fetch_assoc($qry_taxes)){
	$raw_taxes[$i]=$rs_taxes;
	$i++;
}
 $json_taxes = json_encode($raw_taxes);
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
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#btn_acept').click(function(){
	var raw_form = $("form input[type=text]").toArray();
	var form_length = raw_form.length;
	var raw_value=new Object();
	for(i=0;i<form_length;i++){
	var id=$(raw_form[i]).attr("name");
		raw_value[id]=$(raw_form[i]).val();
	}
	$.ajax({	data: {"a" : raw_value},	type: "GET",	dataType: "text",	url: "attached/get/upd_taxes.php",	})
	.done(function( data, textStatus, jqXHR ) {
		console.log("GOOD" + textStatus);
		window.opener.$("#span_impuesto").html(data);
		if (data) {
				setTimeout(function(){	self.close(); },300)
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus);	});
})
$('#btn_cancel').click(function(){
	self.close();
})

$('#txt_telephone').validCampoFranz('0123456789 -');
$('#txt_cif').validCampoFranz('0123456789-');

var i=0;

$("#btn_create").on("click",function(){
	window.location.href="popup_create_tax.php"
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
<form method="post" name="form_addprovider">
<div id="container_taxes" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php
	foreach($raw_taxes as $index => $taxes){
	?>
<div id="container_txt<?php echo $index; ?>" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_<?php echo str_replace(" ","",$taxes['TX_impuesto_nombre']); ?>">
	<?php echo $taxes['TX_impuesto_nombre']; ?></label>
    <input type="text" id="txt_<?php echo str_replace(" ","",$taxes['TX_impuesto_nombre']); ?>" name="<?php echo $taxes['AI_impuesto_id']; ?>" class="form-control" value="<?php echo $taxes['TX_impuesto_value']; ?>" />
</div>
<?php
	}
?>
</div>

<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_create" class="btn btn-default">Gestionar</button>
		&nbsp;&nbsp;
    <button type="button" id="btn_acept" class="btn btn-success">Guardar</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
