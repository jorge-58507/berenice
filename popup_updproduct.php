<?php
require 'bh_con.php';
$link=conexion();
session_start();
?>
<?php
$product_id=$_GET['a'];

$qry_product=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'");
$rs_product=mysql_fetch_assoc($qry_product);

$qry_medida=mysql_query("SELECT * FROM bh_medida", $link);
$rs_medida=mysql_fetch_assoc($qry_medida);

$qry_precio=mysql_query("SELECT * FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC", $link);
$rs_precio=mysql_fetch_assoc($qry_precio);

$qry_letra=mysql_query("SELECT bh_letra.AI_letra_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM bh_letra");

$qry_datocompra=mysql_query("SELECT TX_datocompra_precio,TX_datocompra_impuesto,TX_datocompra_descuento, bh_datocompra.AI_datocompra_id FROM bh_datocompra WHERE datocompra_AI_producto_id = '$product_id' ORDER BY AI_datocompra_id DESC");
$rs_datocompra=mysql_fetch_array($qry_datocompra);
$descuento = ($rs_datocompra['TX_datocompra_descuento']*$rs_datocompra['TX_datocompra_precio'])/100;
$precio_descuento = $rs_datocompra['TX_datocompra_precio']-$descuento;
$impuesto = ($rs_datocompra['TX_datocompra_impuesto']*$precio_descuento)/100;
$last_price = $precio_descuento+$impuesto;

$qry_precio_listado = mysql_query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_producto.AI_producto_id FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$product_id' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die(mysql_error());

$qry_datocompra_listado = mysql_query("SELECT bh_facturacompra.TX_facturacompra_fecha,bh_datocompra.TX_datocompra_precio,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento FROM ((bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_producto.AI_producto_id = '$product_id' ORDER BY TX_facturacompra_fecha DESC")or die(mysql_error());
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
	window.resizeTo("1010", "654");
	$('#btn_cancel_product').click(function(){
		self.close();
	});

	$("#txt_precio1, #txt_precio2, #txt_precio3, #txt_precio4, #txt_precio5").on("blur",function(){
		this.value = val_intw2dec(this.value);
	})


	$('#btn_save_product').click(function(){
		arr_price_field=[];
		arr_price_field=['txt_precio1','txt_precio2','txt_precio3','txt_precio4','txt_precio5'];
		for(i=0;i<arr_price_field.length;i++){
			if($("#"+arr_price_field[i]+"").val() != ""){
				var ans = val_intwdec($("#"+arr_price_field[i]+"").val());
				if(!ans){
					return false;
				}
			}
		}
		ans = confirm("¿Seguro desea guardar la información?");
		if(!ans){ return false; }
		upd_product(<?php echo $product_id; ?>);
	});

	$("#txt_nombre").on("keyup", function(){
		$("#txt_nombre").val(this.value.toUpperCase());
	});
	$("#txt_reference").on("keyup", function(){
		$("#txt_reference").val(this.value.toUpperCase());
	});
	$("#txt_impuesto").on("click", function(){
		if($(this).attr("readonly")){
			var ans=confirm("¿Desea agregar los impuestos manualmente?");
			if(!ans){
				window.location.href="popup_modify_tax.php?a=<?php echo $product_id; ?>";
			}else{
				$(this).attr("readOnly",false);
			}
		}

	});

	$("#btn_discount").on("click",function(){
		var discount = prompt("Indique el Porcentaje a Deducir                  (Este aplicara a todos los precios)");
		ans = val_intwdec(discount);
		if (!ans) {
			return false;
		}
		$.ajax({	data: {"a" : discount, "b" : <?php echo $_GET['a']; ?>},	type: "GET",	dataType: "text",	url: "attached/get/upd_product_discount.php",	})
		.done(function( data, textStatus, jqXHR ) {
			console.log("GOOD " + data);
			$("#btn_discount").text(data)
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })

	})
	$("#txt_precio4").focus();
	$("#txt_precio4").on("keyup", function(e){
		if (e.which === 13) {
			$("#txt_precio4").blur();
			setTimeout(function(){ $("#btn_save_product").click(); }, 250);
		}
	})
	$('#txt_cantidad').validCampoFranz('.0123456789');
	$('#txt_cantminima').validCampoFranz('0123456789');
	$('#txt_cantmaxima').validCampoFranz('0123456789');
	$('#txt_impuesto').validCampoFranz('0123456789');
	$('#txt_reference').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz/- ")
	$('#txt_nombre').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyzº'#/-; ");
	$('#txt_codigo').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz");
	$('#txt_precio1').validCampoFranz('.0123456789');
	$('#txt_precio2').validCampoFranz('.0123456789');
	$('#txt_precio3').validCampoFranz('.0123456789');
	$('#txt_precio4').validCampoFranz('.0123456789');
	$('#txt_precio5').validCampoFranz('.0123456789');


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
<div id="container_upd_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6" >

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <label for="txt_nombre">Nombre:</label>
      <input type="text" class="form-control input-sm" id="txt_nombre" name="txt_nombre" title="<?php echo $rs_product['TX_producto_value']; ?>" value="<?php echo $rs_product['TX_producto_value']; ?>">
      	</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="txt_codigo">Codigo:</label>
      <input type="text" class="form-control input-sm" id="txt_codigo" name="txt_codigo" readonly="readonly" value="<?php echo $rs_product['TX_producto_codigo']; ?>">
      	</div>
		<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
      <label for="txt_cantidad">Cantidad:</label>
      <input type="text" class="form-control input-sm" id="txt_cantidad" name="txt_cantidad" value="<?php echo $rs_product['TX_producto_cantidad']; ?>">
      	</div>
		<div id="container_alarm" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label for="r_alarm">Alarma:</label>
      <?php if($rs_product['TX_producto_alarma'] == '0'){
	  $checked_0="checked='checked'";
	  $checked_1="";
	  }else{
	  $checked_0="";
	  $checked_1="checked='checked'";
	  } ?>
		<label for="r_alarm_0" class="radio"><input type="radio" name="r_alarm" value="0" <?php echo $checked_0 ?> /> Si</label>
		<label for="r_alarm_0" class="radio"><input type="radio" name="r_alarm" value="1" <?php echo $checked_1 ?> /> No</label>
      	</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="sel_medida">Referencia:</label>
		<input type="text" id="txt_reference" name="txt_reference" class="form-control input-sm" value="<?php echo $rs_product['TX_producto_referencia']; ?>"/>
      	</div>
		<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
      <label for="sel_medida">Impuesto:</label>
      <input type="text" class="form-control input-sm" id="txt_impuesto" name="txt_impuesto" value="<?php echo $rs_product['TX_producto_exento']; ?>" readonly="readonly">
      	</div>
      <?php if($rs_product['TX_producto_activo'] == '0'){
	  $checked_0="checked='checked'";
	  $checked_1="";
	  $color = "#333";
	  }else{
	  $checked_0="";
	  $checked_1="checked='checked'";
	  $color = "#F00";
	  } ?>
		<div id="container_activo" style="color:<?php echo $color; ?>;" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 container_radio">
      <label for="r_alarm">ACTIVO:</label>
			<label for="r_activo_0" class="radio"><input type="radio" name="r_active" value="0" <?php echo $checked_0 ?> /> Si</label>
			<label for="r_activo_0" class="radio"><input type="radio" name="r_active" value="1" <?php echo $checked_1 ?> /> No</label>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="sel_medida">Medida:</label>
			<select  class="form-control input-sm" id="sel_medida" name="sel_medida">
