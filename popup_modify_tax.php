<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
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
<link rel="stylesheet" href="attached/css/font-awesome.css" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	window.resizeTo("500","488")

	$('#btn_cancel').click(function(){
		window.location.href = "popup_updproduct.php?a=<?php echo $_GET['a']; ?>";
	});

	$('#btn_save').click(function(){
		save_product_exento();
	});


	$("#sel_taxes").on("change",function(){
		add_rawtaxes(this)
	})



});
var raw_taxes = [];
var i = 0;
function add_rawtaxes(field){
	if(field.value <= 0.1){ return false; }
	for (var i = 0; i < raw_taxes.length; i++) {
		if (raw_taxes[i].value == field.value) {
			return false;
		}
	}
	var tr_tax = new Object();
	tr_tax['value']=field.value;
	tr_tax['text']=$("#sel_taxes option:selected").text();
	tr_tax['id']=$("#sel_taxes option:selected").attr("label");
	raw_taxes[i]= tr_tax;
	i++;
	print_rawtaxes(raw_taxes);
}
var total_tax=0;
function print_rawtaxes(obj){
	total_tax=0;
	var content_tbody=""
	for(it=0;it<obj.length;it++){
		total_tax = total_tax+parseFloat(obj[it]['value']);
		content_tbody = content_tbody+"<tr><td>"+obj[it]['text']+"</td><td>"+obj[it]['value']+"</td><td><button type='button' name='"+it+"' class='btn btn-danger btn-xs' onclick='javascript: remove_rawtaxes(this.name)'>X</button></td></tr>";
	}
	$("#tbl_taxes tbody").html(content_tbody);
//	alert(total_tax);
}
function remove_rawtaxes(index){
	raw_taxes.splice(index,1);
	print_rawtaxes(raw_taxes);
}
function save_product_exento(){
$.ajax({	data: {"a" : total_tax, "b" : <?php echo $_GET['a']; ?>, "c" : raw_taxes},	type: "GET",	dataType: "text",	url: "attached/get/upd_product_exento.php",	})
.done(function( data, textStatus, jqXHR ) {
	console.log("GOOD " + data);
	setTimeout(function(){	window.location.href = "popup_updproduct.php?a=<?php echo $_GET['a']; ?>"	},300);

})
.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
}


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

<div id="container_modify_tax" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<?php
		$qry_taxes = $link->query("SELECT TX_impuesto_value, TX_impuesto_nombre, AI_impuesto_id FROM bh_impuesto");
		$rs_taxes =	$qry_taxes->fetch_array();
	 ?>
	<div id="container_seltaxes" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="sel_taxes">Impuestos</label>
		<select id="sel_taxes" class="form-control">
			<?php
			echo "<option value='0'>SELECCIONAR</option>";
			do {
				echo "<option value='$rs_taxes[0]' label='$rs_taxes[2]'>$rs_taxes[1]</option>";
			} while ($rs_taxes = $qry_taxes->fetch_array());
			?>
		</select>
	</div>
	<div id="container_tbltaxes" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<table id="tbl_taxes" class="table table-bordered table-condensed table-striped">
			<thead class="bg_green">
				<th></th><th></th><th></th>
			</thead>
			<tbody>
				<tr>

				</tr>
			</tbody>
			<tfoot class="bg_green">
				<tr>
					<td></td><td></td><td></td>
				</tr>
			</tfoot>
		</table>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
			&nbsp;&nbsp;
			<button type="button" id="btn_cancel" class="btn btn-warning">Salir</button>
		</div>
	</div>
</div>


</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
