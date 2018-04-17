<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$qry_seller=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user");
$rs_seller=$qry_seller->fetch_array();
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
  $("#clear_date_initial").click(function(){
	 $("#txt_date_initial").val("");
  });
  $("#clear_date_final").click(function(){
	 $("#txt_date_final").val("");
  });
	$("#btn_search").click(function(){
		if($("#sel_vendedor").val() == ""){
			$("#sel_vendedor").addClass("input_invalid");
			return false;
		}	$("#sel_vendedor").removeClass("input_invalid");
		if($("#txt_date_initial").val() == ""){
			$("#txt_date_initial").addClass("input_invalid");
			return false;
		}	$("#txt_date_initial").removeClass("input_invalid");
		if($("#txt_date_final").val() == ""){
			$("#txt_date_final").addClass("input_invalid");
			return false;
		}	$("#txt_date_final").removeClass("input_invalid");

		filter_facturaventa_vendedor('datopago_AI_metododepago_id');
	});


$("#btn_back").click(function(){
	history.back(1);
});

});


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
<form action="" method="post" name="form_sell"  id="form_sell">
<div id="container_filter" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_selvendedor" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <label for="txt_sel_vendedor">Vendedor</label>
        <select id="sel_vendedor" class="form-control">
            <option value=""></option>
<?php	do{ ?>
		<option value="<?php echo $rs_seller['AI_user_id']; ?>"><?php echo $rs_seller['TX_user_seudonimo']; ?></option>
<?php	}while($rs_seller=$qry_seller->fetch_array()); ?>
		</select>
	</div>
	<div id="container_txtdateinitial" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="txt_date_initial">F. Inicio
        <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs"><strong>!</strong></button></label>
        <input type="text" id="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo date('d-m-Y',strtotime('-1 week')); ?>" />
    </div>
    <div id="container_filterfacturaventa" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="txt_date_initial">F. Final
        <button type="button" id="clear_date_final" class="btn btn-danger btn-xs"><strong>!</strong></button></label>
        <input type="text" id="txt_date_final" class="form-control" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
    </div>
    <div id="container_btnsearch" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    	<button type="button" id="btn_search" class="btn btn-success">Buscar</button>
    	&nbsp;&nbsp;
        <button type="button" id="btn_back" class="btn btn-warning">Volver</button>
    </div>

</div>
<div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_facturaventa" class="table table-bordered table-condensed table-striped">
    <thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Cotización</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Factura F. Asociada</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Metodo de P.</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total de P.</th>
        </tr>
    </thead>
    <tbody>
    <tr>
		<td></td>
        <td></td>
		<td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
    <tfoot class="bg-primary">
    <tr>
    	<td></td>
        <td></td>
    	<td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
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
