<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';

$link->query("DELETE FROM bh_pago WHERE pago_AI_user_id = '{$_COOKIE['coo_iuser']}'") or die($link->error);

$client_id=$_GET['b'];

$qry_credit = $link->query("SELECT TX_cliente_limitecredito, TX_cliente_plazocredito FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$row_credit = $qry_credit->fetch_array();

$facturaf_limite=strtotime('-'.$row_credit[1].' weeks');
$limit_facturaf=date('Y-m-d',$facturaf_limite);
$qry_outcredit_term=$link->query("SELECT AI_facturaf_id FROM bh_facturaf WHERE TX_facturaf_fecha < '$limit_facturaf'");
$nr_outcredit_term=$qry_outcredit_term->num_rows;

$qry_deficit=$link->query("SELECT SUM(bh_facturaf.TX_facturaf_deficit) AS suma FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE bh_cliente.AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre DESC LIMIT 10");
$row_deficit=$qry_deficit->fetch_array();

$qry_product=$link->query("SELECT * FROM bh_producto WHERE TX_producto_activo = '0' ORDER BY TX_producto_value ASC LIMIT 10")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_client=$link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client=$qry_client->fetch_array();

$txt_facturaventa="SELECT
bh_facturaventa.AI_facturaventa_id, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero,
bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono,
bh_datoventa.AI_datoventa_id, bh_datoventa.datoventa_AI_producto_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_user_id, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_medida,
bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE";

$str_factid = $_GET['a'];
$arr_factid = explode(",",$str_factid);

foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' ORDER BY AI_facturaventa_id ASC, AI_datoventa_id ASC ";
	}else {
		$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' OR";
	}
}
$qry_facturaventa=$link->query($txt_facturaventa);
$nr_facturaventa=$qry_facturaventa->num_rows;
if($nr_facturaventa<1){	echo "<meta http-equiv='Refresh' content='1;url=paydesk.php'>"; }
$raw_facturaventa=array();
while ($rs_facturaventa=$qry_facturaventa->fetch_array()) {
	$raw_facturaventa[]=$rs_facturaventa;
}

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
  $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$txt_pago="SELECT bh_pago.AI_pago_id, bh_pago.TX_pago_fecha, bh_pago.TX_pago_monto, bh_pago.TX_pago_numero, bh_metododepago.TX_metododepago_value FROM (bh_pago INNER JOIN bh_metododepago ON bh_pago.pago_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE pago_AI_user_id = '{$_COOKIE['coo_iuser']}'";
$qry_pago=$link->query($txt_pago);
$rs_pago=$qry_pago->fetch_array();
$ite=0;
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
<link href="attached/css/paydesk_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/paydesk_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$(window).on('beforeunload',function(){
	clean_payment();
	close_popup();
});

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

$("#txt_filterproduct").focus();
var posicion = $("#container_client").offset().top;

$("#btn_addclient").click(function(){
	var name = $("#txt_filterclient").val();
	if($("#txt_filterclient").prop('alt') != ""){
		open_popup('popup_updclient.php?a='+$("#txt_filterclient").prop('alt'),'popup_updclient','425','420')
	}else{
		open_popup('popup_addclient.php?a='+name,'popup_addclient','425','420')
	}
});

$("#btn_payment").click(function(){
	$("#container_paymentinfo").show(500);
	$("#container_product_list").hide(500);
	$("#btn_payment").hide(500);
  $("#btn_discount").hide(500);
  $("#btn_discount").hide(500);
	$("#btn_refresh_tblproduct2sell").hide(500);
	$("#txt_amount").val("");
	setTimeout(function(){	$("#txt_amount").focus() },150);
	$("#span_totalcomprado").text($("#span_total").text());
	$("#span_pendiente").text($("#span_total").text());
	$(".btn_del_product").prop("disabled","disabled");
	$("#tbl_product2sell").css("pointer-events","none");
	var posicion = $("#tbl_paymentlist").offset().top;
	$("html, body").animate({	scrollTop: posicion	}, 2000);
});

$("#btn_cancel").click(function(){
	window.location='paydesk.php';
});

$("#txt_amount").on("keyup", function(e){
	var total_pendiente = $("#span_pendiente").text().replace(",","");
	if(e.which == 13){
		if(this.value === ""){
			$("#btn_amount").click();
		}else{
			$("#txt_amount").blur();
			$("#1").click();
		}
	}
	if(e.which == 120) {
		if (parseFloat(total_pendiente) > 0) {
			console.log("Saldo Pendiente: " + total_pendiente);
			return false;
		}
		$("#btn_process").click();
	}
})

