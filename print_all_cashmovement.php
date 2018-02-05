<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$fecha=date('Y-m-d',strtotime($_GET['a']));

$txt_cajamenuda="SELECT bh_efectivo.AI_efectivo_id, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_efectivo.TX_efectivo_fecha,
bh_efectivo.TX_efectivo_status, bh_user.TX_user_seudonimo
FROM (bh_efectivo INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE bh_efectivo.TX_efectivo_fecha = '$fecha' ORDER BY TX_efectivo_tipo ASC";
$qry_cajamenuda=$link->query($txt_cajamenuda)or die($link->error);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Caja Menuda - <?php echo $_GET['a']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">

</script>

<body style="font-family:Arial" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto">
<tr style="height:6px">
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
</tr>
<tr style="height:132px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="75px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo $fecha; ?>
    </td>
</tr>
<tr style="height:34px">
	<td valign="top" colspan="10"  style="text-align:center">
		<h4>Caja Menuda - <?php echo $_GET['a']; ?></h4><br />
  </td>
</tr>
<tr style="height:781px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_notadebito" class="table table-striped table-bordered">
    <thead style="border:solid; background-color:#DDDDDD">
    	<tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>FECHA</strong>
				</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>USUARIO</strong>
				</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<strong>NUMERO</strong>
				</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
					<strong>TIPO</strong>
				</th>
				<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<strong>MOTIVO</strong>
				</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<strong>MONTO</strong>
				</th>
			</tr>
		</thead>
    <tbody>
<?php
$index = 1;
$pager = 0;
		while ($rs_cajamenuda=$qry_cajamenuda->fetch_array()) {
			$pager++;
			if($index === 1){
				if($pager === 20){
					$pager = 0;
					$index++;
?>
					</tbody>
					</table>
				</td>
			</tr>
			<tr style="height:781px;">
				<td valign="top" colspan="10" style="padding-top:2px;">
					<table id="tbl_notadebito" class="table table-striped table-bordered">
						<thead style="border:solid; background-color:#DDDDDD">
							<tr>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>FECHA</strong>
								</th>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>USUARIO</strong>
								</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<strong>NUMERO</strong>
								</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<strong>TIPO</strong>
								</th>
								<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
									<strong>MOTIVO</strong>
								</th>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									<strong>MONTO</strong>
								</th>
							</tr>
						</thead>
					<tbody>
<?php
}
}else{
if($pager === 26){
$pager = 0;
$index++;
 ?>
				</tbody>
				</table>
			</td>
		</tr>
		<tr style="height:781px;">
			<td valign="top" colspan="10" style="padding-top:2px;">
				<table id="tbl_notadebito" class="table table-striped table-bordered">
					<thead style="border:solid; background-color:#DDDDDD">
						<tr>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>FECHA</strong>
							</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>USUARIO</strong>
							</th>
							<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								<strong>NUMERO</strong>
							</th>
							<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								<strong>TIPO</strong>
							</th>
							<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<strong>MOTIVO</strong>
							</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<strong>MONTO</strong>
							</th>
						</tr>
					</thead>
				<tbody>
<?php
					}
				}
?>
				<tr style="height:30px;">
					<td><?php echo $rs_cajamenuda['TX_efectivo_fecha']; ?></td>
					<td><?php echo substr($rs_cajamenuda['TX_user_seudonimo'],0,11); ?></td>
					<td><?php echo $rs_cajamenuda['AI_efectivo_id']; ?></td>
					<td><?php echo $rs_cajamenuda['TX_efectivo_tipo']; ?></td>
					<td><?php echo substr($rs_cajamenuda['TX_efectivo_motivo'],0,45); ?></td>
					<td><?php echo $rs_cajamenuda['TX_efectivo_monto']; ?></td>
				</tr>
<?php
			}
?>
 		</tbody>
	</table>

    </td>
</tr>
</table>
</body>
</html>
