<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$facturacompra_id=$_GET['a'];

$qry_facturacompra = $link->query("SELECT bh_facturacompra.AI_facturacompra_id,
  bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_facturacompra.TX_facturacompra_elaboracion,
  bh_facturacompra.TX_facturacompra_almacen, bh_facturacompra.TX_facturacompra_ordendecompra,
  bh_facturacompra.TX_facturacompra_observacion, bh_proveedor.TX_proveedor_nombre, bh_almacen.TX_almacen_value,
  bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_datocompra.TX_datocompra_precio,
  bh_datocompra.TX_datocompra_cantidad,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento,
  bh_datocompra.TX_datocompra_medida,bh_datocompra.AI_datocompra_id
  FROM (((((bh_facturacompra
  INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id)
  INNER JOIN bh_datocompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id)
  INNER JOIN bh_almacen ON bh_almacen.AI_almacen_id = bh_facturacompra.TX_facturacompra_almacen)
  INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id)
  INNER JOIN bh_medida ON bh_medida.AI_medida_id = bh_datocompra.TX_datocompra_medida)
  WHERE bh_facturacompra.AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
  $raw_facturacompra = array();
while($rs_facturacompra = $qry_facturacompra->fetch_array(MYSQLI_ASSOC)){
  $raw_facturacompra[]=$rs_facturacompra;
};

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$qry_compradevolucion=$link->query("SELECT bh_datocompradevolucion.datocompradevolucion_AI_datocompra_id, bh_datocompradevolucion.TX_datocompradevolucion_cantidad, bh_datocompradevolucion.datocompradevolucion_AI_medida_id
  FROM ((bh_datocompradevolucion
  INNER JOIN bh_compradevolucion ON bh_compradevolucion.AI_compradevolucion_id = bh_datocompradevolucion.datocompradevolucion_AI_compradevolucion_id)
  INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_compradevolucion.compradevolucion_AI_facturacompra_id)
  WHERE bh_facturacompra.AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
$raw_compradevolucion = array();

$prep_datocompra = $link->prepare("SELECT * FROM bh_datocompra WHERE AI_datocompra_id = ?")or die($link->error);
$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

while($rs_compradevolucion = $qry_compradevolucion->fetch_array(MYSQLI_ASSOC)){
  $datocompra_id = $rs_compradevolucion['datocompradevolucion_AI_datocompra_id'];
  $prep_datocompra->bind_param('i',$datocompra_id); $prep_datocompra->execute(); $qry_datocompra=$prep_datocompra->get_result();
  $rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);

  $prep_producto_medida->bind_param('ii',$producto_id,$medida);
  $producto_id = $rs_datocompra['datocompra_AI_producto_id'];

  $medida = $rs_datocompra['TX_datocompra_medida'];
  $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
  $rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
  $compra_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

  $medida = $rs_compradevolucion['datocompradevolucion_AI_medida_id'];
  $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
  $rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
  $devolucion_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

  $factor = $devolucion_qtymedida/$compra_qtymedida;

  if (empty($raw_compradevolucion[$rs_compradevolucion['datocompradevolucion_AI_datocompra_id']])) {
    $raw_compradevolucion[$rs_compradevolucion['datocompradevolucion_AI_datocompra_id']]=0;
  }
  $raw_compradevolucion[$rs_compradevolucion['datocompradevolucion_AI_datocompra_id']] += $rs_compradevolucion['TX_datocompradevolucion_cantidad']*$factor;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<?php include 'attached/php/req_required.php'; ?>
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="attached/js/admin_funct.js"></script>

<script type="text/javascript">
$(document).ready(function() {
$("#txt_motivonc").validCampoFranz("0123456789 abcdefghijklmnopqrstuvwxyz.,");
$("#txt_motivonc").blur(function(){ this.value = this.value.toUpperCase();  });

$("#btn_save").click(function(){
	if(parseFloat($("#td_total").html()) < 0.01 || isNaN(parseFloat($("#td_total").html())) ){ return false;	}
	if($("#txt_motivonc").val() === "" ){
		set_bad_field("txt_motivonc");
		$("#txt_motivonc").focus();
		return false;
	}  set_good_field("txt_motivonc");
    $("#btn_save").attr("disabled", true);
    plus_compradevolucion();
});

$("#btn_cancel").click(function(){
  document.location.href = 'stock.php';
});

});

function plus_compradevolucion () {
  obj = {"a" : $("#txt_motivonc").val(), "b" : raw_compradevolucion };
  myJSON = JSON.stringify(obj);
  $.ajax({	data: obj,	type: "get",	dataType: "json",	url: "attached/get/plus_compradevolucion.php", })
  .done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
    print_html('print_devolution_html.php?a='+data['compradevolucion_id']);
    setTimeout(function(){
      document.location.href = 'stock.php';
    },1500)
  })
  .fail(function( jqXHR, textStatus, errorThrown ) { console.log("BAD " + textStatus); });
}

