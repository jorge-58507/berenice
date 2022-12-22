<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
date_default_timezone_set('America/Panama');
require 'attached/php/req_login_admin.php';
$link->query("DELETE FROM bh_nuevodebito WHERE nuevodebito_AI_user_id = '$user_id'")or die($link->error);


$str_factid=$_GET['a'];
$arr_factid = explode(",",$str_factid);
$client_id=$_GET['b'];

$qry_client=$link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client=$qry_client->fetch_array();

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento,
bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if($value === end($arr_factid)){
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' ORDER BY bh_facturaf.TX_facturaf_deficit ASC";
	}else{
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' OR";
	}
}
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();



$txt_nuevodebito="SELECT bh_nuevodebito.AI_nuevodebito_id, bh_nuevodebito.TX_nuevodebito_monto, bh_nuevodebito.TX_nuevodebito_numero, bh_nuevodebito.TX_nuevodebito_fecha, bh_metododepago.TX_metododepago_value
FROM (bh_nuevodebito
INNER JOIN bh_metododepago ON bh_nuevodebito.nuevodebito_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
WHERE bh_nuevodebito.nuevodebito_AI_user_id = '$user_id'";
$qry_nuevodebito=$link->query($txt_nuevodebito)or die($link->error);
$rs_nuevodebito=$qry_nuevodebito->fetch_array();

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
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	$(window).on('beforeunload', function(){
		clean_payment();
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

$("#txt_amount").validCampoFranz(".0123456789");

$("#sel_paymentmethod").val("");

$("#btn_amount").click(function(){
	$("#txt_amount").val($("#span_pendiente").text().replace(",",""));
});

$("#btn_cancelpaymentmethod").click(function(){
	history.back(1);
});
$("#1, #2, #3, #4, #7").on("click", function(){
	plus_paymentondebit($(this).prop("id"), '<?php echo $str_factid; ?>');
})

$("#btn_process").click(function(){
	if($("#txt_motivond").val() == ""){
		$("#txt_motivond").focus();
		$("#txt_motivond").css("border","outset 2px #F00");
		return false;
	}
	$("#txt_motivond").css("border","1px solid #ccc");

	$.ajax({	data: {"a" : '<?php echo $str_factid; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/get_paymentondebit.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data === '1'){
			 $("#btn_process").attr("disabled", true);
	 	 	 plus_debit('<?php echo $str_factid ?>');
		 }
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
});
$("#btn_amount").click(function(){
var pendiente = $("#span_payment_to_pay").text().replace(",","");
	pendiente = val_intw2dec(pendiente);
	$("#txt_amount").val(pendiente);
});

$("#txt_amount").on("keyup", function(e){
	if(e.which === 13){
		if(this.value === ""){
			$("#btn_amount").click();
		}else{
			$("#txt_amount").blur();
			$("#1").click();
		}
	}
	if(e.which === 120) {
		$("#btn_process").click();
	}
})
$("#txt_amount").focus();
$("#txt_amount").on("blur",function(){
	this.value = val_intw2dec(this.value);
});
// ########## payment method
// $("#sel_paymentmethod").on("click",function(){
// 	var total_pagado = $("#span_totalpagado").text().replace(",","");
// 	var total_comprado = $("#span_totalcomprado").text().replace(",","");
// 	var total_pendiente = $("#span_pendiente").text().replace(",","");
// 	if(parseFloat(total_pagado) >= parseFloat(total_comprado)){
// 		return false;
// 	}
// 	var ans = val_intwdec($("#txt_amount").val());
// 	if($("#sel_paymentmethod").val() == '7'){
// 		var	option = ($("#sel_paymentmethod option:selected").text());
// 		numero = option.split(" ");
// 		numero_length = numero.length;
// 		$("#txt_amount").val(parseFloat(numero[numero_length-1]));
// 		$("#txt_number").val($("#sel_paymentmethod option:selected").prop("label"));
// 		exedente = numero[numero_length-1];
// 		if(parseFloat(exedente) > parseFloat(total_pendiente)){
// 			return false;
// 		}
// 	}
// 	if($("#sel_paymentmethod").val()=='1' || $("#sel_paymentmethod").val()=='2'){
// 		if(!ans){	alert("Verifique el monto");	$("#txt_amount").focus();	return false;	}
// 	}
// 	if($("#sel_paymentmethod").val()=='3' || $("#sel_paymentmethod").val()=='4'){
// 		if(!ans){	alert("Verifique el monto");	$("#txt_amount").focus();	return false;	}
// 			var total_pendiente = $("#span_pendiente").text().replace(",","");
// 			total_apagar = $("#txt_amount").val();
// 		if(total_apagar > parseFloat(total_pendiente)){
// 			return false;
// 		}
// 	}
// 	if($("#sel_paymentmethod").val() == '5'){
// 		var client_id=$("#txt_filterclient").attr("alt");
// 		$.ajax({	data: {"a" : client_id},type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
// 		 .done(function( data, textStatus, jqXHR ) { if ( console && console.log ) { console.log( "GOOD " + textStatus );	 }
//
// 			$("#txt_number").prop('disabled',true);	$("#txt_number").val("");
// 			$("#txt_amount").prop('disabled',true);	$("#btn_amount").hide(200);
// 			if(data[0][0] != " "){
// 				$("#txt_amount").val($("#span_pendiente").text().replace(",",""));
// 				plus_paymentondebit();
// 			}
// 			var suma = parseFloat(data[1][0])+parseFloat($("#span_pendiente").text().replace(",",""));
// 			var limit = parseFloat(data[2][0]);
// 			if(suma > limit){
// 				$("#container_limitalert").html('<strong>Atencion!</strong> Este cliente a sobrepasado el monto LIMITE para creditos. <strong>($ '+limit+')</strong>');
// 				$("#container_limitalert").show(300);
// 			}
// 			var deadline = data[3][0];
// 			if(deadline > 0){
// 				$("#container_termalert").html('<strong>Atencion!</strong> Este cliente a sobrepasado el PLAZO para creditos.');
// 				$("#container_termalert").show(300);
// 			}
// 		})
// 		 .fail(function( jqXHR, textStatus, errorThrown ) {
// 			 if ( console && console.log ) {
// 				 console.log( "La solicitud a fallado: " +  textStatus);
// 			 }
// 		})
// 		return false;
// 	}
//
// 	plus_paymentondebit();
// 	$("#btn_amount").hide(300);
// })




});

// ########################### FUNCIONES JS


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
        Bienvenido: <label class="bg-primary">
         <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
        </label>
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
	<div id="container_cliente" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="container_clientname" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <label for="span_clientname">Nombre</label>
    <span id="span_clientname" class=" form-control bg-disabled"><?php echo $rs_facturaf['TX_cliente_nombre']; ?></span>
    	</div>
    	<div id="container_txtmotivond" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <label for="txt_motivond">Motivo</label>
    <input type="text" id="txt_motivond" class="form-control" value="ABONO" />
    	</div>
    </div>
	<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
    <thead class="bg-primary">
    <tr>
    <th>Numero</th>
	<th>Fecha</th>
    <th>Total Bruto</th>
    <th>Impuesto</th>
    <th>Total Facturado</th>
	<th>Monto D&eacute;ficit</th>
    </tr>
    </thead>
    <tbody>
    <?php $total_deuda = 0; ?>
    <?php do{ ?>
    <tr>
    	<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
      <td><?php echo $rs_facturaf['TX_facturaf_fecha']; ?></td>
			<td><?php echo number_format($gross_total = ($rs_facturaf['TX_facturaf_subtotalni']+$rs_facturaf['TX_facturaf_subtotalci']),2); ?></td>
      <td><?php echo number_format($rs_facturaf['TX_facturaf_impuesto'],2); ?></td>
      <td><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></td>
      <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
    </tr>
    <?php $total_deuda += $rs_facturaf['TX_facturaf_deficit']; ?>
	<?php }while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
    </tbody>
    <tfoot class="bg-primary">
    <tr>
    	<td></td><td></td><td></td><td></td><td></td>
        <td><strong>Total:</strong><br />B/ <span id="span_total"><?php echo number_format($total_deuda,2); ?></span></td>
    </tr>
    </tfoot>
    </table>
    </div>
    <div id="container_paymentinfo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="container_paymentinfotitle" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <span id="paymentinfo_title">Informaci&oacute;n de Pago</span>
        </div>
        <div id="container_paymentmethod" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" id="1" name="button" class="btn btn-success btn-lg"><i class="fa fa-money" aria-hidden="true"></i> Efectivo</button>&nbsp;
					<button type="button" id="2" name="button" class="btn btn-primary btn-lg"><i class="fa fa-newspaper-o fa-rotate-180" aria-hidden="true"></i> Cheque</button>&nbsp;
					<button type="button" id="3" name="button" class="btn btn-default btn-lg"><i class="fa fa-cc-visa" aria-hidden="true"></i> Tarjeta Cr&eacute;dito</button>&nbsp;
					<button type="button" id="4" name="button" class="btn btn-info btn-lg"><i class="fa fa-credit-card" aria-hidden="true"></i> Tarjeta D&eacute;bito</button>&nbsp;
					&nbsp;&nbsp;&nbsp;
					<button type="button" id="7" name="button" class="btn btn-warning btn-lg">N.C. <span id="client_balance" class="badge"><?php echo number_format($rs_client['TX_cliente_saldo'],2); ?></span></button>
        </div>
        <div id="container_txtnumber" class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <label for="txt_number">Numero de Control</label>
            <input type="text" id="txt_number" name="txt_number" class="form-control" autocomplete="off" />
        </div>
        <div id="container_txtamount" class="col-xs-11 col-sm-6 col-md-4 col-lg-4">
            <label for="txt_amount">Monto</label>
            <input type="text" id="txt_amount" name="txt_amount" class="form-control" autocomplete="off" />
        </div>
        <div id="container_btnamount" class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
					<button type="button" id="btn_amount" title="Todo" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i></button>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> &nbsp;</div>
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
								if($nr_nuevodebito=$qry_nuevodebito->num_rows > 0){
			 					do{ ?>
			        <tr>
				        <td><?php echo $ite=$ite+'1'.".-" ?></td>
				        <td><?php	echo $date=date('d-m-Y',strtotime($rs_nuevodebito['TX_nuevodebito_fecha']));?></td>
				        <td><?php echo $rs_nuevodebito['TX_metododepago_value']; ?></td>
			          <td><?php echo $rs_nuevodebito['TX_nuevodebito_numero']; ?></td>
			        	<td><?php echo number_format($rs_nuevodebito['TX_nuevodebito_monto'],2); ?></td>
			          <td>
			<?php				if($_COOKIE['coo_tuser'] < 3 || $_COOKIE['coo_tuser'] == '4' ){	?>
			            	<button type="button" name="<?php echo $rs_nuevodebito['AI_nuevodebito_id']; ?>" id="btn_delpago" class="btn btn-danger btn-xs" onclick="del_paymentmethod(this.name)">X</button>
			<?php    		}        ?>
			          </td>
			        </tr>
			<?php
							$monto_pagado += $rs_nuevodebito['TX_nuevodebito_monto'];
						}while($rs_nuevodebito=$qry_nuevodebito->fetch_array()); ?>
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
							if ($total_deuda > $monto_pagado) {
								$cambio = 0;
								$diferencia = $total_deuda-$monto_pagado;
							}else{
								$cambio = $monto_pagado-$total_deuda;
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
					        B/ <span id="span_payment_total"><?php echo number_format($total_deuda,2); ?></span>
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
				&nbsp;&nbsp;
				<button type="button" id="btn_cancelpaymentmethod" class="btn btn-warning">Cancelar</button>
        </div>
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
    &copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
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