<?php
		do{
			if($rs_medida['TX_medida_value']==$rs_product['TX_producto_medida']){
?>
<option value="<?php echo $rs_medida['TX_medida_value']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']; ?></option>
<?php	}	?>
<option value="<?php echo $rs_medida['TX_medida_value']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
<?php
		}while($rs_medida=mysql_fetch_assoc($qry_medida));
?>    </select>
      	</div>
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		<label for="sel_letter">Letra:</label>
		<select  class="form-control input-sm" id="sel_letter" name="sel_letter">
<?php
       	$percent = 0;
		while($rs_letra=mysql_fetch_assoc($qry_letra)){
		if($rs_letra['AI_letra_id']==$rs_product['producto_AI_letra_id']){
		$percent = $rs_letra['TX_letra_porcentaje'];
?>
<option value="<?php echo $rs_letra['AI_letra_id']; ?>" selected="selected"><?php echo $rs_letra['TX_letra_value']; ?></option>
<?php	} ?>
<option value="<?php echo $rs_letra['AI_letra_id']; ?>"><?php echo $rs_letra['TX_letra_value']; ?></option>
<?php
		};
?>      </select>
      	</div>
        <?php $alicuota = ($percent * $last_price)/100; $price = $last_price+$alicuota; ?>
		<div id="container_spanpsugerido" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <label for="span_p_sugerido">P. Sugerido</label>
        <span id="span_p_sugerido" class="form-control bg-warning input-sm"><?php echo number_format($price,2); ?></span>
        </div>

		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="txt_cantminima">Cantidad M&iacute;nima:</label>
      <input type="text" class="form-control input-sm" id="txt_cantminima" name="txt_cantminima" value="<?php echo $rs_product['TX_producto_minimo']; ?>">
      	</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="txt_cantmaxima">Cantidad M&aacute;xima:</label>
      <input type="text" class="form-control input-sm" id="txt_cantmaxima" name="txt_cantmaxima" value="<?php echo $rs_product['TX_producto_maximo']; ?>">
      	</div>
    </div>
    <div id="container_precio" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="container_precio4" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label for="txt_precio4">Standard:</label>
			<input type="text" class="form-control input-sm" id="txt_precio4" name="txt_precio4"
			value="<?php echo $rs_precio['TX_precio_cuatro']; ?>">
	    </div>
    	<div id="container_precio5" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label for="txt_precio5">P. M&aacute;ximo:</label>
			<input type="text" class="form-control input-sm" id="txt_precio5" name="txt_precio5"
			value="<?php echo $rs_precio['TX_precio_cinco']; ?>">
	    </div>
    	<div id="container_precio3" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label for="txt_precio3">Descuento #3:</label>
			<input type="text" class="form-control input-sm" id="txt_precio3" name="txt_precio3"
			value="<?php echo $rs_precio['TX_precio_tres']; ?>">
	    </div>
    	<div id="container_precio2" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     		<label for="txt_precio2">Descuento #2:</label>
			<input type="text" class="form-control input-sm" id="txt_precio2" name="txt_precio2"
			value="<?php echo $rs_precio['TX_precio_dos']; ?>">
	    </div>
			<div id="container_precio1" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label for="txt_precio1">Descuento #1:</label>
			<input type="text" class="form-control input-sm" id="txt_precio1" name="txt_precio1"
			value="<?php echo $rs_precio['TX_precio_uno']; ?>">
	    </div>
			<div id="container_btnpercent" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label for="btn_percent">Descuento %:</label>
			<?php if($_COOKIE['coo_tuser'] == 1 || $_COOKIE['coo_tuser'] == 2 || isset($_SESSION['admin'])){ ?>
				<button type="button" id="btn_discount" class="btn btn-default form-control input-sm">
					<?php echo $rs_product['TX_producto_descuento']; ?>
				</button>
			<?php } else{	?>
				<button type="button" id="" class="btn btn-default form-control input-sm" disabled="disabled">
					<?php echo $rs_product['TX_producto_descuento']; ?>
				</button>
			<?php	}	?>
	    </div>
    </div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" name="btn_save_product" id="btn_save_product" class="btn btn-success">Guardar</button>
		&nbsp;
    <button type="button" name="btn_cancel_product" id="btn_cancel_product" class="btn btn-warning">Cancelar</button>
  </div>
