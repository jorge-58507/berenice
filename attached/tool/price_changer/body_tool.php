<?php
include '../../../bh_conexion.php';
$link=conexion();
include_once '../method_crud.php';
$crud_function = new method_crud(); $public_bd = new public_access_bd();

$name_tool = $crud_function->get_name_tool();

$json_contenido = $crud_function->read_json_tool($name_tool);
$raw_contenido = json_decode($json_contenido, true);

 $raw_medida = $public_bd->get_tbl_medida();
 $raw_producto = $public_bd->get_lista_producto(10);
 $raw_activo = ["ACTIVO","INACTIVO"];

// $qry_user = $link->query("SELECT AI_user_id,TX_user_seudonimo FROM bh_user")or die($link->error);
// $raw_user = array();
// while ($rs_user = $qry_user->fetch_array(MYSQLI_ASSOC)) {
// 	$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
// }

?>
<!--    #######################     JS             -->
<script type="text/javascript">
  var raw_group = [];
  var raw_newprice = {};
  var raw_medida = <?php echo json_encode($raw_medida);?>;
  function filter_product(str){
    $.ajax({data: {"a" : url_replace_regular_character(str) }, type: "GET", dataType: "text", url: "attached/tool/price_changer/filter_product.php",})
    .done(function( data, textStatus, jqXHR ) {
      $("#tbl_product tbody").html(data);
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }
  function add_product2group(producto_id, codigo, descripcion){
    var coheficiente = prompt("Ingrese la constante");
    if (coheficiente > 0) {
      coheficiente = val_intw4dec(coheficiente);
    }else{ return false; }
    group = {"producto_id" : producto_id, "codigo" : codigo, "producto_value" : descripcion, "coheficiente" : coheficiente };
    raw_group.push(group);
    print_group();
  }
  function del_product2group(key){
    var index = raw_group.indexOf(key);
    raw_group.splice(key,1);
    print_group();
  }
  function print_group(){
    var content = '';
    array_length = Object.keys(raw_group)
    if (array_length.length > 0) {
      for (var x in raw_group) {
        content += `
          <tr>
            <td>${raw_group[x]['codigo']}</td>
            <td>${replace_special_character(raw_group[x]['producto_value'])}</td>
            <td>${raw_group[x]['coheficiente']}</td>
            <td><button type="button" class="btn btn-danger btn-xs btn_squared_xs" onclick="del_product2group(${x})"><i class="fa fa-times"> </i></td>
          </tr>
        `;
      }
    }else{ content += `<tr><td colspan="4"></td></tr>`; }
    $("#tbl_group tbody").html(content);
  }
  function save_group(){
    if ($("#txt_title").val() === "") {
      set_bad_field("txt_title");
      return false;
    }else{ set_good_field("txt_title"); $("#txt_title").val($("#txt_title").val().toUpperCase()); }
    array_length = Object.keys(raw_group)
    if (array_length.length > 0) {
      var new_group = {};
      for (var x in raw_group) {
        new_group[raw_group[x]['producto_id']] = {"codigo" : raw_group[x]['codigo'], "producto_value" : raw_group[x]['producto_value'], "coheficiente" : raw_group[x]['coheficiente'] };
      }
      $.ajax({data: {"a" : new_group, "b" : $("#txt_title").val(), "z" : "save_group"}, type: "GET", dataType: "text", url: "attached/tool/price_changer/method_tool.php",})
      .done(function( data, textStatus, jqXHR ) {
        console.log("GOOD "+textStatus);
        raw_group =[];
        print_group();
      })
      .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
    }else{ console.log("esta vacio");}
  }

  function generate_tbl_vistaprevia(method){
    json_group = $("#sel_group option:selected").prop("label");
    raw_group = JSON.parse(json_group);
    p1 = ($("#txt_p1").val()>0) ? parseFloat($("#txt_p1").val()) : 0,
    p2 = ($("#txt_p2").val()>0) ? parseFloat($("#txt_p2").val()) : 0,
    p3 = ($("#txt_p3").val()>0) ? parseFloat($("#txt_p3").val()) : 0,
    p4 = ($("#txt_p4").val()>0) ? parseFloat($("#txt_p4").val()) : 0,
    p5 = ($("#txt_p5").val()>0) ? parseFloat($("#txt_p5").val()) : 0
    raw_price = { 1 : p1, 2 : p2, 3 : p3, 4 : p4, 5 : p5  }
    var content = '';
    // var raw_newprice = {};
    array_length = Object.keys(raw_group);
    if (array_length.length > 0) {
      for (var x in raw_group) {
        var product_id = x;
        raw_newprice[product_id] = '';
        var coheficiente = parseFloat(raw_group[x]['coheficiente']);
        raw_newprice[product_id] = {
            "medida" : $("#sel_medida").val(),
            "p1" : val_intw2dec(coheficiente*raw_price[1]),
            "p2" : val_intw2dec(coheficiente*raw_price[2]),
            "p3" : val_intw2dec(coheficiente*raw_price[3]),
            "p4" : val_intw2dec(coheficiente*raw_price[4]),
            "p5" : val_intw2dec(coheficiente*raw_price[5])
          }

      }
    }else{ return false;  }
    for (var y in raw_newprice) {
      content += `
      <tr>
        <td>${raw_group[y]['codigo']}</td>
        <td>${replace_special_character(raw_group[y]['producto_value'])}</td>
        <td>${raw_medida[raw_newprice[y]['medida']]}</td>
        <td>${raw_newprice[y]['p4']}</td>
        <td>${raw_newprice[y]['p5']}</td>
        <td>${raw_newprice[y]['p3']}</td>
        <td>${raw_newprice[y]['p2']}</td>
        <td>${raw_newprice[y]['p1']}</td>
      </tr>`;
    }
    $("#tbl_vistaprevia tbody").html(content);
  }
  function execute_pricechanger(){
    $.ajax({data: {"a" : raw_newprice }, type: "GET", dataType: "text", url: "attached/tool/price_changer/execute_pricechanger.php",})
    .done(function( data, textStatus, jqXHR ) {
      console.log("GOOD "+textStatus);
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
  }

</script>
<!--      #######################     CSS         -->
<!--    ########################      TABS      ######################### -->
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  <div id="container_tabs" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
  	<ul class="nav nav-tabs">
  	  <li class="active"><a data-toggle="tab" href="#group">Grupos</a></li>
  	  <li><a data-toggle="tab" href="#price">Precios</a></li>
  	</ul>
  </div>
  <div class="tab-content">
    <div id="group" class="col-xs-12 col-sm-12 col-md-12 col-lg-12  no_padding tab-pane fade in active">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding pt_7">
        <label for="txt_title" class="label label_blue_sky">T&iacute;tulo</label>
        <input type="text" id="txt_title" name="" placeholder="Escriba un titulo para este grupo" class="form-control" value="" autocomplete="off">
      </div>
      <div id="container_tbl_group" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <table id="tbl_group" class="table table-bordered table-hover table-condensed">
          <caption>Productos Agrupados</caption>
          <thead class="bg_green">
            <tr>
              <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
              <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
              <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Coheficiente</th>
              <th> </th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="4"></td></tr>
          </tbody>
          <tfoot class="bg_green"><tr><td colspan="4"></tr></tfoot>
        </table>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center pt_7">
        <button type="button" name="button" class="btn btn-success" onclick="save_group();">Guardar</button>
        &nbsp;&nbsp;
        <button type="button" name="button" class="btn btn-warning" onclick="window.location.reload();">Cancelar</button>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <label for="txt_filterproduct" class="label label_blue_sky">Buscar</label>
        <input type="text" id="txt_filterproduct" name="" placeholder="Codigo o Descripcion" onkeyup="filter_product(this.value);" class="form-control" value="" autocomplete="off">
      </div>
      <div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <table id="tbl_product" class="table table-bordered table-hover table-condensed">
          <caption>Lista de Productos</caption>
          <thead class="bg_red">
            <tr>
              <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
              <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
              <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Activo</th>
              <th> </th>
            </tr>
          </thead>
          <tbody>
  <?php       foreach ($raw_producto as $key => $rs_producto) {  ?>
              <tr>
                <td class="no_padding"><?php echo $rs_producto['TX_producto_codigo'] ?></td>
                <td class="no_padding"><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']) ?></td>
                <td class="no_padding"><?php echo $raw_activo[$rs_producto['TX_producto_activo']]; ?></td>
                <td class="no_padding"> <button type="button" name="button" class="btn btn-info btn-xs" onclick="add_product2group('<?php echo $rs_producto['AI_producto_id'] ?>','<?php echo $rs_producto['TX_producto_codigo'] ?>', '<?php echo str_replace("'", "&squote;",$rs_producto['TX_producto_value']) ?>')"><i class="fa fa-plus"> </i> Agregar</button> </td>
              </tr>
    <?php    }  ?>
          </tbody>
          <tfoot class="bg_red"><tr><td colspan="4"></tr></tfoot>
        </table>
      </div>
    </div>
    <div id="price" class="col-xs-12 col-sm-12 col-md-12 col-lg-12  no_padding tab-pane fade">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding pt_7">
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <label for="txt_p4" class="label label_blue_sky">P. Standard</label>
          <input type="text" id="txt_p4" name="" value="" placeholder="0.00" class="form-control">
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <label for="txt_p5" class="label label_blue_sky">Precio Max.</label>
          <input type="text" id="txt_p5" name="" value="" placeholder="0.00" class="form-control">
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <label for="txt_p3" class="label label_blue_sky">Descuento#3</label>
          <input type="text" id="txt_p3" name="" value="" placeholder="0.00" class="form-control">
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <label for="txt_p2" class="label label_blue_sky">Descuento#2</label>
          <input type="text" id="txt_p2" name="" value="" placeholder="0.00" class="form-control">
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <label for="txt_p1" class="label label_blue_sky">Descuento#1</label>
          <input type="text" id="txt_p1" name="" value="" placeholder="0.00" class="form-control">
        </div>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="sel_group" class="label label_blue_sky">Grupo</label>
        <select class="form-control" id="sel_group" name="sel_group">
          <option value=""label="">SELECCIONE</option>
    <?php foreach ($raw_contenido['GROUP'] as $group_name => $group) {
            echo "<option value=\"$group_name\" label='".json_encode($group)."'>$group_name</option>";
          }
     ?> </select>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="sel_group" class="label label_blue_sky">Medidas</label>
        <select class="form-control" id="sel_medida" name="sel_medida">
    <?php foreach ($raw_medida as $medida_id => $medida_value) {
            echo "<option value=\"$medida_id\">$medida_value</option>";
          }
     ?> </select>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center pt_7">
        <button type="button" class="btn btn-primary" name="button" onclick="generate_tbl_vistaprevia()">Vista Previa</button>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7" >
        <table class="table table-bordered table-condensed table-striped" id="tbl_vistaprevia">
          <thead class="bg-primary">
            <tr>
              <th>CODIGO</th>
              <th>DESCRIPCION</th>
              <th>MEDIDA</th>
              <th>P. STANDARD</th>
              <th>P. MAXIMO</th>
              <th>DESC. #3</th>
              <th>DESC. #2</th>
              <th>DESC. #1</th>
            </tr>
          </thead>
          <tfoot class="bg-primary"><tr><td colspan="8"></td></tr></tfoot>
          <tbody>
            <tr>
              <td colspan="8"> </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center  pt_7">
        <button type="button" class="btn btn-success" name="button" onclick="execute_pricechanger();"> Procesar</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-warning" name="button" onclick="window.location.reload();"> Cancelar</button>
      </div>
    </div>


  </div>
</div>
