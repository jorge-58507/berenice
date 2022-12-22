<?php
include '../../../bh_conexion.php';
$link=conexion();
include_once '../method_crud.php';
$crud_function = new method_crud(); $public_bd = new public_access_bd();

$name_tool = $crud_function->get_name_tool();

// $json_contenido = $crud_function->read_json_tool($name_tool);
// $raw_contenido = json_decode($json_contenido, true);
// unset($raw_contenido['minus']);
// unset($raw_contenido['plus']);
// $json_contenido = json_encode($raw_contenido);
// $crud_function->write_json_tool($name_tool,$json_contenido);

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
  function add_promotion_product(producto_id){
    if($("#txt_promotion_cantidad").val() === '' || $("#txt_promotion_precio").val() === '' || $("#txt_promotion_impuesto").val() === '' || $("#txt_promotion_descuento").val() === ''){ return false; }
    if (!val_intwdec($("#txt_promotion_cantidad").val()) || !val_intwdec($("#txt_promotion_precio").val()) || !val_intwdec($("#txt_promotion_impuesto").val()) || !val_intwdec($("#txt_promotion_descuento").val())) { return false; }
    array_promotion[producto_id]={}
    array_promotion[producto_id]["medida"]=$("#sel_promotion_medida").val();
    array_promotion[producto_id]["cantidad"]=val_intw2dec($("#txt_promotion_cantidad").val());
    array_promotion[producto_id]["precio"]=val_intw2dec($("#txt_promotion_precio").val());
    array_promotion[producto_id]["impuesto"]=val_intw2dec($("#txt_promotion_impuesto").val());
    array_promotion[producto_id]["descuento"]=val_intw2dec($("#txt_promotion_descuento").val());
    array_promotion[producto_id]["producto_value"]=$("#span_descripcion").html();
    array_promotion[producto_id]["medida_value"]=$("#sel_promotion_medida option:selected").text();
    $("#mod_add_promotion_product").hide("200");
    generate_tbl_promotion(array_promotion);
  }
  function close_modal(){
    $("#mod_add_promotion_product").hide("200")
  }
  function del_promotion_product(producto_id){
    delete array_promotion[producto_id];
    generate_tbl_promotion(array_promotion);
  }
  function generate_tbl_promotion(raw_content){
    var content = '';
    array_length = Object.keys(raw_content);
    if (array_length.length > 0) {
      for (var x in raw_content) {
         content += `<tr><td>${raw_content[x]['producto_value']}</td><td>${raw_content[x]['medida_value']}</td><td class="al_center">${raw_content[x]['cantidad']}</td><td class="al_center">${raw_content[x]['precio']}</td><td class="al_center">${raw_content[x]['descuento']}</td><td class="al_center">${raw_content[x]['impuesto']}</td><td><button type="button" class="btn btn-danger btn-xs btn_squared_xs" onclick="del_promotion_product(${x})"><i class="fa fa-times"></i></button></td></tr>`;
      }
    }else{
      content += `<tr><td colspan="7"></td></tr>`;
    }
    $("#tbl_promotion tbody").html(content);
  }
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
  // ###########################       MODIFICAR PROMOCIONES ANTIGUAS    ################
  function get_promotion(promotion_id){
    $.ajax({data: {"a" : promotion_id, "z" : 'get_promotion' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        var array_promotion = JSON.parse(data);
        generate_tbl_promotion(array_promotion);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function del_promotion(promotion_id){
    $.ajax({data: {"a" : promotion_id, "z" : 'del_promotion' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
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
  $('#txt_promotion_title, #ta_promotion_description').validCampoFranz('1234567890.abcdefghijklmnopqrstuvwxyz -%/º');
</script>
<!--      #######################     CSS         -->
<style type="text/css">
  #tbl_product > tbody{
    cursor: pointer;
  }
  #mod_add_promotion_product{
    border: solid 1px #ccc;
    border-radius: 10px;
    position: fixed;
    top: 100px;
    z-index: 1;
    background-color: #ffffff;
    padding: 15px;
    box-shadow: 0px 2px 300px 85px #aaa4a4;
  }
  #mod_add_promotion_product div{
    margin: 3px 0;
  }
  #container_information div{
    margin: 3px 0;
  }
  #container_information{
    border: solid 1px #ccc;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    padding: 5px 0;
  }
  #container_promotion_product{
    border: solid 1px #ccc;
		border-top: none;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
  }
  #container_promotion_product div{
    margin: 5px 0;
  }
  #ta_promotion_description{
    height: 54px;
  }
  #tbl_promotion_exist tbody{
    cursor: pointer;
  }