function make_devolution (datocompra_id,cantidad_disp) {
  open_popup(`popup_product2compradevolution.php?a=${datocompra_id}&b=${cantidad_disp}`,"_popup",'420','420');
}

var raw_compradevolucion = {"facturacompra":<?php echo $facturacompra_id; ?>};
    raw_compradevolucion['datocompra'] = new Array();
function add_return (datocompra_id,cantidad_disp,new_cantidad,medida,medida_cantidad,datocompra_medida) {
  var precio = document.getElementById('precio_'+datocompra_id).innerHTML;
  var raw_medida = <?php echo json_encode($raw_medida); ?>;
  var factor = medida_cantidad/datocompra_medida;   // FACTOR RELATIVO
  var precio_medida = precio*factor;
  if((cantidad_disp*datocompra_medida) < (new_cantidad*medida_cantidad)){ return false; }
  var new_total = new_cantidad*(precio_medida);
  document.getElementById('medida_'+datocompra_id).innerHTML = raw_medida[medida];
  document.getElementById('cant_'+datocompra_id).innerHTML = val_dec(new_cantidad,2,0,1);
  document.getElementById('total_'+datocompra_id).innerHTML = new_total.toFixed(2);
  var raw_datocompra = new Object();
  raw_datocompra["id"] = datocompra_id; raw_datocompra["cantidad"] = new_cantidad*factor; raw_datocompra["medida"] = medida;
    // DUPLICADO
  var exist = 0;
  for (var a in raw_compradevolucion['datocompra']) { if (raw_compradevolucion['datocompra'][a]['id'] === raw_datocompra["id"]) { exist = 1; var key = a; } }
  if (exist === 0) {  raw_compradevolucion['datocompra'].push(raw_datocompra);  }else{  raw_compradevolucion['datocompra'][key] = raw_datocompra; }
  var total_total = 0;
  for (var x in raw_compradevolucion['datocompra']) {
    var datocompra_id = raw_compradevolucion['datocompra'][x]['id'];
    var precio = parseFloat(document.getElementById('precio_'+datocompra_id).innerHTML);
    var cantidad = parseFloat(raw_compradevolucion['datocompra'][x]['cantidad']);
    total_total = total_total+(precio * cantidad);
  }
  document.getElementById('td_total').innerHTML = total_total.toFixed(2);
  close_popup();
}
</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
  	<div id="logo" ></div>
  </div>
	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10 hidden-md">
  	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
      Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
    </div>
		<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php
switch ($_COOKIE['coo_tuser']){
	case '1':
		include 'attached/php/nav_master.php';
	break;
	case '2':
		include 'attached/php/nav_admin.php';
	break;
	case '3':
		include 'attached/php/nav_sale.php';
	break;
	case '4':
		include 'attached/php/nav_paydesk.php';
	break;
	case '5':
		include 'attached/php/nav_stock.php';
	break;
}
?>
		</div>
	</div>
