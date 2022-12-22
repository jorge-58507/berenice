<?php
require '../../../bh_conexion.php';
$link=conexion();
include '../method_crud.php';
$method_crud = new method_crud();
require '../../php/req_login_sale.php';

$name_tool = $method_crud->get_name_tool();
$json_contenido = $method_crud->read_json_tool($name_tool);
$raw_contenido=json_decode($json_contenido, true);
$raw_data = $raw_contenido['saved'][$_GET['a']];

$qry_opcion = $link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$qry_user = $link->query("SELECT AI_user_id,TX_user_seudonimo FROM bh_user")or die($link->error);
$raw_user = array();
while ($rs_user = $qry_user->fetch_array(MYSQLI_ASSOC)) {
	$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Orden de Reducci&oacute;n</title>
<link href="../../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="../../css/print_css.css" rel="stylesheet" type="text/css">
</head>
<body style="font-family:Arial" onLoad="window.print()">

<?php
$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$fecha = $dias[date('N', strtotime(date('Y-m-d')))+1];
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
<tr style="height:135px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="75px" src="../../image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
    <?php
		$date=date('d-m-Y');
	?>
<?php echo $fecha."&nbsp;-&nbsp;"; ?><?php echo $date; ?>
    </td>
</tr>
<tr style="height:108px">
	<td valign="top" colspan="10">
		<table id="tbl_titulo" class="table table-print">
		<tbody>
		<tr>
			<td class="al_center">    <h3>Orden de Reducci&oacute;n y/o Recomposici&oacute;n</h3>	</td>
		</tr>
		</tbody>
		</table>
		<table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
  	<tr>
      <td valign="top" style="text-align: right;">
				<strong>Fecha: </strong><?php echo date('d-m-Y',strtotime($raw_data['fecha'])); ?>
			</td>
  	</tr>
		</table>
  </td>
</tr>
<tr style="height:638px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_minus" class="table table-bordered table-print">
			<caption>Productos a Reducir</caption>
			<thead style="border:solid; background-color:#DDDDDD;">
      	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
          <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripci&oacute;n</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
    		</tr>
			</thead>
      <tbody>
<?php 	foreach ($raw_data['minus'] as $key => $value) {
					$crud_function = new method_crud(); $public_bd = new public_access_bd();
					$raw_columna=["TX_producto_value","TX_producto_codigo"]; $raw_where = ["AI_producto_id" => $value['producto_id']];
					$rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
?>				<tr>
						<td><?php echo $rs_producto['TX_producto_codigo']; ?></td>
						<td><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']); ?></td>
						<td><?php echo $value['cantidad']; ?></td>
					</tr>
<?php 	}
		?>
      </tbody>
			<tfoot style="border:solid; background-color:#DDDDDD;">
			<tr>
				<td colspan="4"></td>
			</tr>
			</tfoot>
		</table>
		<table id="tbl_minus" class="table table-bordered table-print">
			<caption>Productos a Incrementar</caption>
			<thead style="border:solid; background-color:#DDDDDD;">
      	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
          <th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripci&oacute;n</th>
          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
    		</tr>
			</thead>
      <tbody>
<?php 	foreach ($raw_data['plus'] as $key => $value) {
					$crud_function = new method_crud(); $public_bd = new public_access_bd();
					$raw_columna=["TX_producto_value","TX_producto_codigo"]; $raw_where = ["AI_producto_id" => $value['producto_id']];
					$rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
?>				<tr>
						<td><?php echo $rs_producto['TX_producto_codigo']; ?></td>
						<td><?php echo $r_function->replace_special_character($rs_producto['TX_producto_value']); ?></td>
						<td><?php echo $value['cantidad']; ?></td>
					</tr>
<?php 	}
		?>
      </tbody>
			<tfoot style="border:solid; background-color:#DDDDDD;">
			<tr>
				<td colspan="4"></td>
			</tr>
			</tfoot>
		</table>
	</td>
</tr>
<tr style="height:66px;">
	<td colspan="10">
	<table id="tbl_autorized" class="table table-print">
	<tbody>
	<tr>
		<td>
		<p>
			__________________________
			<br />Autorizado por:<br />
			<?php echo $raw_user[$raw_data['user_id']]; ?>
		</p>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
</tr>

</table>
</body>
</html>
