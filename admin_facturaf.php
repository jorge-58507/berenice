﻿<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$fecha_actual=date('Y-m-d');
$fecha_i=date('d-m-Y',strtotime(date('Y-m-d',strtotime('-1 week'))));
$fecha_f = date('d-m-Y', strtotime($fecha_actual));

$qry_facturaf=$link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.facturaf_AI_cliente_id, bh_facturaf.facturaf_AI_user_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_status,
bh_cliente.TX_cliente_nombre,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaf.facturaf_AI_user_id = bh_user.AI_user_id)
ORDER BY  TX_facturaf_numero DESC LIMIT 10");
$rs_facturaf=$qry_facturaf->fetch_array();
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>

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

$(window).on('beforeunload',function(){
	close_popup();
});

$("#btn_back").click(function(){
	window.history.back(1);
});
$("#txt_filterfacturaf").keyup(function(){
	filter_adminfacturaf(this.value);
})
$("input[name=r_limit]").on("change", function(){
	$("#txt_filterfacturaf").keyup();
})
$("#sel_paymentmethod").on("change", function(){
	filter_adminfacturaf($("#txt_filterfacturaf").val());
})

$( function() {
var dateFormat = "dd-mm-yy",
  from = $( "#txt_date_initial" )
	.datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  numberOfMonths: 2
	})
	.on( "change", function() {
	  to.datepicker( "option", "minDate", getDate( this ) );
	}),
  to = $( "#txt_date_final" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	numberOfMonths: 2
  })
  .on( "change", function() {
	from.datepicker( "option", "maxDate", getDate( this ) );
  });

function getDate( element ) {
  var date;
  try {
	date = $.datepicker.parseDate( dateFormat, element.value );
  } catch( error ) {
	date = null;
  }

  return date;
}
});

$("#filter_by_deficit").on("click",function(){
	filter_adminfacturaf("deficit");
});