</div>
<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="login.php" method="post" name="form_login"  id="form_login">

  <div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_14">
  	<div id="container_name" class="col-xs-12 col-sm-7 col-md-5 col-lg-5">
      <label class="label label_blue_sky">Proveedor</label>
  	  <span class="form-control bg-disabled"><?php echo $raw_facturacompra[0]['TX_proveedor_nombre']; ?></span>
    </div>
    <div id="container_numeroff" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
      <label class="label label_blue_sky">Numero de Factura</label>
  	  <span class="form-control bg-disabled"><?php echo $raw_facturacompra[0]['TX_facturacompra_numero']; ?></span>
    </div>
    <div id="" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
      <label class="label label_blue_sky" for="">Fecha Factura</label>
  	  <span id="" class="form-control bg-disabled"><?php echo date('d-m-Y', strtotime($raw_facturacompra[0]['TX_facturacompra_fecha'])); ?></span>
    </div>
    <div id="" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
      <label class="label label_blue_sky" for="">Fecha de Ingreso</label>
  	  <span id="" class="form-control bg-disabled"><?php echo date('d-m-Y', strtotime($raw_facturacompra[0]['TX_facturacompra_elaboracion'])); ?></span>
    </div>
    <div id="" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
      <label class="label label_blue_sky" for="">O.C.</label>
  	  <span id="" class="form-control bg-disabled"><?php echo $raw_facturacompra[0]['TX_facturacompra_ordendecompra']; ?></span>
    </div>
    <div id="" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
      <label class="label label_blue_sky" for="">Dep&oacute;sito</label>
  	  <span id="" class="form-control bg-disabled"><?php echo $raw_facturacompra[0]['TX_almacen_value']; ?></span>
    </div>
    <div id="container_motivo"  class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
      <label class="label label_blue_sky" for="txt_motivonc">Motivo de la Devoluci&oacute;n</label>
      <input type="text" id="txt_motivonc" class="form-control" value="" autofocus />
    </div>
  </div>
  <div id="container_tblfacturacompra" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_facturacompra" class="table table-bordered table-condensed table-striped">
      <caption>Productos Ingresados</caption>
      <thead class="bg-primary">
        <tr>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">C&oacute;digo</th>
          <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Producto</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio c/Impuesto</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad Ingresada</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Disponible</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
      </thead>
      <tfoot class="bg-primary"><tr><td colspan="7"></td></tr></tfoot>
      <tbody>
<?php   foreach($raw_facturacompra as $key => $rs_facturacompra){       ?>
          <tr>
            <td><?php echo $rs_facturacompra['TX_producto_codigo']; ?></td>
            <td><?php echo $r_function->replace_special_character($rs_facturacompra['TX_producto_value']); ?></td>
            <td><?php echo $raw_medida[$rs_facturacompra['TX_datocompra_medida']]; ?></td>
<?php         $precio_descuento = $rs_facturacompra['TX_datocompra_precio']-(($rs_facturacompra['TX_datocompra_descuento']*$rs_facturacompra['TX_datocompra_precio'])/100);
              $impuesto = ($precio_descuento*$rs_facturacompra['TX_datocompra_impuesto'])/100;
              $precio_total = $precio_descuento+$impuesto;  ?>
            <td><?php echo number_format($precio_total,2);  ?></td>
            <td><?php echo round($rs_facturacompra['TX_datocompra_cantidad'],3); ?></td>
            <td><?php
              $ttl_devuelto = (empty($raw_compradevolucion[$rs_facturacompra['AI_datocompra_id']])) ? 0 : $raw_compradevolucion[$rs_facturacompra['AI_datocompra_id']];
              echo $disp_quantity = round($rs_facturacompra['TX_datocompra_cantidad']-$ttl_devuelto,3); ?>
            </td>
            <td>
              <button type="button" class="btn btn-warning btn-xs btn-fa" onclick="make_devolution('<?php echo $rs_facturacompra['AI_datocompra_id'];?>','<?php echo $disp_quantity ?>')"><strong>X</strong></button>
            </td>
        	</tr>
<?php   } ?>
      </tbody>
    </table>
  </div>
  <div id="container_tblreturn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<table id="tbl_return" class="table table-bordered table-striped table-condensed">
      <caption>Productos a Devolver</caption>
      <thead class="bg-success">
        <tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
          <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Producto</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Medida</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio C/Imp</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
      </thead>
      <tbody>
