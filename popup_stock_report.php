<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
$qry_reporte=mysql_query("SELECT bh_reporte.AI_reporte_id, bh_reporte.TX_reporte_value, bh_reporte.TX_reporte_fecha, bh_reporte.TX_reporte_status, bh_user.TX_user_seudonimo FROM (bh_reporte INNER JOIN bh_user ON bh_user.AI_user_id = bh_reporte.reporte_AI_user_id) WHERE bh_reporte.TX_reporte_tipo = 'INVENTARIO' AND bh_reporte.TX_reporte_status = 'ACTIVA'");
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
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	$('#btn_cancel').click(function(){
		self.close();
	});
	
});


</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
 
    <div id="container_tblreport" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

	<table id="tbl_report" class="table table-bordered table-condensed table-hover">
    <thead>
    <tr class="bg-primary">
    	<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Contenido</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Usuario</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Status</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"> </th>
    </tr>
    </thead>
    <tbody>
    <?php
	if($nr_reporte=mysql_num_rows($qry_reporte) > 0){
	 while($rs_reporte=mysql_fetch_assoc($qry_reporte)){ ?>
    <tr>
    	<td><?php echo $rs_reporte['TX_reporte_value']; ?></td>
        <td><?php echo $rs_reporte['TX_reporte_fecha']; ?></td>
        <td><?php echo $rs_reporte['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_reporte['TX_reporte_status']; ?></td>
        <td><button type="button" id="btn_process" name="<?php echo $rs_reporte['AI_reporte_id']; ?>" class="btn btn-success btn-sm" onclick="upd_report(this.name)"><i class="fa fa-check" aria-hidden="true"></i></button></td>
    </tr>
    <?php } 
	}else{?>
    <tr>
    	<td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary"><tr><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
	</table>
    </div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
    </div>


</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
