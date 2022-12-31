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
$qry_outcredit_term=$link->query("SELECT AI_facturaf_id, TX_facturaf_fecha FROM bh_facturaf WHERE TX_facturaf_fecha < '$limit_facturaf' AND TX_facturaf_deficit > 0 AND facturaf_AI_cliente_id = '$client_id'")or die($link->error);
$nr_outcredit_term=$qry_outcredit_term->num_rows;
$rs_outcredit_term = $qry_outcredit_term->fetch_array(MYSQLI_ASSOC);

$qry_deficit=$link->query("SELECT SUM(bh_facturaf.TX_facturaf_deficit) AS suma FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE bh_cliente.AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre DESC LIMIT 10");
$row_deficit=$qry_deficit->fetch_array();

$qry_client=$link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client=$qry_client->fetch_array();

$txt_facturaventa = "SELECT 
bh_facturaventa.AI_facturaventa_id, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero,
bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono,
bh_datoventa.AI_datoventa_id, bh_datoventa.datoventa_AI_producto_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_user_id, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_medida,
bh_producto.TX_producto_value, bh_producto.TX_producto_codigo, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento,

bh_datoventa.*,
(SELECT bh_precio.TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = bh_datoventa.datoventa_AI_producto_id AND precio_AI_medida_id = bh_datoventa.TX_datoventa_medida ORDER BY AI_precio_id DESC LIMIT 1) AS precio_venta,
(SELECT bh_datocompra.TX_datocompra_precio FROM bh_datocompra WHERE datocompra_AI_producto_id = bh_datoventa.datoventa_AI_producto_id AND bh_datocompra.TX_datocompra_medida = bh_datoventa.TX_datoventa_medida ORDER BY AI_datocompra_id DESC LIMIT 1) AS precio_compra
FROM (((( bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id) 
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id) 
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id) 
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE ";

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

$raw_porcobrar = array();
$qry_porcobrar = $link->query("SELECT AI_cliente_id FROM bh_cliente WHERE TX_cliente_porcobrar = 1")or die($link->error);
while($rs_porcobrar=$qry_porcobrar->fetch_array(MYSQLI_ASSOC)){
	$raw_porcobrar[$rs_porcobrar['AI_cliente_id']] = $rs_porcobrar;
}
$json_porcobrar = json_encode($raw_porcobrar);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/paydesk_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/paydesk_funct.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(window).on('beforeunload',function(){
				clean_payment();
				close_popup();
			});
			$("#txt_filterclient").validCampoFranz('P0123456789-');

			$("#txt_amount").focus();
			var posicion = $("#container_client").offset().top;

			$("#btn_addclient").click(function(){
				var name = $("#txt_filterclient").val();
				if($("#txt_filterclient").prop('alt') != ""){
					open_popup('popup_updclient.php?a='+$("#txt_filterclient").prop('alt'),'popup_updclient','425','506')
				}else{
					open_popup('popup_addclient.php?a='+name,'popup_addclient','425','460')
				}
			});

			$(".btn_del_product").prop("disabled","disabled");
			$("#btn_modify").click(function(){
				if($("#btn_modify").prop("title") == 1){
					var data = `?a=Errordeprecio&d=Precio&e=notification`;
					$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/new_message.php"+data,	})
					.done(function( data, textStatus, jqXHR ) {
						shot_snackbar('Mensaje enviado','bg-warning'); 			
					})
					.fail(function( jqXHR, textStatus, errorThrown ) {
						if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
					})
				}
				
				$("#container_paymentinfo").hide(500);
				$("#btn_modify").hide(500);
				$("#container_product_list").show(500);
				$("#btn_payment").show(500);
				$("#btn_discount").show(500);
				$("#btn_refresh_tblproduct2sell").show(500);
				$("#txt_amount").val("");
				$(".btn_del_product").prop("disabled",false);

				var posicion = $("#tbl_paymentlist").offset().top;
				$("html, body").animate({	scrollTop: posicion	}, 2000);

			});

			$("#btn_payment").click(function(){
				// AL HACER CLICK EN COBRAR LLAMAR LA FUNCION
				if($("#btn_payment").prop("title") == 1){
					var data = `?a=Errordeprecio&d=Precio&e=notification`;
					$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/new_message.php"+data,	})
					.done(function( data, textStatus, jqXHR ) {
						shot_snackbar('Mensaje enviado','bg-warning'); 			
					})
					.fail(function( jqXHR, textStatus, errorThrown ) {
						if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
					})
				}
				
				$("#container_paymentinfo").show(500);
				$("#btn_modify").show(500);
				$("#container_product_list").hide(500);
				$("#btn_payment").hide(500);
				$("#btn_discount").hide(500);
				$("#btn_refresh_tblproduct2sell").hide(500);
				$("#txt_amount").val("");
				setTimeout(function(){	$("#txt_amount").focus() },150);
				$("#span_payment_total").text($("#span_total").text());
				var to_pay = parseFloat($("#span_total").text())-parseFloat($("#span_payment_paid_out").text())
				console.log(to_pay);
				$("#span_pendiente").text(to_pay);
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
				var pendiente = (cls_collect.total <= cls_collect.payed) ? 0.00 : cls_collect.total - cls_collect.payed;
				pendiente = parseFloat(pendiente).toFixed(2);
				pendiente = val_intw2dec(pendiente);
				$("#txt_amount").val(pendiente);
			});
			// PROCESAR FACTURA
			$("#btn_process").click(function(){
				$("#btn_process, #btn_generate").attr("disabled", true);
				$.ajax({	data: {"a" : '<?php echo $str_factid; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/get_payment.php", })
				.done(function( data, textStatus, jqXHR ) {
					data = JSON.parse(data);
					if(data['answer'] === 1){
						if($("#txt_filterclient").prop("alt") == ""){
							$("#txt_filterclient").focus;
							shot_snackbar('Debe seleccionar un cliente');
							$("#btn_process, #btn_generate").prop("disabled", false);
							return false;
						}
						plus_facturaf('<?php echo $str_factid ?>');
					}else{
						if (data['message'] != '') {
							alert(data['message']);
						}
						$("#btn_process, #btn_generate").prop("disabled", false);
					}
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {		});
			});
			// GENERAR FACTURA
			$("#btn_generate").click(function(){
				$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
				.done(function( data, textStatus, jqXHR ) { console.log( "GOOD " + textStatus);
					if(data[0][0] != ""){
						$("#btn_process, #btn_generate").attr("disabled", true);     /* BLOQUEAR BOTONES */
						$.ajax({	data: {"a" : '<?php echo $str_factid; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/get_payment.php", })
							.done(function( data, textStatus, jqXHR ) {
								data = JSON.parse(data);
								if(data['answer'] === 1){
									if($("#txt_filterclient").prop("alt") === ""){  
										$("#txt_filterclient").focus;  
										alert("Debe Agregar al Cliente Primero"); 
										$("#btn_process, #btn_generate").prop("disabled", false);
										return false; 
									}
									generate_facturaf('<?php echo $str_factid ?>');
								}else{ 
									if (data['message'] != '') {
										alert(data['message']);
									}
									if (data['payment'] === 1) {
										generate_facturaf('<?php echo $str_factid ?>');
									}else{
										$("#btn_process, #btn_generate").prop("disabled", false);
									}
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
			// FIN DE GENERAR FACTURA
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
									console.log('AQUI BLOQUEA RESTRICCION');
									
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
			$("#8").on("click", function(){
				var client_id=$("#txt_filterclient").attr("alt");
				var json_porcobrar = <?php echo $json_porcobrar; ?>;
				var raw_porcobrar = json_porcobrar;
				if(raw_porcobrar[client_id] === undefined) {
					alert("Cliente inhabilitado");
					return false;
				}
				$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
				.done(function( data, textStatus, jqXHR ) {
						console.log( "GOOD " + textStatus);
						if(data[0][0] != ""){
							if(data[0][0] != 2 && data[0][0] != 1){
								$.ajax({	data: {"a" : client_id, "b" : $("#txt_amount").val() },	type: "GET",	dataType: "text",	url: "attached/get/get_credit_client.php", })
								.done(function( data, textStatus, jqXHR ) {
									$("#container_alertcredit").html(data)
									plus_payment(8, '<?php echo $str_factid; ?>');
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {		});
							}else{
								$.ajax({	data: {"a" : client_id, "b" : $("#txt_amount").val() },	type: "GET",	dataType: "text",	url: "attached/get/get_credit_client.php", })
								.done(function( data, textStatus, jqXHR ) {
									$("#container_alertcredit").html(data)
									plus_payment(8, '<?php echo $str_factid; ?>');
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
  <div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
    <div id="logo" ></div> 
  </div>
	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
  	<div id="container_username" class="col-lg-4 visible-lg">
      Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
    </div>
	  <div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
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
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  	<div id="container_txtfilterclient" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
			<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="<?php echo $raw_facturaventa[0]['facturaventa_AI_cliente_id']; ?>" id="txt_filterclient" name="txt_filterclient" value="<?php echo $r_function->replace_special_character($raw_facturaventa[0]['TX_cliente_nombre']); ?>" onkeyup="unset_filterclient(event)" />
    </div>
  	<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
  		<button type="button" id="btn_addclient" class="btn btn-success"><strong>+</strong></button>
  	</div>
    <div id="container_spannumeroff" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    	<label class="label label_blue_sky" for="span_numeroff">Nº</label>
    	<span id="span_numeroff" class="form-control bg-disabled"><?php echo $_SESSION['numero_ff']; ?></span>
    </div>
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
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc.%</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Imp.%</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
          <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">  </th>
        </tr>
      </thead>
      <tbody>
				<?php 
				$sub_imp = 0; 
				$sub_noimp = 0; 
				$total_imp = 0;  
				$total_descuento = 0; 
				$raw_tax=[];
				foreach ($raw_facturaventa as $key => $value) {
					$descuento 				= ($value['TX_datoventa_descuento']*$value['TX_datoventa_precio'])/100;  //Monto a descontar
					$descuento				= round($descuento,2);
					$precio_descuento = ($value['TX_datoventa_precio']-$descuento); //Precio con descuento
					$precio_descuento = round($precio_descuento,2);
					$impuesto 				= ($value['TX_datoventa_impuesto']*$precio_descuento)/100; // Monto del impuesto
					$precio_unitario 	= $precio_descuento+$impuesto;
					$subtotal 				= ($value['TX_datoventa_cantidad']*$precio_unitario); //Percio total de Linea de producto
					$total_descuento	+= $value['TX_datoventa_cantidad']*$descuento;
					$raw_tax[$value['TX_datoventa_impuesto']] = (!empty($raw_tax[$value['TX_datoventa_impuesto']])) ? round(($raw_tax[$value['TX_datoventa_impuesto']] + ($precio_descuento*$value['TX_datoventa_cantidad'])),2) : round((0 + $precio_descuento*$value['TX_datoventa_cantidad']),2);
					?>

					<tr ondblclick="open_popup('popup_loginadmin.php?a=<?php echo $str_factid ?>&b=<?php echo $_GET['b'] ?>&z=admin_datoventa.php','popup_loginadmin','425','420');">
						<td><?php echo $value['TX_producto_codigo']; ?> </td>
						<td><?php echo $value['TX_facturaventa_numero']; ?></td>
						<td><?php echo $r_function->replace_special_character($value['TX_datoventa_descripcion']); ?></td>
						<td><?php echo $raw_medida[$value['TX_datoventa_medida']]; ?></td>
						<td onclick="upd_quantityonnewcollect('<?php echo $value['AI_datoventa_id']; ?>');"><?php echo $value['TX_datoventa_cantidad']; ?></td>
						<td><?php echo number_format($value['TX_datoventa_precio'],2); ?></td>
						<td><?php echo number_format($descuento,3).' ('.$value['TX_datoventa_descuento'].'%)'; ?></td>
						<td><?php echo number_format($impuesto,3).' ('.$value['TX_datoventa_impuesto'].'%)'; ?></td>
						<td><?php echo number_format($subtotal,2);	?></td>
						<td class="al_center"><?php
						if($value['datoventa_AI_user_id'] != $_COOKIE['coo_iuser']){
							if($_COOKIE['coo_tuser'] < 3 && !empty($_SESSION['admin'])){ ?>
								<button type="button" name="" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect('<?php echo $value['datoventa_AI_producto_id']; ?>','<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
								<?php 
							}
						}else{ ?>
							<button type="button" name="" id="btn_delproduct" class="btn btn-danger btn-xs btn_del_product" onclick="del_product2addcollect('<?php echo $value['datoventa_AI_producto_id']; ?>','<?php echo $value['AI_facturaventa_id']; ?>','<?php echo $str_factid ?>');"><strong>X</strong></button>
							<?php       
						} ?>
						</td>
					</tr>
					<?php 
				} 
				foreach ($raw_tax as $a => $tax) {
					if ($a > 0) {
						$sub_imp += $tax;
						$cal_imp = ($a*$tax)/100;
						$cal_imp = round($cal_imp,2);
						$total_imp += $cal_imp;
					}else{
						$sub_noimp += $tax;
					}
				}
				$total_ff = $sub_imp + $sub_noimp + $total_imp;
				?>
      </tbody>
      <tfoot class="bg-primary">
        <tr>
			<td colspan="4"></td>
			<td>
				<span id="span_noimp" class="display_none"><?php echo $sub_noimp; ?></span>
				<strong>Ttl No Impo: </strong> 	<br />B/ <?php echo number_format($sub_noimp,2); ?>
			</td>
			<td>
				<span id="span_nettotal"><?php echo $sub_imp; ?></span>
				<strong>Ttl Impo: </strong> 	<br />B/ <?php echo number_format($sub_imp,2); ?>
			</td>
			<td><strong>Desc: </strong> <br />B/ <span id="span_discount"	><?php echo number_format($total_descuento,2); ?></span></td>
			<td><strong>Imp: </strong> 	<br />B/ <span id="span_itbm"			><?php echo number_format($total_imp,2); ?></span></td>
			<td><strong>Total: </strong> <br />B/ <span id="span_total"		><?php echo number_format($total_ff,2); ?></span></td>
			<td></td>
        </tr>
      </tfoot>
    </table>
	</div>
</div>
<div id="snackbar"></div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_modify" class="btn btn-warning btn-lg">Modificar</button>
	&nbsp;&nbsp;
	<button type="button" id="btn_payment" class="btn btn-primary btn-lg display_none">Cobrar</button>
	&nbsp;&nbsp;
	<button type="button" id="btn_discount" class="btn btn-info btn-lg display_none">Descuento</button>
  &nbsp;&nbsp;
  <button type="button" id="btn_cancel" class="btn btn-danger btn-lg">Salir</button>
</div>
<div id="container_paymentinfo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
	<div id="container_paymentinfotitle" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<span id="paymentinfo_title">INFORMACI&Oacute;N DE PAGO</span>
	</div>
	<div id="container_payment" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_alertcredit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<?php $outcredit_term = ($nr_outcredit_term > 0) ? '<strong>L&iacute;mite Sobrepasado</strong> ('.date('d-m-Y', strtotime($rs_outcredit_term['TX_facturaf_fecha'])).')' : '<strong>No sobrepasado</strong>'; ?>
			<div id="container_termalert" class="alert alert-info">
				<strong>Limite Plazo:</strong> <?php echo date('d-m-Y', strtotime($limit_facturaf)).', '.$outcredit_term ?> - <strong>L&iacute;mite Deuda:</strong> B/ <?php echo number_format($row_credit['TX_cliente_limitecredito'],2).', <strong>Saldo Pendiente: </strong>B/'.number_format($row_deficit['suma'],2) ?>
			</div>
			<div id="container_termalert" class="alert alert-danger display_none">
				plazo
			</div>
			<div id="container_limitalert" class="alert alert-danger display_none">
				limite
			</div>
		</div>
		<div id="container_paymentmethod" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="1" name="button" class="btn btn-success btn-lg"	>Efectivo</button>&nbsp;
			<button type="button" id="2" name="button" class="btn btn-primary btn-lg"	>Cheque</button>&nbsp;
			<button type="button" id="3" name="button" class="btn btn-default btn-lg"	>Tarjeta Cr&eacute;dito</button>&nbsp;
			<button type="button" id="4" name="button" class="btn btn-info btn-lg"		>Tarjeta Clave</button>&nbsp;
			<button type="button" id="5" name="button" class="btn btn-danger btn-lg"	>Cr&eacute;dito</button>
			&nbsp;&nbsp;&nbsp;
			<button type="button" id="7" name="button" class="btn btn-warning btn-lg">N.C. <span id="client_balance" class="badge"><?php echo number_format($rs_client['TX_cliente_saldo'],2); ?></span></button>
			<?php     if (!empty($raw_porcobrar[$client_id])) {   ?>
				<button type="button" id="8" name="button" class="btn btn_yellow btn-lg">Por Cobrar</button>
			<?php     }   ?>
		</div>
	</div>
    <div class="col-xs-11 col-sm-12 col-md-12 col-lg-12 no_padding" style="display:flex; flex-direction: inline-block; justify-content:center;">
      <div id="container_txtnumber" class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        <label class="label label_blue_sky" for="txt_number">Numero de Control</label>
        <input type="text" id="txt_number" name="txt_number" class="form-control" />
      </div>
      <div id="container_txtamount" class="col-xs-10 col-sm-3 col-md-2 col-lg-2">
        <label class="label label_blue_sky" for="txt_amount">Monto</label>
        <input type="text" id="txt_amount" name="txt_amount" class="form-control"  />
      </div>
      <div id="container_btnamount" class="col-xs-2 col-sm-2 col-md-2 col-lg-1 side-btn-md-label">
        <button type="button" id="btn_amount" title="Todo" class="btn btn-success"><span class="glyphicon glyphicon-tower"></span></button>
      </div>
    </div>
    <div id="container_tblpaymentlist" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
    <table id="tbl_paymentlist" class="table table-bordered table-condensed table-striped">
    	<thead class="bg-primary">
        <tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Metodo de Pago</th>
        	<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nº de Control</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Monto</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
			</thead>
			<tbody id="tbody_paymentlist">
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<tfoot class="bg-primary">
				<tr>
					<td colspan="5">
						<div id="container_payment_data" class="container-fluid">
							<div id="payment_total" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<strong>Total: </strong><br />
								B/ <span id="span_payment_total"><?php echo number_format($total_ff,2); ?></span>
							</div>
							<div id="payment_paid_out" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<strong>Entrega: </strong><br />
								B/ <span id="span_payment_paid_out"><?php echo number_format(0,2); ?></span>
							</div>
							<div id="payment_to_pay" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<strong>Diferencia</strong><br />
								B/ <span id="span_pendiente"><?php	echo number_format($total_ff,2);	?> </span>
							</div>
							<div id="payment_change" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<strong>Cambio: </strong><br />
								B/ <span id="span_payment_change"><?php	echo number_format(0,2);	?> </span>
							</div>
						</div>
					</td>
				</tr>
			</tfoot>
    </table>
    </div>
    <div id="container_btn_process" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center pt_15">
	    <button type="button" id="btn_process" class="btn btn-success">Procesar</button>
			&nbsp;
			<button type="button" id="btn_cancelpaymentmethod" class="btn btn-warning">Cancelar</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" id="btn_generate" class="btn btn-default">Generar</button>
		</div>
</div>

<div id="container_product_list" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 display_none">
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
				<tr class="bg-info">
					<td>  </td>
					<td>  </td>
					<td>  </td>
					<td>  </td>
					<td>  </td>
				</tr>
			</tbody>
    </table>
	</div>
</div>


<!-- ############# FIN DE CONTENT  ################-->
</form>
</div>

<div id="footer">
  <?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
<script type="text/javascript">
	<?php include 'attached/php/req_footer_js.php'; ?>
	const cls_collect = new class_collect(<?php echo $total_ff; ?>,0)
	var raw_medida = JSON.parse('<?php echo json_encode($raw_medida); ?>');
	const cls_measure = new class_measure(raw_medida)
</script>
</body>
</html>