<?php
        $total_precio = 0;  $total_impuesto = 0;
        foreach($raw_facturacompra as $key => $rs_facturacompra){       ?>
          <tr>
            <td><?php echo $rs_facturacompra['TX_producto_codigo']; ?></td>
            <td><?php echo $r_function->replace_special_character($rs_facturacompra['TX_producto_value']); ?></td>
            <td id="medida_<?php echo $rs_facturacompra['AI_datocompra_id']; ?>"><?php echo $raw_medida[$rs_facturacompra['TX_datocompra_medida']]; ?></td>
            <td id="cant_<?php echo $rs_facturacompra['AI_datocompra_id']; ?>"><?php echo 0 ?></td>
<?php         $precio_descuento = $rs_facturacompra['TX_datocompra_precio']-(($rs_facturacompra['TX_datocompra_descuento']*$rs_facturacompra['TX_datocompra_precio'])/100);
              $impuesto = ($precio_descuento*$rs_facturacompra['TX_datocompra_impuesto'])/100;
              $precio_total = $precio_descuento+$impuesto;  ?>
            <td id="precio_<?php echo $rs_facturacompra['AI_datocompra_id']; ?>"><?php echo number_format($precio_total,2);  ?></td>
            <td id="total_<?php echo $rs_facturacompra['AI_datocompra_id']; ?>"></td>
          </tr>
<?php   }         ?>
      </tbody>
      <tfoot class="bg-success">
        <tr>
          <td colspan="5"></td>
          <td id="td_total"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
    &nbsp;
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
  </div>
<?php
  $qry_compradevolucion = $link->query("SELECT bh_compradevolucion.AI_compradevolucion_id, bh_compradevolucion.TX_compradevolucion_fecha, bh_compradevolucion.TX_compradevolucion_motivo, ((bh_compradevolucion.TX_compradevolucion_monto-bh_compradevolucion.TX_compradevolucion_descuento)+bh_compradevolucion.TX_compradevolucion_impuesto) AS total, bh_user.TX_user_seudonimo FROM bh_compradevolucion
  INNER JOIN bh_user ON bh_user.AI_user_id = bh_compradevolucion.compradevolucion_AI_user_id
  WHERE bh_compradevolucion.compradevolucion_AI_facturacompra_id = '$facturacompra_id'")or die($link->error);
?>
  <div id="container_tblcreditnote" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<table id="tbl_creditnote" class="table table-bordered table-condensed table-striped">
      <caption>Notas de Crédito</caption>
      <thead class="bg-danger">
        <tr>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
          <th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Motivo</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
      </thead>
      <tfoot class="bg-danger">
        <tr><td colspan="4"></td></tr>
      </tfoot>
      <tbody>
  <?php if($qry_compradevolucion->num_rows > 0){ ?>
  <?php   while($rs_compradevolucion = $qry_compradevolucion->fetch_array(MYSQLI_ASSOC)){ ?>
            <tr title="<?php echo $rs_compradevolucion['TX_user_seudonimo']; ?>">
              <td><?php echo $fecha = date('d-m-Y',strtotime($rs_compradevolucion['TX_compradevolucion_fecha'])); ?></td>
            	<td><?php echo substr($rs_compradevolucion['TX_compradevolucion_motivo'],0,80); ?></td>
              <td class="al_center"><strong>B/ </strong><?php echo number_format($rs_compradevolucion['total'],2); ?></td>
              <td class="al_center">
                <button type="button" onclick="print_html('print_devolution_html.php?a=<?php echo $rs_compradevolucion['AI_compradevolucion_id']; ?>')" name="button" class="btn btn-info btn-sm">Imprimir</button>
              </td>
            </tr>
  <?php   }?>
  <?php }else{ ?>
          <tr>
            <td colspan="4"></td>
          </tr>
  <?php } ?>
      </tbody>
    </table>
  </div>

</form>
</div>


<div id="footer">
  <?php include 'attached/php/req_footer.php'; ?>
</div>

</div>
</div>
<script type="text/javascript">
<?php include 'attached/php/req_footer_js.php'; ?>
</script>

</body>
</html>