$("#txt_amount").on("blur", function(){
	this.value = val_intw2dec(this.value);
})

$("#btn_cancelpaymentmethod").click(function(){
	document.location.reload();
});

$("#btn_amount").click(function(){
var pendiente = $("#span_payment_to_pay").text().replace(",","");
	pendiente = val_intw2dec(pendiente);
	$("#txt_amount").val(pendiente);
});

$("#btn_process").click(function(){
	$.ajax({	data: {"a" : '<?php echo $str_factid; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/get_payment.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data === '1'){
			 if($("#txt_filterclient").prop("alt") == ""){
			 	$("#txt_filterclient").focus;
				alert("Debe Agregar al Cliente Primero");
			 	return false;
			 }
			 plus_facturaf('<?php echo $str_factid ?>');
		 }
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
});
$("#btn_generate").click(function(){
  $.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
	 .done(function( data, textStatus, jqXHR ) { console.log( "GOOD " + textStatus);
	  if(data[0][0] != ""){
      console.log(data[0][0]);
      $.ajax({	data: {"a" : '<?php echo $str_factid; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/get_payment.php", })
        .done(function( data, textStatus, jqXHR ) {
      	   if(data === '1'){  if($("#txt_filterclient").prop("alt") === ""){  $("#txt_filterclient").focus;  alert("Debe Agregar al Cliente Primero");  return false; }
      		   generate_facturaf('<?php echo $str_factid ?>');
      		 }
      	})
      	.fail(function( jqXHR, textStatus, errorThrown ) {		});
      }else{
        popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
      }
    })
  .fail(function( jqXHR, textStatus, errorThrown ) {
    if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
  })
});
$("#txt_filterproduct").on("keyup",function(e){
	if(e.which == 13){
		setTimeout( function(){ $("#tbl_product tbody tr:first").click(); },250);
	}
	if(e.which == 120) {
		$("#btn_payment").click();
	}
});
$("#btn_discount").on("click", function(){
	$("#tbl_product2sell tbody tr:first").dblclick();
})

// $( function() {
// 	$( "#txt_filterclient").autocomplete({
// 		source: "attached/get/filter_client_sell.php",
// 		minLength: 2,
// 		select: function( event, ui ) {
//       var n_val = ui.item.value;
//       raw_n_val = n_val.split(" | Dir:");
//       ui.item.value = raw_n_val[0];
//       console.log(ui.item.value);
// 			$("#txt_filterclient").prop('alt', ui.item.id);
// 			content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
// 			fire_recall('container_client_recall', content)
// 		}
// 	});
// });

$("#1, #2, #3, #4, #7").on("click", function(){
	plus_payment($(this).prop("id"), '<?php echo $str_factid; ?>');
})
$("#5").on("click", function(){
	var client_id=$("#txt_filterclient").attr("alt");
	$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
	 .done(function( data, textStatus, jqXHR ) {
			console.log( "GOOD " + textStatus);
			if(data[0][0] != ""){
        if(data[0][0] != 2 && data[0][0] != 1){
  				$.ajax({	data: {"a" : client_id, "b" : $("#txt_amount").val() },	type: "GET",	dataType: "text",	url: "attached/get/get_credit_client.php", })
  				.done(function( data, textStatus, jqXHR ) {
             $("#container_alertcredit").html(data)
             if ($("#credit_time_limit, #credit_amount_limit").hasClass('alert-danger')){
               return false;
             }
             plus_payment($("#5").prop("id"), '<?php echo $str_factid; ?>');
           })
  				.fail(function( jqXHR, textStatus, errorThrown ) {		});
        }else{
          $.ajax({	data: {"a" : client_id, "b" : $("#txt_amount").val() },	type: "GET",	dataType: "text",	url: "attached/get/get_credit_client.php", })
          .done(function( data, textStatus, jqXHR ) {
            $("#container_alertcredit").html(data)
            plus_payment($("#5").prop("id"), '<?php echo $str_factid; ?>');
          })
          .fail(function( jqXHR, textStatus, errorThrown ) {		});
        }
			}else{
        popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
      }
	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {
		 if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
	})
})

$("#btn_refresh_tblproduct2sell").on("click",function(){
  location.reload();
});

$('#txt_amount').validCampoFranz('0123456789.');
// $("#container_client_recall").css("display","none");
});



// ########################### FUNCIONES JS

function open_product2addpaycollect(product_id,str_factid){
	open_popup('popup_product2addcollect.php?a='+product_id+'&b='+str_factid, 'popup_product2sell','425','420');
}


</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
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
<form id="form_new_collect" action="print_f_fiscal.php" method="post">
<input type="hidden" name="n_ff" value="<?php echo $_SESSION['numero_ff']; ?>" />
<span id="span_ff"><?php echo $_SESSION['numero_ff'];?></span>
<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtfilterclient" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
  	<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
    <input type="text" class="form-control" alt="<?php echo $raw_facturaventa[0]['facturaventa_AI_cliente_id']; ?>" id="txt_filterclient" name="txt_filterclient" value="<?php echo $raw_facturaventa[0]['TX_cliente_nombre']; ?>" onkeyup="unset_filterclient(event)" />
  </div>
	<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
		<button type="button" id="btn_addclient" class="btn btn-success"><strong>+</strong></button>
	</div>
  <div id="container_spannumeroff" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
  	<label class="label label_blue_sky" for="span_numeroff">Nº</label>
  	<span id="span_numeroff" class="form-control bg-disabled"><?php echo $_SESSION['numero_ff']; ?></span>
  </div>
	<div id="container_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 display_none">

	</div>
</div>
<div id="container_product2sell">
	<div id="container_tblproduct2sale"  class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_product2sell" class="table table-bordered table-condensed table-striped table-hover">
      <thead class="bg-primary">
        <tr>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Coti. Nº</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-3">Producto</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Medida</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Imp.%</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc.%</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">  </th>
        </tr>
      </thead>
      <tbody>
<?php $sub_total= 0; $total_itbm = 0;  $total_descuento = 0;
    	foreach ($raw_facturaventa as $key => $value) {
    		$descuento = (($value['TX_datoventa_descuento']*$value['TX_datoventa_precio'])/100);  $precio_descuento = ($value['TX_datoventa_precio']-$descuento);
    		$impuesto = (($value['TX_datoventa_impuesto']*$precio_descuento)/100);                $precio_total = ($value['TX_datoventa_cantidad']*($precio_descuento+$impuesto)); ?>
        <tr ondblclick="open_popup('popup_loginadmin.php?a=<?php echo $str_factid ?>&b=<?php echo $_GET['b'] ?>&z=admin_datoventa.php','popup_loginadmin','425','420');">
        	<td><?php echo $value['TX_producto_codigo']; ?> </td>
        	<td><?php echo $value['TX_facturaventa_numero']; ?></td>
        	<td><?php echo $r_function->replace_special_character($value['TX_datoventa_descripcion']); ?></td>
        	<td><?php echo $raw_medida[$value['TX_datoventa_medida']]; ?></td>
        	<td onclick="upd_quantityonnewcollect('<?php echo $value['AI_datoventa_id']; ?>');"><?php echo $value['TX_datoventa_cantidad']; ?></td>
        	<td><?php echo number_format($value['TX_datoventa_precio'],2); ?></td>
        	<td><?php echo number_format($impuesto,2).' ('.$value['TX_datoventa_impuesto'].'%)'; ?></td>
        	<td><?php echo number_format($descuento,2).' ('.$value['TX_datoventa_descuento'].'%)'; ?></td>
<?php 		$total_descuento+=$value['TX_datoventa_cantidad']*$descuento;
		      $total_itbm+=$value['TX_datoventa_cantidad']*$impuesto;
		      $sub_total+=$value['TX_datoventa_cantidad']*$value['TX_datoventa_precio'];
		      $total_ff = ($sub_total-$total_descuento)+$total_itbm;
		      $total_ff = round($total_ff,2); ?>
        	<td><?php echo number_format($precio_total,2);	?></td>
	        <td class="al_center"><?php
            if($value['datoventa_AI_user_id'] != $_COOKIE['coo_iuser']){
              if($_COOKIE['coo_tuser'] < 3 && !empty($_SESSION['admin'])){ ?>
                <button type="button" name="" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect('<?php echo $value['datoventa_AI_producto_id']; ?>','<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
<?php 	      }
 			      }else{ ?>
    		      <button type="button" name="" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect('<?php echo $value['datoventa_AI_producto_id']; ?>','<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
<?php       } ?>
          </td>
        </tr>
<?php } ?>
      </tbody>
      <tfoot class="bg-primary">
        <tr>
      	  <td colspan="5"></td>
          <td><span id="span_nettotal"><?php echo $sub_total; ?></span></td>
          <td><strong>Imp: </strong> <br />B/ <span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span></td>
          <td><strong>Desc: </strong> <br />B/ <span id="span_discount"><?php echo number_format($total_descuento,2); ?></span></td>
          <td><strong>Total: </strong> <br />B/ <span id="span_total"><?php echo number_format($total_ff,2); ?></span></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
	</div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_payment" class="btn btn-primary">Cobrar</button>
	&nbsp;&nbsp;
	<button type="button" id="btn_discount" class="btn btn-info">Descuento</button>
  &nbsp;&nbsp;
  <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
  &nbsp;&nbsp;
  <button type="button" id="btn_refresh_tblproduct2sell" class="btn btn-info btn-md" title="Refrescar Tabla">
  <strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></strong>
  </button>
</div>
<div id="container_paymentinfo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 display_none">
    <div id="container_paymentinfotitle" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<span id="paymentinfo_title">Informaci&oacute;n de Pago</span>
    </div>
    <div id="container_payment" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="container_alertcredit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="container_termalert" class="alert alert-danger  display_none">
            plazo
            </div>
            <div id="container_limitalert" class="alert alert-danger display_none">
            limite
            </div>
        </div>
        <div id="container_paymentmethod" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" id="1" name="button" class="btn btn-success btn-lg"><i class="fa fa-money" aria-hidden="true"></i> Efectivo</button>&nbsp;
					<button type="button" id="2" name="button" class="btn btn-primary btn-lg"><i class="fa fa-newspaper-o fa-rotate-180" aria-hidden="true"></i> Cheque</button>&nbsp;
					<button type="button" id="3" name="button" class="btn btn-default btn-lg"><i class="fa fa-cc-visa" aria-hidden="true"></i> Tarjeta Cr&eacute;dito</button>&nbsp;
					<button type="button" id="4" name="button" class="btn btn-info btn-lg"><i class="fa fa-credit-card" aria-hidden="true"></i> Tarjeta Clave</button>&nbsp;
					<button type="button" id="5" name="button" class="btn btn-danger btn-lg"><i class="fa fa-university" aria-hidden="true"></i> Cr&eacute;dito</button>
					&nbsp;&nbsp;&nbsp;
					<button type="button" id="7" name="button" class="btn btn-warning btn-lg">N.C. <span id="client_balance" class="badge"><?php echo number_format($rs_client['TX_cliente_saldo'],2); ?></span></button>
        </div>
    </div>
    <div id="container_txtnumber" class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label class="label label_blue_sky" for="txt_number">Numero de Control</label>
        <input type="text" id="txt_number" name="txt_number" class="form-control" />
    </div>
    <div id="container_txtamount" class="col-xs-11 col-sm-6 col-md-4 col-lg-4">
        <label class="label label_blue_sky" for="txt_amount">Monto</label>
        <input type="text" id="txt_amount" name="txt_amount" class="form-control"  />
    </div>
    <div id="container_btnamount" class="col-xs-2 col-sm-2 col-md-1 col-lg-1 side-btn-md-label">
      <button type="button" id="btn_amount" title="Todo" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i></button>
    </div>
    <div id="container_tblpaymentlist" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_paymentlist" class="table table-bordered table-condensed table-striped">
    	<thead class="bg-primary">
        <tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        	<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Fecha</th>
        	<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Metodo de Pago</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº de Control</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
        </thead>
        <tbody id="tbody_paymentlist">
<?php
					$monto_pagado=0;	$var_pmethod="0";
					if($nr_pago=$qry_pago->num_rows > 0){
 					do{ ?>
        <tr>
	        <td><?php echo $ite=$ite+'1'.".-" ?></td>
	        <td><?php	echo $date=date('d-m-Y',strtotime($rs_pago['TX_pago_fecha']));?></td>
	        <td><?php echo $rs_pago['TX_metododepago_value']; ?></td>
          <td><?php echo $rs_pago['TX_pago_numero']; ?></td>
        	<td><?php echo number_format($rs_pago['TX_pago_monto'],2); ?></td>
          <td>
<?php				if($_COOKIE['coo_tuser'] < 3 || $_COOKIE['coo_tuser'] == '4' ){	?>
            	<button type="button" name="<?php echo $rs_pago['AI_pago_id']; ?>" id="btn_delpago" class="btn btn-danger btn-xs" onclick="del_paymentmethod(this.name)">X</button>
<?php    		}        ?>
          </td>
        </tr>
<?php
				$monto_pagado += $rs_pago['TX_pago_monto'];
      }while($rs_pago=$qry_pago->fetch_array()); ?>
<?php }else{ ?>
		    <tr>
	        <td>&nbsp;</td>
	        <td></td>
	        <td></td>
	      	<td></td>
	        <td></td>
	        <td></td>
	     	</tr>
<?php 	}
				if ($total_ff > $monto_pagado) {
					$cambio = 0;
					$diferencia = $total_ff-$monto_pagado;
				}else{
					$cambio = $monto_pagado-$total_ff;
					$diferencia = 0;
				}
?>
        </tbody>
    <tfoot class="bg-primary">
    <tr>
			<td colspan="6">
				<div id="container_payment_data" class="container-fluid">
					<div id="payment_total" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Total: </strong><br />
		        B/ <span id="span_payment_total"><?php echo number_format($total_ff,2); ?></span>
					</div>
					<div id="payment_paid_out" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Entrega: </strong><br />
		        B/ <span id="span_payment_paid_out"><?php echo number_format($monto_pagado,2); ?></span>
					</div>
					<div id="payment_to_pay" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Diferencia</strong><br />
		        B/ <span id="span_payment_to_pay"><?php	echo number_format($diferencia,2);	?> </span>
					</div>
					<div id="payment_change" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Cambio: </strong><br />
						B/ <span id="span_payment_change"><?php	echo number_format($cambio,2);	?> </span>
					</div>
				</div>
			</td>
		</tr>
		</tfoot>
    </table>
    </div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    <button type="button" id="btn_process" class="btn btn-success">Procesar</button>
			&nbsp;
			<button type="button" id="btn_cancelpaymentmethod" class="btn btn-warning">Cancelar</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" id="btn_generate" class="btn btn-default">Generar</button>
		</div>
</div>

<div id="container_product_list" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_filterproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label class="label label_blue_sky" for="txt_filterproduct">Buscar:</label>
    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" onkeyup="filter_product_collect(this,'<?php echo $str_factid; ?>');" />
	</div>
	<div id="container_selproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_product" class="table table-bordered table-hover table-striped">
    <caption>Lista de Productos:</caption>
    <thead  class="bg-info">
    	<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
        <th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Nombre</th>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Letra</th>
      </tr>
    </thead>
    <tfoot>
	    <tr class="bg-info">
    		<td colspan="5">  </td>
    	</tr>
    </tfoot>
	<tbody>
<?php
	if($nr_product=$qry_product->num_rows > 0){
	do{ ?>
    	<tr onclick="open_product2addpaycollect('<?php echo $rs_product['AI_producto_id']; ?>','<?php echo $str_factid; ?>');">
        	<td><?php echo $rs_product['TX_producto_codigo']; ?></td>
        	<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
        	<td><?php echo $rs_product['TX_producto_cantidad']; ?></td>
        	<td><?php
			$qry_precio=$link->query("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}'")or die($link->error);
			if($nr_precio=$qry_precio->num_rows > 0){
				$rs_precio=$qry_precio->fetch_array();
        if (!empty($rs_precio['TX_precio_cuatro'])) {
          echo number_format($rs_precio['TX_precio_cuatro'],2);
        }else if(empty($rs_precio['TX_precio_cuatro'])) {
          echo number_format(0,2);
        }else{
          echo number_format(0,2);
        }
			}
			?>
            </td>
        	<td>
            <?php
            $qry_letra = $link->query("SELECT bh_letra.TX_letra_value FROM bh_letra, bh_producto WHERE bh_letra.AI_letra_id = '{$rs_product['producto_AI_letra_id']}'");
            $rs_letra=$qry_letra->fetch_array();
			      echo $rs_letra['TX_letra_value']; ?>
          </td>
        </tr>
    <?php
	}while($rs_product=$qry_product->fetch_array());
	}else{
	?>
	    <tr class="bg-info">
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    	</tr>
	<?php
    }
	?>
    </tbody>

    </table>
	</div>
</div>


<!-- ############# FIN DE CONTENT  ################-->
</form>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    		<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>

</body>
</html>
