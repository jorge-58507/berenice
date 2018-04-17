<?php
require 'bh_conexion.php';
$link=conexion();

$order_id=$_GET['a'];

$qry_datopedido = $link->query("SELECT bh_producto.TX_producto_value, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.TX_datopedido_precio, bh_datopedido.AI_datopedido_id FROM (bh_datopedido
	INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id)
WHERE bh_datopedido.datopedido_AI_pedido_id = '$order_id' ");
$nr_datopedido = $qry_datopedido->num_rows;

$it=0;
$raw_datopedido=array();
while ($rs_datopedido=$qry_datopedido->fetch_array()) {
	$raw_datopedido[$it]=$rs_datopedido;
	$it++;
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
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#btn_acept').click(function(){
	var ans = confirm("¿Desea Ingresar la Compra?");
	var raw_form = $("form input[type=text]").toArray();
	var form_length = raw_form.length;
	var raw_value=new Object();
	for(i=0;i<form_length;i++){
	var id=$(raw_form[i]).attr("name");
			raw_value[id]=$(raw_form[i]).val();
	}
	json_string = JSON.stringify(raw_value);
	$.ajax({	data: {"a" : raw_value},	type: "GET",	dataType: "text",	url: "attached/get/upd_order.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if(!ans){ window.opener.location.reload(); this.close();};
		if(data && ans){ window.opener.location="new_purchase.php?a="+json_string;	};
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD" +  textStatus);	});

});
$('#btn_cancel').click(function(){
	self.close();
});

$("form input[type=text]").validCampoFranz('0123456789.');
$("form input[type=text]").on("blur",function(){
	this.value = val_intw2dec(this.value);
})


});



</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
		<div id="logo" ></div>
	</div>

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form id="form_process_order" method="post" name="form_product2addcollect">
	<div id="container_tbldatopedido" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<table id="tbl_datopedido" class="table table-bordered table-condensed table-striped">
		<caption>Listado de Productos</caption>
		<thead class="bg_green">
		<tr>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
			<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Detalle</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
		</tr>
		</thead>
		<tbody>
<?php
	foreach ($raw_datopedido as $key => $datopedido) {
?>
			<tr>
				<td>
					<input type="text" id="<?php echo $datopedido[3]; ?>" name="<?php echo $datopedido[3]; ?>" class="form-control input-sm" value="<?php echo $datopedido[1]; ?>" />
				</td>
				<td><?php echo $datopedido[1]; ?></td>
				<td><?php echo $datopedido[0]; ?></td>
				<td><?php echo $datopedido[2]; ?></td>
			</tr>
<?php
		}
?>
		</tbody>
		<tfoot class="bg_green">
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		</tfoot>
		</table>
	</div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_acept" name="button" class="btn btn-success btn-md">Aceptar</button>
		&nbsp;&nbsp;
		<button type="button" id="btn_cancel" name="button" class="btn btn-warning btn-md">Cancelar</button>

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
