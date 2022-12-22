<?php
require 'bh_conexion.php';
$link=conexion();

$qry_medida=$link->query("SELECT * FROM bh_medida WHERE TX_medida_value = 'UNIDADES'")or die($link->error);
$rs_medida=$qry_medida->fetch_array();

$qry_letra=$link->query("SELECT bh_letra.AI_letra_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM bh_letra")or die($link->error);

$qry_impuesto = $link->query("SELECT SUM(TX_impuesto_value) as impuesto FROM bh_impuesto WHERE TX_impuesto_categoria = 'GENERAL'")or die($link->error);
$rs_impuesto = $qry_impuesto->fetch_array();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	$('#btn_cancel_product').click(function(){
		self.close();
	});
	$("#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5").on("blur",function(){
		this.value = val_intw2dec(this.value);
	})

	$('#btn_save_product').click(function(){
		if($("#txt_nombre").val() ===	"" || $("#txt_codigo").val() ===	"" || $("#txt_impuesto").val() ===	"" || $("#txt_cantmaxima").val() ===	"" || $("#txt_cantminima").val() ===	"" || $("#txt_cantidad").val() ===	"" || $("#txt_p_4").val() ===	""){
			return false;
		}
		$.ajax({	data: {"a" : $("#txt_codigo").val(), "b" : $("#txt_referencia").val(), "c" : url_replace_regular_character($("#txt_nombre").val()), "d" : $("#sel_medida").val(), "e" : $("#txt_cantidad").val(), "f" : $("#txt_cantmaxima").val(), "g" : $("#txt_cantminima").val(), "h" : $("#txt_impuesto").val(), "i" : $("#sel_letter").val(), "j" : $("#txt_p_1").val(), "k" : $("#txt_p_2").val(), "l" : $("#txt_p_3").val(), "m" : $("#txt_p_4").val(), "n" : $("#txt_p_5").val(), "o" : $("#sel_subfamilia").val() }, type: "GET", dataType: "text", url: "attached/get/plus_newproduct_popup.php",	})
		.done(function( data, textStatus, jqXHR ) {
			window.opener.open_product2purchase(data);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$("#txt_nombre").on("blur", function(){
		$("#txt_nombre").val(this.value.toUpperCase());
	});
	$("#txt_referencia").on("blur", function(){
		$("#txt_referencia").val(this.value.toUpperCase());
	});
	
	$("#txt_codigo").on("blur", function(){
		if(this.value.length == '6'){
			this.value = "0000000"+this.value;
		}
	});
	$( function() {
		$("#txt_codigo").autocomplete({
			source: "attached/get/filter_producto_codigo.php",
			minLength: 2,
			select: function( event, ui ) {
				splited_value = ui.item.value.split(" | ");
				new_value = splited_value[0];
				ui.item.value = new_value;
				fire_recall('container_product_recall', '¡Atencion!, Codigo a duplicar')
			}
		});
	});


	$('#txt_cantidad, #txt_impuesto, #txt_cantminima, #txt_cantmaxima').validCampoFranz('.0123456789');
	$('#txt_referencia').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz/- ")
	$('#txt_nombre').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyzº'#/-; ");
	$('#txt_codigo').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz");
	$('#txt_precio1, #txt_precio2, #txt_precio3, #txt_precio4, #txt_precio5').validCampoFranz('.0123456789');
});
function generate_code () {	
	var subfamily = document.getElementById('sel_subfamilia').value;
	data = {"a":subfamily}
	url_data = data_fetch(data);
	var myRequest = new Request(`attached/get/code_generator.php${url_data}`);
	fetch(myRequest)
	.then(function(response) {
		return response.text()
		.then(function(text) {
			document.getElementById('txt_codigo').value = text;
		});
	});
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
<div id="container_upd_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6" >

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<label class="label label_blue_sky" for="txt_nombre">Descripci&oacute;n:</label>
				<input type="text" class="form-control input-sm" id="txt_nombre" name="txt_nombre" >
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<label class="label label_blue_sky" for="txt_referencia">Referencia:</label>
				<input type="text" id="txt_referencia" name="txt_referencia" class="form-control input-sm" value=""/>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<input type="hidden" class="form-control  input-sm" id="txt_cantidad" name="txt_cantidad" value="0">
				<label class="label label_blue_sky"  for="sel_subfamilia">Subfamilia</label>
<?php 	$qry_subfamilia = $link->query("SELECT bh_subfamilia.AI_subfamilia_id, bh_subfamilia.TX_subfamilia_value, bh_familia.TX_familia_value
				FROM (bh_subfamilia
				INNER JOIN bh_familia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
				ORDER BY subfamilia_AI_familia_id ASC")or die($link->error); 		?>
				<select  class="form-control input-sm" id="sel_subfamilia" name="sel_subfamilia">
<?php 		$group = '';
					while($rs_subfamilia=$qry_subfamilia->fetch_array(MYSQLI_ASSOC)){
						if ($rs_subfamilia['TX_familia_value'] != $group) {
							echo "</optgroup><optgroup label=".$rs_subfamilia['TX_familia_value'].">";
							$group=$rs_subfamilia['TX_familia_value'];
							if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
								<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 				}else{			?>
								<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 				}
						}else{
							if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
								<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 				}else{			?>
								<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 				}
						}
					} 	?>
				</select>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<label class="label label_blue_sky" for="txt_codigo">C&oacute;digo:</label>
				<input type="text" class="form-control input-sm" id="txt_codigo" name="txt_codigo" value="">
			</div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-sm-label pt_14 ">
				<button type="button" class="btn btn-sm btn-success" onclick="generate_code();" ><i class="fa fa-file-text"></i></button>
			</div>			
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-danger display_none" id="container_product_recall">

			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<label class="label label_blue_sky" for="txt_impuesto">Impuesto:</label>
				<input type="text" class="form-control input-sm" id="txt_impuesto" name="txt_impuesto" value="<?php echo $rs_impuesto['impuesto'] ?>">
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<label class="label label_blue_sky" for="sel_medida">Medida:</label>
				<select  class="form-control input-sm" id="sel_medida" name="sel_medida">
<?php			do{ ?>
						<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
<?php			}while($rs_medida=$qry_medida->fetch_array());	?>
		    </select>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<label class="label label_blue_sky" for="sel_letter">Letra:</label>
				<select  class="form-control input-sm" id="sel_letter" name="sel_letter">
<?php			$percent = 0;
					while($rs_letra=$qry_letra->fetch_array()){	?>
						<option value="<?php echo $rs_letra['AI_letra_id']; ?>"><?php echo $rs_letra['TX_letra_value']." (".$rs_letra['TX_letra_porcentaje']."%)"; ?></option>
<?php			};	?>
	      </select>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky" for="txt_cantminima">Cantidad M&iacute;nima:</label>
				<input type="text" class="form-control input-sm" id="txt_cantminima" name="txt_cantminima" value="">
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky" for="txt_cantmaxima">Cantidad M&aacute;xima:</label>
				<input type="text" class="form-control input-sm" id="txt_cantmaxima" name="txt_cantmaxima" value="">
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<input type="hidden" class="form-control input-sm" id="txt_cantidad" name="txt_cantidad" value="0">
		</div>
    <div id="container_precio" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="container_precio4" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label class="label label_blue_sky" for="txt_p_4">Standard:</label>
			<input type="text" class="form-control input-sm" id="txt_p_4" name="txt_p_4" value="">
	    </div>
    	<div id="container_precio5" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label class="label label_blue_sky" for="txt_p_5">P. M&aacute;ximo:</label>
			<input type="text" class="form-control input-sm" id="txt_p_5" name="txt_p_5" value="">
	    </div>
    	<div id="container_precio3" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label class="label label_blue_sky" for="txt_p_3">Descuento #3:</label>
			<input type="text" class="form-control input-sm" id="txt_p_3" name="txt_p_3"	value="">
	    </div>
    	<div id="container_precio2" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label class="label label_blue_sky" for="txt_p_2">Descuento #2:</label>
			<input type="text" class="form-control input-sm" id="txt_p_2" name="txt_p_2"	value="">
	    </div>
			<div id="container_precio1" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label class="label label_blue_sky" for="txt_p_1">Descuento #1:</label>
			<input type="text" class="form-control input-sm" id="txt_p_1" name="txt_p_1"	value="">
	    </div>
    </div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" name="btn_save_product" id="btn_save_product" class="btn btn-success">Guardar</button>
		&nbsp;
    <button type="button" name="btn_cancel_product" id="btn_cancel_product" class="btn btn-warning">Cancelar</button>
  </div>
</div>


</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
