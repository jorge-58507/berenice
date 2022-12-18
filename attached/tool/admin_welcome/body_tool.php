<?php
include '../../../bh_conexion.php';
$link=conexion();
include_once '../method_crud.php';
$crud_function = new method_crud(); $public_bd = new public_access_bd();

$name_tool = $crud_function->get_name_tool();


$raw_producto = $public_bd->get_lista_producto(10);
$raw_medida = $public_bd->get_tbl_medida();

$qry_user = $link->query("SELECT AI_user_id,TX_user_seudonimo FROM bh_user")or die($link->error);
$raw_user = array();
while ($rs_user = $qry_user->fetch_array(MYSQLI_ASSOC)) {
	$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
}

$qry_promocion = $link->query("SELECT AI_promocion_id, TX_promocion_componente, TX_promocion_tipo, TX_promocion_titulo, TX_promocion_descripcion, TX_promocion_fecha FROM bh_promocion")or die($link->error);
$raw_promocion = array();
while ($rs_promocion = $qry_promocion->fetch_array(MYSQLI_ASSOC)) {
  if ($rs_promocion['TX_promocion_tipo'] != 4) {
    $raw_promocion[] = $rs_promocion;
  }
}


?>
<!--    #######################     JS             -->
<script type="text/javascript">
  function filter_product(str){
    $.ajax({data: {"a" : url_replace_regular_character(str)}, type: "GET", dataType: "text", url: "attached/tool/make_promotion/filter_product.php",})
    .done(function( data, textStatus, jqXHR ) {
      $("#tbl_product tbody").html(data);
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function get_producto_medida(product_id){
    $.ajax({data: {"a" : product_id, "z" : 'get_producto_medida' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        $("#mod_add_promotion_product").html(data);
        $("#mod_add_promotion_product").show("200");
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  // ##############################   OPERANTES ARRAY_PROMOTION    ######################
  var array_promotion = new Object();
  function save_promotion(){
    var array_length = Object.keys(array_promotion);
    if (array_length.length < 1) {  return false; }
    for (var x in array_promotion) {
      delete array_promotion[x]['producto_value'];
      delete array_promotion[x]['medida_value'];
    }
    if($("#txt_promotion_title").val() === ''){ set_bad_field('txt_promotion_title'); $("#txt_promotion_title").focus(); return false; }
    if($("#txt_promotion_description").val() === '') { set_bad_field('txt_promotion_description'); $("#txt_promotion_description").focus(); return false; }
    $.ajax({data: {"a" : JSON.stringify(array_promotion), "b" : $("#txt_promotion_title").val(), "c" : $("#ta_promotion_description").val(), "d" : $("#sel_promotion_type").val(),  "z" : 'save_promotion' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        var array_promotion = new Object();
        $("#txt_promotion_title").val('');
        $("#ta_promotion_description").val('');
        $("#tbl_promotion tbody").html(`<tr><td colspan="7"></td></tr>`);
        raw_data = JSON.parse(data);
        content = ''
        for (var x in raw_data) {
          content += `<tr><td>${raw_data[x]['TX_promocion_fecha']}</td><td>${raw_data[x]['TX_promocion_titulo']}</td><td>${raw_data[x]['TX_promocion_descripcion']}</td><td><button type="button" class="btn btn-danger btn-xs btn_squared_xs" onclick="del_promotion(${raw_data[x]['AI_promocion_id']})"><i class="fa fa-times"></i></button></td></tr>`;
        }
        $("#tbl_promotion_exist tbody").html(content);

      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
	function save_motd () {
		var motd = document.getElementById('txt_motdvalue').value;
		data = {"a":motd}
		$.ajax({data: {"a" : motd, "z" : 'save_motd' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {	$("#txt_motdvalue").val('');	}
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	}
	function save_phrase () {
		var phrase = document.getElementById('txt_phrasevalue').value;
		data = {"a":phrase}
		$.ajax({data: {"a" : phrase, "z" : 'save_phrase' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {	$("#txt_phrasevalue").val('');	}
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	}
  $('#txt_motdvalue, #txt_phrasevalue').validCampoFranz('1234567890.abcdefghijklmnopqrstuvwxyz ,.;-%');

</script>
<!--      #######################     CSS         -->
<!--      #######################     CSS         -->
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#motd">Mensaje del D&iacute;a</a></li>
			<li><a data-toggle="tab" href="#phrase">Frases</a></li>
		</ul>
		<div class="tab-content">
	    <div id="motd" class="tab-pane fade in active">
				<div id="container_motdvalue" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_7">
					<label for="txt_motdvalue" class="label label_blue_sky">Mensaje del dia</label>
					<textarea name="txt_motdvalue" id="txt_motdvalue" class="form-control" rows="8" cols="80" onkeypress="verify_limit(event,this,70)"></textarea>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7 al_center">
					<button type="button" id="btn_motd_save" class="btn btn-success" onclick="save_motd();"><i class="fa fa-plus" aria-hidden="true"></i> Guardar</button>
				</div>
			</div>
			<div id="phrase" class="tab-pane fade">
				<div id="container_phrasevalue" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_7">
					<label for="txt_phrasevalue" class="label label_blue_sky">Frase de Bienvenida</label>
					<textarea name="txt_phrasevalue" id="txt_phrasevalue" class="form-control" rows="8" cols="80" onkeypress="verify_limit(event,this,84)"></textarea>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7 al_center">
					<button type="button" id="btn_phrase_save" class="btn btn-success" onclick="save_phrase()"><i class="fa fa-plus" aria-hidden="true"></i> Guardar</button>
				</div>
			</div>
		</div>
	</div>
</div>
