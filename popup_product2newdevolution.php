<?php
require 'bh_conexion.php';
$link=conexion();
$datoventa_id=$_GET['a'];
$retired_quantity = $_GET['b'];

$qry_datoventa=$link->query("SELECT AI_datoventa_id, TX_datoventa_medida, datoventa_AI_producto_id FROM bh_datoventa WHERE AI_datoventa_id = '$datoventa_id'")or die($link->error);
$rs_datoventa=$qry_datoventa->fetch_array();

$qry_producto=$link->query("SELECT AI_producto_id,TX_producto_value FROM bh_producto WHERE AI_producto_id = '{$rs_datoventa['datoventa_AI_producto_id']}'")or die($link->error);
$rs_producto=$qry_producto->fetch_array(MYSQLI_ASSOC);

$qry_producto_medida=$link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.AI_rel_productomedida_id, rel_producto_medida.TX_rel_productomedida_cantidad FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$rs_datoventa['datoventa_AI_producto_id']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
}

$prep_rel_cantidad = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);
$prep_rel_cantidad->bind_param("ii", $rs_datoventa['datoventa_AI_producto_id'], $rs_datoventa['TX_datoventa_medida']); $prep_rel_cantidad->execute(); $qry_rel_cantidad = $prep_rel_cantidad->get_result();
$rs_rel_cantidad = $qry_rel_cantidad->fetch_array();

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
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

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#btn_acept').on("click",function(){
	if($("#txt_quantity").val() === '' || $("#txt_quantity").val() < 0.01){
		set_bad_field("txt_quantity");
		return false;
	}else{
		$("#txt_quantity").val(val_intw4dec($("#txt_quantity").val()))
	}
	var medida_cantidad = $("#sel_medida option:selected").attr("alt");
	var datoventa_medida = <?php echo $rs_rel_cantidad['TX_rel_productomedida_cantidad']; ?>;
	var cantidad = $("#txt_quantity").val() * medida_cantidad;
	var retirable = $("#span_retired").html() * datoventa_medida;
	console.log(`cantidad:${cantidad} retirable:${retirable}`);
	if(parseFloat(cantidad) > parseFloat(retirable)){	set_bad_field("txt_quantity"); return false;	}
	set_good_field("txt_quantity");
	$("#btn_anulate").attr("disabled", true);
	plus_return(<?php echo $datoventa_id; ?>,$("#txt_quantity").val(),$("#sel_medida").val());
})

$('#btn_cancel').click(function(){
	self.close();
})
$('#txt_quantity').validCampoFranz('.0123456789');
$("#txt_quantity").on("blur",function(){
	this.value = val_intw4dec(this.value);
});

$("#form_product2addcollect").keyup(function(e){
	if(e.which === 13){
		$('#btn_acept').focus();
		$('#btn_acept').click();
	}
});




});

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form id="form_product2addcollect" method="post" name="form_product2addcollect">
	<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<label class="label label_blue_sky" for="txt_product">Producto:</label>
    <input type="text" class="form-control" readonly="readonly" value="<?php echo $rs_producto['TX_producto_value'] ?>" />
	</div>
	<div id="container_measure_retired" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<label class="label label-danger" for="span_measure_retired">Medida Retirada:</label>
		<span id="span_measure_retired" class="form-control"><?php echo $raw_medida[$rs_datoventa['TX_datoventa_medida']] ?></span>
	</div>
	<div id="container_retired" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<label class="label label-danger" for="span_retired">Cantidad Retirada:</label>
	  <span id="span_retired" class="form-control"><?php echo $retired_quantity; ?></span>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label class="label label_blue_sky" class="label label_blue_sky"  for="sel_medida">Medida Devuelta:</label>
		<select class="form-control" id="sel_medida" name="sel_medida"><?php
			foreach ($raw_producto_medida as $key => $rs_medida) {
				if($rs_medida['AI_medida_id']===$rs_datoventa['TX_datoventa_medida']){
	?>			<option value="<?php echo $rs_medida['AI_medida_id']; ?>" alt="<?php echo $rs_medida['TX_rel_productomedida_cantidad']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
	<?php	}else{ 	?>
					<option value="<?php echo $rs_medida['AI_medida_id']; ?>" alt="<?php echo $rs_medida['TX_rel_productomedida_cantidad']; ?>"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
	<?php }
			}					?>
		</select>
	</div>
	<div id="container_quantity" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<label class="label label_blue_sky" for="txt_quantity">Cantidad Devuelta:</label>
	  <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" placeholder="1" autofocus />
	</div>
	<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
		&nbsp;
		<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
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