</style>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <!-- #####################     MODAL WINDOWS add_promotion_product   ######################### -->
  <div id="mod_add_promotion_product" class="col-xs-5 col-sm-5 col-md-5 col-lg-5 display_none">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <span></span>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    </div>
  </div>
  <div id="container_information" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="txt_promotion_title" class="label label_blue_sky">T&iacute;tulo</label>
      <input type="text" id="txt_promotion_title" class="form-control" value="" />
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <label for="txt_promotion_title" class="label label_blue_sky">Tipo</label>
      <select id="sel_promotion_type" class="form-control">
        <option value="1">Descuento</option>
        <option value="2">Conjugacion</option>
        <option value="3">Cantidad</option>
      </select>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <label for="txt_promotion_title" class="label label_blue_sky">Descripci&oacute;n</label>
      <textarea class="form-control" id="ta_promotion_description"></textarea>
    </div>
  </div>
  <!-- ######################        PRODUCTOS A RESTAR       ##################### -->
  <div id="container_promotion_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_promotion" class="table table-bordered table-hover table-condensed">
      <caption>Promocion</caption>
      <thead class="bg_red">
        <tr>
          <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Descripcion</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Medida</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc %</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp %</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="7"></td></tr>
      </tbody>
      <tfoot class="bg_red"><tr><td colspan="7"></td></tr></tfoot>
    </table>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <label for="txt_filterproduct" class="label label_blue_sky">Buscar</label>
      <input type="text" id="txt_filterproduct" name="" placeholder="Codigo o Descripcion" onkeyup="filter_product(this.value);" class="form-control" value="" autocomplete="off">
    </div>
    <div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <table id="tbl_product" class="table table-bordered table-hover ">
        <caption>Lista de Productos</caption>
        <thead class="bg-danger">
          <tr>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
            <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
						<th></th>
          </tr>
        </thead>
        <tbody>
<?php     foreach ($raw_producto as $key => $rs_producto) {  ?>
            <tr>
              <td class="no_padding" onclick="get_producto_medida(<?php echo $rs_producto['AI_producto_id'] ?>);"><?php echo $rs_producto['TX_producto_codigo'] ?></td>
              <td class="no_padding" onclick="get_producto_medida(<?php echo $rs_producto['AI_producto_id'] ?>);"><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']) ?></td>
              <td class="no_padding" onclick="get_producto_medida(<?php echo $rs_producto['AI_producto_id'] ?>);"><?php echo $rs_producto['TX_producto_cantidad']; ?></td>
							<td><button type="button" class="btn btn-warning btn-xs btn_squared_xs" onclick="open_popup('popup_updproduct.php?a=<?php echo $rs_producto['AI_producto_id'] ?>','_popup','1010','580');"><i class="fa fa-wrench"></i></td>
            </tr>
  <?php    }  ?>
        </tbody>
        <tfoot class="bg-danger"><tr><td colspan="4"></tr></tfoot>
      </table>
    </div>
  </div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
  <button type="button" id="btn_save" class="btn btn-success btn-lg" name="button" onclick="save_promotion()">Guardar</button>
  <table id="tbl_promotion_exist" class="table table-bordered table-hover ">
    <caption>Promociones Existentes</caption>
    <thead class="bg-success">
      <tr>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Titulo</th>
        <th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Descripcion</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php   foreach ($raw_promocion as $key => $value) {
?>
          <tr>
            <td class="no_padding"><?php echo date('d-m-Y',strtotime($value['TX_promocion_fecha'])); ?></td>
            <td class="no_padding" onclick="get_promotion(<?php echo $value['AI_promocion_id']; ?>)"><?php echo $value['TX_promocion_titulo']; ?></td>
            <td class="no_padding"><?php echo $value['TX_promocion_descripcion']; ?></td>
            <td class="al_center"><button type="button" class="btn btn-danger btn-xs btn_squared_xs" onclick="del_promotion(<?php echo $value['AI_promocion_id']; ?>)"><i class="fa fa-times"></i></button></td>
          </tr>
<?php   } ?>
    </tbody>
    <tfoot class="bg-success"><tr><td colspan="4"></tr></tfoot>
  </table>
</div>
