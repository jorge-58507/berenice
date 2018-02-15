<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';
?>
<?php
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

$("#btn_nuevanc").click(function(){
	window.location='new_nc.php';
});

$("#btn_back").click(function(){
	window.history.back(1);
	// window.location='start_admin.php';
});

$("#txt_filterfacturaf").keyup(function(){
	filter_adminfacturaf(this.value);
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
<form action="login.php" method="post" name="form_login"  id="form_login">
<div id="container_btn_nuevanc" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php // <button type="button" id="btn_nuevanc" class="btn btn-warning btn-lg">Nueva Nota de Cr&eacute;dito</button>
?>
</div>
<div id="container_txtfilterfacturaf" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_filterfacturaf">Buscar</label>
    <input type="text" id="txt_filterfacturaf" class="form-control" />
</div>
<div id="container_txtdatei"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    <label for="txt_date_initial">Fecha
    <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs" onclick="setEmpty('txt_date_i')"><strong>!</strong></button>
    </label>
    <input type="text" id="txt_date_initial" name="txt_date_initial" class="form-control" readonly="readonly" />
</div>
<div id="container_txtdatef"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    <label for="txt_date_final">Fecha
    <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs" onclick="setEmpty('txt_date_f')"><strong>!</strong></button>
    </label>
    <input type="text" id="txt_date_final" name="txt_date_final" class="form-control" readonly="readonly" />
</div>
<div id="container_txtdate"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_date">Mostrar:</label><br />
<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="10" checked="checked">10</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_50" value="50">50</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="100">100</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="">Todas</label>
</div>
<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
    <thead class="bg-primary">
        <tr>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-3">Nombre</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Fecha</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Hora</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Total</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Deficit</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Vendedor</th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"></th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"><i id="filter_by_deficit" class="fa fa-angle-double-down" onclick="filter_adminfacturaf('deficit');"></i></th>
            <th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"></th>
        </tr>
    </thead>

    <tbody>
    <?php
	$total_total=0; $total_deficit=0;
	$total_efectivo=0; $total_tarjeta_dc=0; $total_tarjeta_dd=0; $total_cheque=0; $total_credito=0; $total_notadc=0;

 	if($nr_facturaf=$qry_facturaf->num_rows > 0){
		do{
		$total_total += $rs_facturaf['TX_facturaf_total'];
		$total_deficit += $rs_facturaf['TX_facturaf_deficit'];
?>
		<tr onclick="toggle_tr('tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>')">
      <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
      <td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
      <td><?php
			$pre_fecha=strtotime($rs_facturaf['TX_facturaf_fecha']);
			echo $fecha=date('d-m-Y',$pre_fecha);
			?></td>
      <td><?php echo $rs_facturaf['TX_facturaf_hora']; ?></td>
      <td><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></td>
      <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
      <td><?php
				$qry_vendor = $link->query("SELECT bh_user.TX_user_seudonimo FROM ((bh_facturaf
					INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
					INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
					WHERE bh_facturaf.AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'")or die($link->error);
				$rs_vendor = $qry_vendor->fetch_array();
				echo $rs_vendor['TX_user_seudonimo'];
			?></td>
      <td>
      <button type="button" id="btn_openff" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_watchfacturaf.php?a='+this.name,'watch_facturaf','950','547');">VER</button>
      </td>
      <td>
<?php 	if($rs_facturaf['TX_facturaf_deficit'] != '0'){ ?>
						 <!-- <button type="button" id="btn_opennewdebit" name="<?php echo $rs_facturaf['facturaf_AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_popup_w_scroll('popup_newdebit.php?a='+this.name,'newdebit','425','420');">DEBITAR</button> -->
						<button type="button" id="btn_opennewdebit" name="<?php echo $rs_facturaf['facturaf_AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_newdebit(this.name);">DEBITAR</button>
<?php		}	?>
      </td>
      <td>
      <button type="button" id="btn_makenc" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-warning btn-sm" onclick="make_nc(this.name);">N.C.</button>
      </td>
  	</tr>
    <tr id="tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>" style="display:none;">
      <td colspan="10" style="padding:0;">
      <table id="tbl_payment" class="table table-condensed table_no_margin table-bordered" style="margin:0;">
      <tr>
			<?php $qry_payment=$link->query("SELECT bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value FROM ((bh_datopago INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id) INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE bh_facturaf.AI_facturaf_id = '{$rs_facturaf['AI_facturaf_id']}'");
			$raw_payment=array();
			$i=0;
while($rs_payment=$qry_payment->fetch_array()){
	if($rs_payment['datopago_AI_metododepago_id']=='1'){
		$total_efectivo += $rs_payment['TX_datopago_monto'];
	}
	if($rs_payment['datopago_AI_metododepago_id']=='2'){
		$total_cheque += $rs_payment['TX_datopago_monto'];
	}
	if($rs_payment['datopago_AI_metododepago_id']=='3'){
		$total_tarjeta_dc += $rs_payment['TX_datopago_monto'];
	}
	if($rs_payment['datopago_AI_metododepago_id']=='4'){
		$total_tarjeta_dd += $rs_payment['TX_datopago_monto'];
	}
	if($rs_payment['datopago_AI_metododepago_id']=='5'){
		$total_credito += $rs_payment['TX_datopago_monto'];
	}
	if($rs_payment['datopago_AI_metododepago_id']=='7'){
		$total_notadc += $rs_payment['TX_datopago_monto'];
	}
	$raw_payment[$rs_payment['datopago_AI_metododepago_id']]=$rs_payment['TX_datopago_monto'];
	$i++;
	}?>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[1])){ echo "<strong>Efectivo:</strong> ".number_format($raw_payment[1],2); }	?>
            </td>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[2])){ echo "<strong>Cheque:</strong> ".number_format($raw_payment[2],2); }	?>
            </td>
						<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[3])){ echo "<strong>TDC:</strong> ".number_format($raw_payment[3],2); }	?>
            </td>
						<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[4])){ echo "<strong>TDD:</strong> ".number_format($raw_payment[4],2); }	?>
            </td>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[5])){ echo "<strong>Cr&eacute;dito:</strong> ".number_format($raw_payment[5],2); }	?>
            </td>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php			if(isset($raw_payment[7])){ echo "<strong>Nota de C.:</strong> ".number_format($raw_payment[7],2); }	?>
            </td>

            </tr>
            </table>
            </td>
        </tr>
    <?php }while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
    <?php }else{ ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary">
        <tr>
            <td colspan="10">
            <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
            <tr>
            	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>Efectivo:</strong> <br /><?php echo number_format($total_efectivo,2); ?></td>
							<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>Cheque:</strong> <br /><?php echo number_format($total_cheque,2); ?></td>
            	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>TDC:</strong> <br /><?php echo number_format($total_tarjeta_dc,2); ?></td>
							<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>TDD:</strong> <br /><?php echo number_format($total_tarjeta_dd,2); ?></td>
            	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>Cr&eacute;dito:</strong> <br /><?php echo number_format($total_credito,2); ?></td>
            	<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <strong>Nota de Cr&eacute;dito:</strong> <br /><?php echo number_format($total_notadc,2); ?></td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <strong>Total:</strong> <br /><?php echo number_format($total_total,2); ?></td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <strong>Deuda:</strong> <br /><?php echo number_format($total_deficit,2); ?></td>
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