$("#txt_date_final").change(function(){
	filter_adminfacturaf($("#txt_filterfacturaf").val());
});

});
function toggle_tr(tr_id){
	$("#"+tr_id+"").toggle("slow","swing");
}
function open_newdebit(client_id){
	$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data[0][0] != ""){
			 open_popup('popup_newdebit.php?a='+client_id,'newdebit','425','420');
		 }else{
			 open_popup('popup_loginadmin.php?z=start_admin.php','_popup','425','420');
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
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
<form action="login.php" method="post" name="form_login"  id="form_login">
<div id="container_filterfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtfilterfacturaf" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	  <label for="txt_filterfacturaf" class="label label_blue_sky">Buscar</label>
	  <input type="text" id="txt_filterfacturaf" class="form-control" placeholder="Numero Factura o Nombre de Cliente" autofocus />
	</div>
	<div id="container_txtdatei"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	  <label for="txt_date_initial" class="label label_blue_sky">Fecha Inicial</label>
	  <input type="text" id="txt_date_initial" name="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_i; ?>" />
	</div>
	<div id="container_txtdatef"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	  <label for="txt_date_final" class="label label_blue_sky">Fecha Final</label>
	  <input type="text" id="txt_date_final" name="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_f; ?>" />
	</div>
	<div id="container_selpaymentmethod" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php 	$qry_paymentmethod = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
		$raw_metododepago = array();
		while ($rs_paymentmethod = $qry_paymentmethod->fetch_array()) {
			$raw_metododepago[$rs_paymentmethod['AI_metododepago_id']] = $rs_paymentmethod['TX_metododepago_value'];
		} ?>
		<label class="label label_blue_sky" for="sel_paymentmethod">M&eacute;todo de P.</label>
		<select id="sel_paymentmethod" class="form-control">
			<option value="todos">Todos</option>
<?php  		foreach ($raw_metododepago as $key => $value) {
				echo "<option value=\"$key\">$value</option>";
			}	?>
		</select>
	</div>
	<div id="container_rlimit"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		<label for="r_limit" class="label label_blue_sky">Mostrar</label><br />
		<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="10" checked="checked">10</label>
	  <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_50" value="50">50</label>
	  <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_100" value="100">100</label>
	  <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_0" value="">Todas</label>
	</div>
</div>
<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
	  <thead class="bg-primary">
			<tr>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº</th>
        <th class="col-xs-4 col-sm-4 col-md-4 col-lg-3">Nombre</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Hora</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Deficit</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Vendedor</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><i id="filter_by_deficit" class="fa fa-angle-double-down" onclick="filter_adminfacturaf('deficit');"></i></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
      </tr>
    </thead>
    <tbody>
<?php
	$total_total=0; $total_deficit=0;
	$total_efectivo=0; $total_tarjeta_dc=0; $total_tarjeta_dd=0; $total_cheque=0; $total_credito=0; $total_notadc=0; $total_porcobrar=0;
	$prep_vendor = $link->prepare("SELECT bh_user.TX_user_seudonimo FROM ((bh_facturaf
		INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
		WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
	$prep_payment=$link->prepare("SELECT bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value FROM ((bh_datopago INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id) INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
		 	if($nr_facturaf=$qry_facturaf->num_rows > 0){
				do{
					$total_total += $rs_facturaf['TX_facturaf_total'];
					$total_deficit += $rs_facturaf['TX_facturaf_deficit'];
					$prep_payment->bind_param('i',$rs_facturaf['AI_facturaf_id']); $prep_payment->execute(); $qry_payment=$prep_payment->get_result();
					$raw_payment=array();	$i=0;
					while($rs_payment=$qry_payment->fetch_array()){
						$raw_payment[$rs_payment['datopago_AI_metododepago_id']]=$rs_payment['TX_datopago_monto'];
						$i++;
					}
					$style='';
					if (array_key_exists(1,$raw_payment)) {	$style= ($style === '') ? 'style="color: #74c374"' : 'style="color: #700fb4"';	}
					if (array_key_exists(2,$raw_payment)) {	$style= ($style === '') ? 'style="color: #518ec2"' : 'style="color: #700fb4"';	}
					if (array_key_exists(3,$raw_payment)) {	$style= ($style === '') ? 'style="color: #000000"' : 'style="color: #700fb4"';	}
					if (array_key_exists(4,$raw_payment)) {	$style= ($style === '') ? 'style="color: #73c9e3"' : 'style="color: #700fb4"';	}
					if (array_key_exists(5,$raw_payment)) {	$style= ($style === '') ? 'style="color: #df6d69"' : 'style="color: #700fb4"';	}
					if (array_key_exists(7,$raw_payment)) {	$style= ($style === '') ? 'style="color: #f2b968"' : 'style="color: #700fb4"';	}
					if (array_key_exists(8,$raw_payment)) {	$style= ($style === '') ? 'style="color: #bdbd07"' : 'style="color: #700fb4"';	}

					?>
					<tr <?php echo $style; ?> onclick="toggle_tr('tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>')" ondblclick="print_html('print_client_facturaf.php?a=<?php echo $rs_facturaf['AI_facturaf_id'];?>')" >
			      <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
			      <td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
			      <td><?php echo $fecha=date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha']));	?></td>
			      <td><?php echo $rs_facturaf['TX_facturaf_hora']; ?></td>
			      <td><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></td>
			      <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
			      <td><?php
							$prep_vendor->bind_param('i', $rs_facturaf['AI_facturaf_id']); $prep_vendor->execute(); $qry_vendor=$prep_vendor->get_result();
							$rs_vendor=$qry_vendor->fetch_array(MYSQLI_ASSOC);
							echo $rs_vendor['TX_user_seudonimo'];
						?></td>
			      <td><button type="button" id="btn_openff" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_watchfacturaf.php?a='+this.name,'watch_facturaf','950','547');">VER</button></td>
      			<td>
<?php 				if($rs_facturaf['TX_facturaf_deficit'] != '0'){ ?>
								<button type="button" id="btn_opennewdebit" name="<?php echo $rs_facturaf['facturaf_AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_newdebit(this.name);">DEBITAR</button>
<?php					}			?>
			      </td>
			      <td><button type="button" id="btn_makenc" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-warning btn-sm" onclick="make_nc(this.name);">N.C.</button></td>
			  	</tr>
			    <tr id="tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>" style="display:none;">
			      <td colspan="10" style="padding:0;">
				      <table id="tbl_payment" class="table table-condensed table_no_margin table-bordered" style="margin:0;">
					      <tr>
            			<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[1])){ echo "<strong>Efectivo:</strong> <br />".number_format($raw_payment[1],2); $total_efectivo += $raw_payment[1];}	?>
            			</td>
            			<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[2])){ echo "<strong>Cheque:</strong> <br />".number_format($raw_payment[2],2); $total_cheque += $raw_payment[2];}	?>
            			</td>
									<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
<?php								if(isset($raw_payment[3])){ echo "<strong>TDC:</strong> <br />".number_format($raw_payment[3],2); $total_tarjeta_dc += $raw_payment[3];}	?>
            			</td>
									<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
<?php								if(isset($raw_payment[4])){ echo "<strong>TDD:</strong> <br />".number_format($raw_payment[4],2); $total_tarjeta_dd += $raw_payment[4];}	?>
            			</td>
            			<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[5])){ echo "<strong>Cr&eacute;dito:</strong> <br />".number_format($raw_payment[5],2); $total_credito += $raw_payment[5];}	?>
            			</td>
									<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[7])){ echo "<strong>Nota de C.:</strong> <br />".number_format($raw_payment[7],2); $total_notadc += $raw_payment[7];}	?>
            			</td>
									<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[8])){ echo "<strong>P.Cobrar:</strong> <br />".number_format($raw_payment[8],2); $total_porcobrar += $raw_payment[8];}	?>
            			</td>
            		</tr>
            	</table>
            </td>
        	</tr>
<?php 	}while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
<?php }else{ 		?>
	    <tr>
	      <td colspan="10"></td>
      </tr><?php
		} ?>
  </tbody>
  <tfoot class="bg-primary">
    <tr>
      <td colspan="10">
        <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
          <tr>
          	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>Efectivo:</strong> <br />B/ <?php echo number_format($total_efectivo,2); ?></td>
						<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>Cheque:</strong> <br />B/ <?php echo number_format($total_cheque,2); ?></td>
          	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>TDC:</strong> <br />B/ <?php echo number_format($total_tarjeta_dc,2); ?></td>
						<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>TDD:</strong> <br />B/ <?php echo number_format($total_tarjeta_dd,2); ?></td>
          	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>Cr&eacute;dito:</strong> <br />B/ <?php echo number_format($total_credito,2); ?></td>
          	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
              <strong>Nota de C:</strong> <br />B/ <?php echo number_format($total_notadc,2); ?></td>
          	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <strong>Total:</strong> <br />B/ <?php echo number_format($total_total,2); ?></td>
          	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <strong>Deuda:</strong> <br />B/ <?php echo number_format($total_deficit,2); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </tfoot>
</table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_back" class="btn btn-warning">Volver</button>
</div>
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