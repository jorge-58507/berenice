<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';

$arqueo_id=$_GET['a'];

$qry_metododepago = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
$raw_metododepago=array();
while ($rs_metododepago=$qry_metododepago->fetch_array()) {
	$raw_metododepago[$rs_metododepago['AI_metododepago_id']] = $rs_metododepago['TX_metododepago_value'];
}

$qry_arqueo_facturaf = $link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_nombre
	FROM (((bh_facturaf
		INNER JOIN bh_arqueo ON bh_arqueo.AI_arqueo_id = bh_facturaf.facturaf_AI_arqueo_id)
		INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id)
		WHERE bh_arqueo.AI_arqueo_id = '$arqueo_id' ORDER BY TX_facturaf_fecha DESC, AI_facturaf_id DESC")or die($link->error);

$prep_payment = $link->prepare("SELECT AI_datopago_id, TX_datopago_monto, datopago_AI_metododepago_id FROM bh_datopago WHERE datopago_AI_facturaf_id = ?")or die($link->error);

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

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$(window).on('beforeunload',function(){
	close_popup();
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
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form name="form_new_nc" id="form_new_nc" action="ins_newnc.php" method="post">

<div id="container_cashregister_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_cashregister_facturaf"class="table table-condensed table-bordered">
		<caption class="caption">Facturas Incluidas</caption>
		<thead class="bg-primary">
			<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nombre</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Hora</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Deficit</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Vendedor</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Met. de Pago</th>
			</tr>
		</thead>
		<tfoot class="bg-primary">
			<tr>
				<td colspan="8"></td>
			</tr>
		</tfoot>
		<tbody><?php
			while ($rs_arqueo_facturaf = $qry_arqueo_facturaf->fetch_array(MYSQLI_ASSOC)) {  ?>
				<tr>
					<td class="al_center"><?php echo $rs_arqueo_facturaf['TX_facturaf_numero'] ?></td>
					<td><?php echo $rs_arqueo_facturaf['TX_cliente_nombre'] ?></td>
					<td><?php echo date('d-m-Y', strtotime($rs_arqueo_facturaf['TX_facturaf_fecha'])); ?></td>
					<td><?php echo $rs_arqueo_facturaf['TX_facturaf_hora'] ?></td>
					<td class="al_center">B/ <?php echo number_format($rs_arqueo_facturaf['TX_facturaf_total'],2) ?></td>
					<td class="al_center">B/ <?php echo number_format($rs_arqueo_facturaf['TX_facturaf_deficit'],2) ?></td>
					<td class="al_center"><?php echo $rs_arqueo_facturaf['TX_user_seudonimo'] ?></td>
<?php 		$prep_payment->bind_param('i', $rs_arqueo_facturaf['AI_facturaf_id']); $prep_payment->execute(); $qry_payment=$prep_payment->get_result();
					$str_payment = '';
					while($rs_payment=$qry_payment->fetch_array(MYSQLI_ASSOC)){
						$str_payment .= "<strong>".$raw_metododepago[$rs_payment['datopago_AI_metododepago_id']]."</strong>: B/ ".number_format($rs_payment['TX_datopago_monto'],2)."<br />";
					}; ?>
					<td><?php echo $str_payment; ?></td>
				</tr>
<?php } ?>
		</tbody>
	</table>
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
