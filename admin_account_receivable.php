<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';
//echo $_SESSION['admin'];
?>
<?php
$qry_cliente=mysql_query("SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS suma, bh_facturaf.TX_facturaf_deficit FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre ASC LIMIT 10");
$rs_cliente=mysql_fetch_assoc($qry_cliente);
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

$(window).on('beforeunload',function(){
	close_popup();
});

$("#btn_back").click(function(){
	window.history.back(1);
//	window.location='start_admin.php';
});

$("#txt_filterclient").keyup(function(){
	filter_adminclient(this.value);
})

$("#btn_print").on("click",function(){
	print_html("print_account_receivable.php");
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
<form action="login.php" method="post" name="form_login"  id="form_login">
<div id="container_txtfilterfacturaf" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
    <label for="txt_filterclient">Buscar</label>
    <input type="text" id="txt_filterclient" class="form-control" />
</div>
<div id="container_rlimit"  class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
    <label for="r_limit">Mostrar:</label><br />
<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="10" checked="checked">10</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_50" value="50">50</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_100" value="100">100</label>
    <label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="">Todas</label>
</div>
<div id="container_tblclient" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_client" class="table table-bordered table-condensed table-striped">
    <thead class="bg-primary">
        <tr>
            <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Nombre</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">RUC</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Deuda</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Telefono</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
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

    <tbody>

    <?php
	if($nr_cliente=mysql_num_rows($qry_cliente) > 0){
	do{ ?>
    	<tr>
            <td><?php echo $rs_cliente['TX_cliente_nombre']; ?></td>
            <td><?php echo $rs_cliente['TX_cliente_cif']; ?></td>
            <td><?php echo number_format($rs_cliente['suma'],2); ?></td>
            <td><?php echo $rs_cliente['TX_cliente_telefono']; ?></td>
            <td>
            <?php
			if($rs_cliente['suma'] > 0){
			?>
            <button type="button" id="btn_openaccount" name="<?php echo $rs_cliente['AI_cliente_id']; ?>" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_client_account.php?a='+this.name,'client_account','1000','420');">CTS. P/COBRAR</button>
			<?php
			}
			?>
            </td>
            <td>
            <button type="button" id="btn_enablecredit" name="<?php echo $rs_cliente['AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_popup_w_scroll('popup_client_credit.php?a='+this.name,'client_credit','1000','420');">MOVIMIENTOS</button>
            </td>
    	</tr>
    <?php }while($rs_cliente=mysql_fetch_assoc($qry_cliente));
	}else{
	?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php
	}
	 ?>
    </tbody>
    </table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_back" class="btn btn-warning">Volver</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_print" class="btn btn-info">Imprimir Cts/Cobrar</button>
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
