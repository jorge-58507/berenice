<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cuentas por Cobrar</title>
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">
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
<tr style="height:85px" align="right">
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
<tr style="height:21px" align="center">
	<td valign="top" colspan="10">
    </td>
</tr>
<tr style="height:48px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
        <tr>
        	<td valign="top" style="text-align:center;">
			<h4>CUENTAS POR COBRAR</h4><br />
            </td>
        </tr>
    </table>    
    </td>
</tr>
<tr style="height:736px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_notadebito" class="table table-striped table-bordered">
    <thead style="border:solid; background-color:#DDDDDD">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<strong>NOMBRE</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>RUC</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>TELEFONO</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>LIMITE</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>PLAZO</strong>
            </th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <strong>SALDO</strong>
            </th>
		</tr>
	</thead>
    <tfoot></tfoot>
    <tbody>
	    <?php
$txt_facturaf="SELECT SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, bh_facturaf.facturaf_AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, 
bh_cliente.TX_cliente_limitecredito, bh_cliente.TX_cliente_plazocredito
FROM (((bh_facturaf 
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
WHERE TX_facturaf_deficit > 0 GROUP BY facturaf_AI_cliente_id";
$qry_facturaf=mysql_query($txt_facturaf) or die(mysql_error());
			$total=0;
			$saldo=0;
		?>
        <?php while($rs_facturaf=mysql_fetch_array($qry_facturaf)){
			$saldo+=round($rs_facturaf['deficit'],2);
		?>
    	<tr style="height:30px;">
        	<td>
				<?php echo $rs_facturaf['TX_cliente_nombre']; ?>
            </td>
            <td>
				<?php echo $rs_facturaf['TX_cliente_cif']; ?>
            </td>
        	<td>
				<?php echo $rs_facturaf['TX_cliente_telefono']; ?>
            </td>
            <td>
				<?php echo "B/ ".number_format($rs_facturaf['TX_cliente_limitecredito'],2); ?>
            </td>
        	<td>
				<?php echo $rs_facturaf['TX_cliente_plazocredito']." Semanas"; ?>
            </td>
            <td>
				<?php echo number_format($rs_facturaf['deficit'],2); ?>
            </td>
		</tr>
        <?php }; ?>
 	</tbody>
    <tfoot style="border:solid; background-color:#DDDDDD; text-align: left;">
    <tr>
    <td></td><td></td><td></td><td></td><td></td>
    <td>
    	<strong>TOTAL:</strong><br />
		<?php 
		echo "B/ ".number_format($saldo,2);	
		?>
    
    </td>
    </tr>
    </tfoot>
	</table>
    </td>
</tr>
</table>
</body>
</html>