</div>
<!-- ####################  COMPRAS #############-->
<div id="container_tbl_purchase_price" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<table id="tbl_purchase_price" class="table table-bordered table-condensed table-striped">
		<caption class="caption">Historial de Precios de Compras</caption>
		<thead class="bg-primary">
		<tr>
			<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Imp.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Desc.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
		</tr>
		</thead>
		<tfoot class="bg-primary">
		<tr>
			<td colspan="5"></td>
		</tr>
		</tfoot>
		<tbody>
<?php
		while ($rs_datocompra_listado = mysql_fetch_array($qry_datocompra_listado)) {
			$descuento = ($rs_datocompra_listado['TX_datocompra_descuento']*$rs_datocompra_listado['TX_datocompra_precio'])/100;
			$precio_descuento = $rs_datocompra_listado['TX_datocompra_precio']-$descuento;
			$impuesto = ($rs_datocompra_listado['TX_datocompra_impuesto']*$precio_descuento)/100;
			$total_precio = $precio_descuento + $impuesto;
?>
			<tr>
				<td><?php echo date('d-m-Y', strtotime($rs_datocompra_listado['TX_facturacompra_fecha'])); ?></td>
				<td>B/ <?php echo $rs_datocompra_listado['TX_datocompra_precio']; ?></td>
				<td><?php echo $rs_datocompra_listado['TX_datocompra_impuesto']; ?>%</td>
				<td><?php echo $rs_datocompra_listado['TX_datocompra_descuento']; ?>%</td>
				<td>B/ <?php echo number_format($total_precio,2); ?></td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
</div>

<div id="container_tbl_historical_price" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<table id="tbl_historical_price" class="table table-bordered table-condensed table-striped">
		<caption>Historial de Precios de Venta</caption>
		<thead class="bg_green">
		<tr>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Reg.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Max.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #3</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #2</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #1</th>
		</tr>
		</thead>
		<tfoot class="bg_green">
			<tr>
				<td colspan="6"></td>
			</tr>
		</tfoot>
		<tbody>
<?php
		while ($rs_precio_listado = mysql_fetch_array($qry_precio_listado)) {
?>
	<tr>
		<td><?php echo date('d-m-Y', strtotime($rs_precio_listado['TX_precio_fecha'])); ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_cuatro'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cuatro'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_cinco'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cinco'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_tres'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_tres'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_dos'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_dos'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_uno'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_uno'],2); } ?></td>
	</tr>
<?php
		}
?>
		<tr>

		</tr>
		</tbody>
	</table>
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
