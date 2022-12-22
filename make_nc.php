<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$facturaf_id=$_GET['a'];

$link->query("DELETE FROM bh_nuevadevolucion WHERE nuevadevolucion_AI_user_id = '$user_id'");

$qry_facturaf=$link->query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha,
bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_deficit,
bh_datoventa.AI_datoventa_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento,bh_datoventa.datoventa_AI_producto_id,bh_datoventa.TX_datoventa_descripcion,bh_datoventa.TX_datoventa_medida,
bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.AI_producto_id, bh_producto.TX_producto_medida,
bh_cliente.TX_cliente_nombre
FROM ((((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'") or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();

$qry_nuevadevolucion=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_nuevadevolucion.TX_nuevadevolucion_cantidad, bh_nuevadevolucion.AI_nuevadevolucion_id, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
FROM ((bh_datoventa
      INNER JOIN bh_nuevadevolucion ON bh_datoventa.AI_datoventa_id = bh_nuevadevolucion.nuevadevolucion_AI_datoventa_id)
      INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
      WHERE bh_nuevadevolucion.nuevadevolucion_AI_user_id = '$user_id'")or die($link->error);
$rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array();

$qry_creditnote=$link->query("SELECT bh_user.TX_user_seudonimo, bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_monto, bh_notadecredito.TX_notadecredito_impuesto, bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_fecha
FROM (bh_notadecredito
INNER JOIN bh_user ON bh_notadecredito.notadecredito_AI_user_id = bh_user.AI_user_id)
WHERE notadecredito_AI_facturaf_id = '$facturaf_id'")or die($link->error);
$rs_creditnote=$qry_creditnote->fetch_array();
$nr_creditnote=$qry_creditnote->num_rows;

$qry_datopago=$link->query("SELECT bh_datopago.AI_datopago_id, bh_metododepago.TX_metododepago_value, bh_datopago.datopago_AI_metododepago_id
  FROM (bh_datopago INNER JOIN bh_metododepago ON bh_metododepago.AI_metododepago_id = bh_datopago.datopago_AI_metododepago_id)
  WHERE datopago_AI_facturaf_id = '$facturaf_id'")or die($link->error);
$payment_method="";
$block_btn_anulate=1;
while ($rs_datopago=$qry_datopago->fetch_array()) {
  $payment_method .=$rs_datopago['TX_metododepago_value'].PHP_EOL;
  if ($rs_datopago['datopago_AI_metododepago_id'] === '7' || $rs_datopago['datopago_AI_metododepago_id'] === '4') {
    $block_btn_anulate=0;
  }
}

$qry_datodevolucion=$link->prepare("SELECT bh_datodevolucion.TX_datodevolucion_cantidad, bh_datodevolucion.datodevolucion_AI_producto_id, bh_datodevolucion.TX_datodevolucion_medida
  FROM ((((bh_datodevolucion
    INNER JOIN bh_notadecredito ON bh_notadecredito.AI_notadecredito_id = bh_datodevolucion.datodevolucion_AI_notadecredito_id)
    INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
    INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
    INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
WHERE bh_datodevolucion.datodevolucion_AI_datoventa_id = ?
AND bh_datoventa.AI_datoventa_id = ?
AND bh_datodevolucion.datodevolucion_AI_producto_id = ? ")or die($link->error);

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

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
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});

$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})

$("#txt_motivonc").validCampoFranz("0123456789 abcdefghijklmnopqrstuvwxyz.,");
$("#txt_debito").validCampoFranz("0123456789");

$("#txt_motivonc").blur(function(){
	this.value = this.value.toUpperCase();
});


$("#btn_save").click(function(){
	if(parseFloat($("#span_totalnc").text()) < 0.01 ){ return false;	}
	if($("#txt_motivonc").val() === "" ){
		set_bad_field("txt_motivonc");
		$("#txt_motivonc").focus();
		return false;
	}  set_good_field("txt_motivonc");
	if($("#sel_destinonc").val() === ""){
		set_bad_field("sel_destinonc");
		$("#sel_destinonc").focus();
		return false;
	}  set_good_field("sel_destinonc");
    $("#btn_save, #btn_anulate").attr("disabled", true);
    plus_creditnote(0);
});

$("#btn_cancel").click(function(){
	clean_newreturn();
});

$("#btn_anulate").on("click", function(){
  res = confirm('¿Confirma la anulacion de la factura?');
  if (!res) { return false; }
  $("#btn_save, #btn_anulate").attr("disabled", true);
  $.ajax({	data: {"a" : '<?php echo $facturaf_id; ?>' },	type: "GET",	dataType: "text",	url: "attached/get/get_datoventabyfacturaf_nc.php", })
   .done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
   $("#txt_motivonc").val("ANULACION");
   $("#sel_destinonc").val('EFECTIVO');
   plus_creditnote(1);
  })
   .fail(function( jqXHR, textStatus, errorThrown ) {	     console.log("BAD " + textStatus);	});
})

});

function plus_creditnote(anulated_index){
  $.ajax({	data: {"a" : $("#txt_motivonc").val(), "b" : $("#sel_destinonc").val(), "c" : $("#txt_debito").val(), "d" : anulated_index },	type: "GET",	dataType: "text",	url: "attached/get/plus_creditnote.php", })
  .done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
     setTimeout("window.location='print_creditnote.php'",250);
  })
  .fail(function( jqXHR, textStatus, errorThrown ) {	     console.log("BAD " + textStatus);	});
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

<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_name" class="col-xs-12 col-sm-7 col-md-5 col-lg-5">
    <label class="label label_blue_sky" for="span_clientenombre">Nombre</label>
	  <span id="span_clientenombre" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_cliente_nombre']; ?></span>
  </div>
	<div id="container_numeroff" class="col-xs-12 col-sm-5 col-md-3 col-lg-2">
    	<label class="label label_blue_sky" for="span_numeroff">Numero de Factura</label>
		<span id="span_numeroff" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_facturaf_numero']; ?>
    </span>
    </div>
	  <div id="container_deficit" class="col-xs-12 col-sm-5 col-md-2 col-lg-2">
    	<?php if($rs_facturaf['TX_facturaf_deficit'] > 0){?>
        <script type="text/javascript">alert("Esta factura posee deficit")</script>
        <label class="label label_blue_sky" for="span_deficit">Deficit</label>
      	<span id="span_deficit" class="form-control"><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></span>
    	<?php } ?>
    </div>
    <div id="container_motivo"  class="col-xs-12 col-sm-12 col-md-6 col-lg-6 pt_7">
        <label class="label label_blue_sky" for="txt_motivonc">Motivo</label>
        <input type="text" id="txt_motivonc" class="form-control" autofocus />
    </div>
    <div id="container_debito"  class="col-xs-2 col-sm-2 col-md-2 col-lg-1 pt_7">
        <label class="label label_blue_sky" for="txt_debito">Retener %</label>
        <input type="text" id="txt_debito" class="form-control" value="0" />
    </div>
    <div id="container_destino"  class="col-xs-10 col-sm-10 col-md-4 col-lg-5 pt_7">
        <label class="label label_blue_sky" for="sel_destinonc">Destino</label>
        <select id="sel_destinonc" class="form-control" >
        	<option value="">Seleccione</option>
            <option value="SALDO">Saldo a Favor</option>
<?php       if ($rs_facturaf['TX_facturaf_deficit'] < 0.01) { ?>
              <option value="EFECTIVO">Retorno de Efectivo</option>
<?php       }  ?>
        </select>
    </div>
</div>
<div class="container-fluid al_center">
<?php if ($qry_datopago->num_rows < 2 && $nr_creditnote < 1 && $block_btn_anulate > 0) { ?>
  <button type="button" name="" id="btn_anulate"  class="btn btn-danger btn-lg" title="<?php echo $payment_method; ?>"><i class="fa fa-times"></i> Anular Factura</button>
<?php
} else {
?>
  <button type="button" name="" id="" disabled  class="btn btn-danger btn-lg" title="<?php echo $payment_method; ?>"><i class="fa fa-times"></i> Anular Factura</button>
<?php } ?>

</div>
<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
    <caption>Productos Facturados</caption>
    <thead class="bg-primary">
        <tr>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">C&oacute;digo</th>
            <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Producto</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio c/Impuesto</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad Facturada</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad Retirada</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
      <tr>
        <td colspan="7"></td>
      </tr>
    </tfoot>

    <tbody>

<?php do{ ?>
    	<tr>
        <td><?php echo $rs_facturaf['TX_producto_codigo']; ?></td>
        <td><?php echo $r_function->replace_special_character($rs_facturaf['TX_datoventa_descripcion']); ?></td>
        <td><?php echo $raw_medida[$rs_facturaf['TX_datoventa_medida']]; ?></td>
<?php     $descuento = ($rs_facturaf['TX_datoventa_precio']*$rs_facturaf['TX_datoventa_descuento'])/100;
          $precio_descuento = $rs_facturaf['TX_datoventa_precio']-$descuento;
          $impuesto = ($precio_descuento*$rs_facturaf['TX_datoventa_impuesto'])/100;
          $precio_total = $precio_descuento+$impuesto;  ?>
        <td><?php echo number_format($precio_total,2);  ?></td>
        <td><?php echo $rs_facturaf['TX_datoventa_cantidad']; ?></td>
        <td><?php
          $qry_datodevolucion->bind_param("iii",$rs_facturaf['AI_datoventa_id'],$rs_facturaf['AI_datoventa_id'],$rs_facturaf['datoventa_AI_producto_id']); $qry_datodevolucion->execute(); $result=$qry_datodevolucion->get_result();
          $rs_datodevolucion=$result->fetch_array();
  			  $total_devuelto=0;
          if ($result->num_rows > 0) {
    			  do{
              $prep_producto_medida->bind_param("ii", $rs_facturaf['datoventa_AI_producto_id'], $rs_datodevolucion['TX_datodevolucion_medida']); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
              $rs_producto_medida = $qry_producto_medida->fetch_array();
              $rel_devuelto = $rs_producto_medida['TX_rel_productomedida_cantidad'];
              $prep_producto_medida->bind_param("ii", $rs_facturaf['datoventa_AI_producto_id'], $rs_facturaf['TX_datoventa_medida']); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
              $rs_producto_medida = $qry_producto_medida->fetch_array();
              $rel_datoventa = $rs_producto_medida['TX_rel_productomedida_cantidad'];
              $cantidad_devuelta = $rs_datodevolucion['TX_datodevolucion_cantidad']*($rel_devuelto/$rel_datoventa);
        			$total_devuelto += $cantidad_devuelta;
            }while($rs_datodevolucion=$result->fetch_array());
          }
  			  echo $retired_quantity = round($rs_facturaf['TX_datoventa_cantidad']-$total_devuelto,3);
      ?></td>
        <td>
          <button type="button" class="btn btn-warning btn-xs btn-fa" onclick="new_return('<?php echo $rs_facturaf['AI_datoventa_id'];?>','<?php echo $retired_quantity ?>')"><strong><i class="fa fa-recycle" aria-hidden="true"></i></strong></button>
        </td>
  	</tr>
<?php }while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
    </tbody>
  </table>
</div>
<div id="container_tblreturn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_return" class="table table-bordered table-striped table-condensed">
    <caption>Productos a Reingresar</caption>
    <thead class="bg-success">
      <tr>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Producto</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP%</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Subtotal</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
      </tr>
    </thead>
    <tbody>
<?php $total_precio = 0;  $total_impuesto = 0;
    do{ ?>
    <tr>
    	<td><?php echo $rs_nuevadevolucion['TX_producto_codigo']; ?></td>
      <td><?php echo $r_function->replace_special_character($rs_nuevadevolucion['TX_datoventa_descripcion']); ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_producto_medida']; ?></td>
      <td><?php echo $rs_nuevadevolucion['TX_nuevadevolucion_cantidad']; ?></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
<?php }while($rs_nuevadevolucion=$qry_nuevadevolucion->fetch_array()); ?>
    </tbody>
    <tfoot class="bg-success">
    <tr>
    	<td></td><td></td><td></td><td></td>
      <td>
      <label for="span_preciowdescuento">Precio c/ Descuento:</label><br />
      B/ <span id="span_preciowdescuento"><?php echo number_format($total_precio,2); ?></span></td>
      <td>
      <label for="span_impuesto">Impuesto:</label><br />
      B/ <span id="span_impuesto"><?php echo number_format($total_impuesto,2); ?></span></td>
      <td>
      <label for="span_totalnc">Total:</label><br />
      B/ <span id="span_totalnc"><?php echo number_format($total_nc = $total_precio+$total_impuesto,2); ?></span></td>
      <td></td>
    </tr>
    </tfoot>
    </table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
  &nbsp;
  <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>
<div id="container_tblcreditnote" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_creditnote" class="table table-bordered table-condensed table-striped">
    <caption>Notas de Crédito Activas</caption>
    <thead class="bg-danger">
      <tr>
        <th>Numero</th>
        <th>Monto Total</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tfoot class="bg-danger">
      <tr><td colspan="3"></td></tr>
    </tfoot>
    <tbody>
<?php if($nr_creditnote > '0'){ ?>
<?php   do{ ?>
          <tr title="<?php echo $rs_creditnote['TX_user_seudonimo']; ?>">
          	<td><?php echo $rs_creditnote['TX_notadecredito_numero']; ?></td>
        		<td><strong>B/ </strong><?php echo number_format($total = $rs_creditnote['TX_notadecredito_monto']+$rs_creditnote['TX_notadecredito_impuesto'],2); ?></td>
            <td><?php echo $fecha = date('d-m-Y',strtotime($rs_creditnote['TX_notadecredito_fecha'])); ?></td>
          </tr>
<?php   }while($rs_creditnote=$qry_creditnote->fetch_array()); ?>
<?php }else{ ?>
        <tr>
          <td colspan="3"></td>
        </tr>
<?php } ?>
    </tbody>
  </table>
</div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    <div id="container_txtcopyright" class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
      &copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
    </div>
    <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
    	<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
    </div>
    <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
      <button type="button" class="btn btn-danger" id="btn_exit">Salir</button>
    </div>
  </div>
</div>

</div>
</div>

</body>
</html>
