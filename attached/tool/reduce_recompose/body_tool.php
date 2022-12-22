<?php
include '../../../bh_conexion.php';
$link=conexion();
include_once '../method_crud.php';
$crud_function = new method_crud(); $public_bd = new public_access_bd();

$name_tool = $crud_function->get_name_tool();

$json_contenido = $crud_function->read_json_tool($name_tool);
$raw_contenido = json_decode($json_contenido, true);
unset($raw_contenido['minus']);
unset($raw_contenido['plus']);
$json_contenido = json_encode($raw_contenido);
$crud_function->write_json_tool($name_tool,$json_contenido);

$qry_producto=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE TX_producto_activo = 0 ORDER BY TX_producto_value ASC LIMIT 5")or die($link->error);
$raw_producto = array();
while ($rs_producto=$qry_producto->fetch_array()) {
  $raw_producto[]=$rs_producto;
}

$qry_user = $link->query("SELECT AI_user_id,TX_user_seudonimo FROM bh_user")or die($link->error);
$raw_user = array();
while ($rs_user = $qry_user->fetch_array(MYSQLI_ASSOC)) {
	$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
}

?>
<!--    #######################     JS             -->
<script type="text/javascript">
  function filter_reduce_product(str,tabla){
    $.ajax({data: {"a" : url_replace_regular_character(str), "b" : tabla }, type: "GET", dataType: "text", url: "attached/tool/reduce_recompose/filter_reduce_product.php",})
    .done(function( data, textStatus, jqXHR ) {
      $("#tbl_product_"+tabla+" tbody").html(data);
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function add_minus_item(product_id){
    var cantidad = prompt("Ingrese la Cantidad");
    ans = val_intwdec(cantidad);
    if(!ans){ return false; }
    cantidad = val_intw2dec(cantidad);
    $.ajax({data: {"a" : product_id, "b" : cantidad, "z" : 'add_minus_item' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        generate_tbl_product('minus',data);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function add_plus_item(product_id){
    var cantidad = prompt("Ingrese la Cantidades");
    ans = val_intwdec(cantidad);
    if(!ans){ return false; }
    cantidad = val_intw2dec(cantidad);
    $.ajax({data: {"a" : product_id, "b" : cantidad, "z" : 'add_plus_item' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        generate_tbl_product('plus',data);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function del_minus_item(position){
    $.ajax({data: {"a" : position, "z" : 'del_minus_item' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        generate_tbl_product('minus',data);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function del_plus_item(position){
    $.ajax({data: {"a" : position, "z" : 'del_plus_item' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        generate_tbl_product('plus',data);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function generate_tbl_product(tabla,data){
    raw_data = JSON.parse(data);
    raw_content = raw_data[tabla];
    var content = '';
    array_length = Object.keys(raw_content);
    if (array_length.length > 0) {
      for (var x in raw_content) {
        content += `<tr><td>${replace_special_character(raw_content[x]['producto_value'])}</td><td>${raw_content[x]['cantidad']}</td><td><button type="button" class="btn btn-danger btn-xs" onclick="del_${tabla}_item(${x})"><b>X</b></button></td></tr>`;
      }
    }else{
      content += `<tr><td colspan="3"></td></tr>`;
    }
    $("#tbl_"+tabla+" tbody").html(content);
  }
  function save_reduce_recompose(){
    $.ajax({data: {"z" : 'save_reduce_recompose' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) {
      if (data) {
        $("#tbl_minus tbody, #tbl_plus tbody").html(`<tr><td colspan="3"></td></tr>`);
        raw_data = JSON.parse(data);
        var array_obj = Object.keys(raw_data['saved']);
        print_html('attached/tool/reduce_recompose/print_reduce_recompose.php?a='+[array_obj.length-1]);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function filter_rr(){
    var str = $("#txt_filter").val();
    $.ajax({data: {"a" : url_replace_regular_character(str.toUpperCase()), "z" : 'filter' }, type: "GET", dataType: "text", url: "attached/tool/<?php echo $name_tool; ?>/method_tool.php",})
    .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
      if (data) {
        raw_data = JSON.parse(data);
        tbody = '';
        for (var x in raw_data) {
          console.log(x);
          tbody += `<tr>
            <td>${convertir_formato_fecha(raw_data[x]['fecha'])}</td>
            <td>${replace_special_character(raw_data[x]['minus'][0]['descripcion'])}</td>
            <td>Ver Impresion</td>
            <td><button type="button" class="btn btn-info btn-xs" onclick="print_html('attached/tool/reduce_recompose/print_reduce_recompose.php?a=${x}')"><i class="fa fa-print"></i></button></td>
          </tr>`;
        }
        $("#tbl_rr tbody").html(tbody);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
</script>
<!--      #######################     CSS         -->
<style type="text/css">
  #tbl_product_minus > tbody, #tbl_product_plus > tbody{
    cursor: pointer;
  }
</style>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <!-- ######################        PRODUCTOS A RESTAR      ##################### -->
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <table id="tbl_minus" class="table table-bordered table-hover ">
      <caption>Productos a Restar</caption>
      <thead class="bg_red">
        <tr>
          <th class="col-xs-9 col-sm-9 col-md-9 col-lg-9">Descripcion</th>
          <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cant</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="3"></td></tr>
      </tbody>
      <tfoot class="bg_red"><tr><td colspan="3"></td></tr></tfoot>
    </table>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <label for="txt_filterproduct_minus" class="label label_blue_sky">Buscar</label>
      <input type="text" id="txt_filterproduct" name="" placeholder="Codigo o Descripcion" onkeyup="filter_reduce_product(this.value,'minus');" class="form-control" value="" autocomplete="off">
    </div>
    <div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <table id="tbl_product_minus" class="table table-bordered table-hover ">
        <caption>Lista de Productos</caption>
        <thead class="bg-danger">
          <tr>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
            <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
          </tr>
        </thead>
        <tbody>
<?php       foreach ($raw_producto as $key => $rs_producto) {  ?>
            <tr onclick="add_minus_item('<?php echo $rs_producto['AI_producto_id'] ?>');">
              <td class="no_padding"><?php echo $rs_producto['TX_producto_codigo'] ?></td>
              <td class="no_padding"><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']) ?></td>
              <td class="no_padding"><?php echo $rs_producto['TX_producto_cantidad']; ?></td>
            </tr>
  <?php    }  ?>
        </tbody>
        <tfoot class="bg-danger"><tr><td colspan="3"></tr></tfoot>
      </table>
    </div>
  </div>
  <!-- ######################        PRODUCTOS A SUMAR      ##################### -->

  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <table id="tbl_plus" class="table table-bordered table-hover ">
      <caption>Productos a Sumar</caption>
      <thead class="bg_green">
        <tr>
          <th class="col-xs-9 col-sm-9 col-md-9 col-lg-9">Descripcion</th>
          <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cantidad</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="3"></td></tr>
      </tbody>
      <tfoot class="bg_green"><tr><td colspan="3"></td></tr></tfoot>
    </table>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <label for="txt_filterproduct" class="label label_blue_sky">Buscar</label>
      <input type="text" id="txt_filterproduct" name="" placeholder="Codigo o Descripcion" onkeyup="filter_reduce_product(this.value,'plus');" class="form-control" value="">
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <table id="tbl_product_plus" class="table table-bordered table-hover ">
          <caption>Lista de Productos</caption>
          <thead class="bg-success">
            <tr>
              <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
              <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
              <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
            </tr>
          </thead>
          <tbody>
    <?php   foreach ($raw_producto as $key => $rs_producto) { ?>
              <tr onclick="add_plus_item('<?php echo $rs_producto['AI_producto_id']; ?>');">
                <td class="no_padding"><?php echo $rs_producto['TX_producto_codigo']; ?></td>
                <td class="no_padding"><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']); ?></td>
                <td class="no_padding"><?php echo $rs_producto['TX_producto_cantidad']; ?></td>
              </tr>
    <?php   } ?>
          </tbody>
          <tfoot class="bg-success"><tr><td colspan="3"></tr></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
  <button type="button" id="btn_save" class="btn btn-success btn-lg" name="button" onclick="save_reduce_recompose()">Ejecutar</button>
</div>
<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
  <label for="txt_filter" class="label label_blue_sky">Buscar</label>
  <input type="text" id="txt_filter" class="form-control" name="" value="" placeholder="Descripcion de Producto"  />
</div>
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md">
  <button type="button" class="btn btn-success btn-md" name="button" onclick="filter_rr()"><i class="fa fa-search"></i></button>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
  <table id="tbl_rr" class="table table-bordered table-hover ">
    <caption>Reducciones Realizadas</caption>
    <thead class="bg-success">
      <tr>
        <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
        <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Usuario</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php   foreach ($raw_contenido['saved'] as $key => $line_saved) {
          $raw_columna=["TX_producto_value"]; $raw_where = ["AI_producto_id" => $line_saved['minus'][0]['producto_id']];
          $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
  ?>
          <tr>
            <td class="no_padding"><?php echo date('d-m-Y',strtotime($line_saved['fecha'])); ?></td>
            <td class="no_padding"><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']); ?></td>
            <td class="no_padding"><?php echo $raw_user[$line_saved['user_id']]; ?></td>
            <td class="al_center"><button type="button" class="btn btn-info btn-xs" onclick="print_html('attached/tool/reduce_recompose/print_reduce_recompose.php?a=<?php echo $key; ?>')"><i class="fa fa-print"></i></button></td>
          </tr>
<?php   } ?>
    </tbody>
    <tfoot class="bg-success"><tr><td colspan="4"></tr></tfoot>
  </table>
</div>
