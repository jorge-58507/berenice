<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');
?>
<?php
require 'attached/php/req_login_admin.php';
?>
<?php
$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_user.TX_user_seudonimo
FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id  AND TX_facturaventa_status != 'CANCELADA'";
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'INACTIVA' AND bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
}
$qry_facturaventa=$link->query($txt_facturaventa);
$rs_facturaventa=$qry_facturaventa->fetch_array();

$fecha_limite=strtotime('-8 weeks');
$date_limit = date('Y-m-d',$fecha_limite);
$qry_old_quotation=$link->query("SELECT TX_facturaventa_fecha FROM bh_facturaventa WHERE TX_facturaventa_status != 'CANCELADA' AND TX_facturaventa_status != 'INACTIVA' AND TX_facturaventa_fecha < '$date_limit'");
$nr_old_quotation=$qry_old_quotation->fetch_array();
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
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min.js"></script>
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

$("#txt_filterfacturaventa").on("keyup",function(){
	filter_adminfacturaventa(this.value);
});
$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
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
    	<div id="container_username" class="col-lg-4 visible-lg">
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
<form action="sale.php" method="post" name="form_sell"  id="form_sell">
	<div id="container_alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php if($nr_old_quotation > 0){ ?>
        <div class="alert alert-danger alert-dismissable fade in">
          <a href="#" onclick="old_quotation('<?php echo $date_limit; ?>');" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Ateci&oacute;n!</strong> Hay cotizaciones activas con mas de 8 semanas.
        </div>
<?php } ?>
	</div>

<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_filterfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="container_txtfilterfacturaventa" class="col-xs-12 col-sm-8 col-md-5 col-lg-5">
            <label class="label label_blue_sky" for="txt_filterfacturaventa">Buscar</label>
            <input type="text" id="txt_filterfacturaventa" class="form-control" />
        </div>
		<div id="container_selfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <label class="label label_blue_sky" for="sel_status">Status</label>
            <select id="sel_status" class="form-control">
            	<option value="">TODAS</option>
            	<option value="ACTIVA">ACTIVA</option>
            	<option value="INACTIVA">INACTIVA</option>
            	<option value="FACTURADA">FACTURADA</option>
            </select>
        </div>
        <div id="container_txtfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
            <label class="label label_blue_sky" for="txt_date">Fecha <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs" onclick="setEmpty('txt_date')"><strong>!</strong></button></label>
            <input type="text" id="txt_date" class="form-control" readonly="readonly" />
        </div>

    </div>
    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		<table id="tbl_facturaventa" class="table table-bordered table-striped">
			<thead>
		  	<tr>
		    	<th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
		    	<th class="bg-primary col-xs-3 col-sm-3 col-md-3 col-lg-3">Vendedor</th>
		      <th class="bg-primary col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
		      <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-2">Nº Factura</th>
		      <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
		      <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
		      <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
		    </tr>
	    </thead>
	    <tfoot>
	    	<tr>
	      	<td class="bg-primary" colspan="7"> </td>
				</tr>
	    </tfoot>
    <tbody>
    <?php if($nr_facturaventa=$qry_facturaventa->num_rows > 0){
	do{
	?>
    <tr>
        <td><?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date; ?></td>
        <td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
        <td>
        <?php
		switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":
				$font='#00CC00';
				break;
			case "FACTURADA":
				$font='#0033FF';
				break;
			default:
				$font='#990000';
		}
		?>
        <font color="<?php echo $font ?>" style="font-weight:bold">
		<?php echo $rs_facturaventa['TX_facturaventa_status']; ?>
        </font>
        </td>
        <td>
        <?php if($rs_facturaventa['TX_facturaventa_status'] != 'CANCELADA'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php } ?>
        </td>
    </tr>
    <?php
	}while($rs_facturaventa=$qry_facturaventa->fetch_array());
    ?>
    <?php }else{ ?>
    <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
    </tbody>
</table>

	</div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_back" class="btn btn-warning">Volver</button>
</div>
</form>
</div>


<div id="footer">
	<?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
<script type="text/javascript">
	ScrollReveal().reveal('#tbl_product tbody tr', {interval: 100});
	<?php include 'attached/php/req_footer_js.php'; ?>
</script>

</body>
</html>
